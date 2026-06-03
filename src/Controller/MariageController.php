<?php

namespace App\Controller;

use App\Entity\Couple;
use App\Entity\Mariage;
use App\Form\EditmariageType;
use App\Form\MariageType;
use App\Repository\FideleRepository;
use App\Repository\MariageRepository;
use App\Traits\ClientIp;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function Sodium\bin2hex;

#[Route('/mariage')]
class MariageController extends AbstractController {

    use ClientIp;

    #[Route('/', name: 'mariage_index', methods: ['GET'])]
    public function index(MariageRepository $mariageRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_CONJUGAL')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
//        $user = $this->getUser();
//        $roles = $user->getRoles();
        $mariage = $mariageRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        return $this->render('mariage/index.html.twig', [
                    'mariages' => $mariage,
        ]);
    }

    #[Route('/new', name: 'mariage_new', methods: ['GET', 'POST'])]
    public function new(Request $request, string $photoDir = null, FideleRepository $fideleRepository, MariageRepository $mariageRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_CONJUGAL')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $mariage = new Mariage();
        $entityManager = $this->getDoctrine()->getManager();

        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $epouxmembre = $fideleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL, "etatfidele" => '1', "sexe" => 'Homme', "etatmariage" => 0]);
        $epousemembre = $fideleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL, "etatfidele" => '1', "sexe" => 'Femme', "etatmariage" => 0]);
//        $fidele = $fideleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL,  "statutmatri" => 'Célibataire']);
//        $fidele = $fideleRepository->findBy(['eglise' => $eglise, "etatfidele" => 1]);

        $form = $this->createForm(MariageType::class, $mariage, ['epouxmembre' => $epouxmembre, 'epousemembre' => $epousemembre],);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $datemariage1 = $form['datemariage']->getData();
            $lieumariage1 = $form['lieumariage']->getData();
            $pasteurmariage1 = $form['pasteurmariage']->getData();
            $pouse = $form['epouse']->getData();
            $epoux = $form['epoux']->getData();
            // Type regime, un champ non mappé du formulaire Mariage afin de preciser le choix
            // 'Fiancé membre' => '1',
            //  'Fiancée membre' => '2',
            //   'Les 2 membres' => '3',
            //'Aucun membre' => '4',
            $regime = $form['typeregime']->getData();

            if ($regime == 1) {
                $epouxMembre = $form['epouxmembre']->getData()->getId() or null;
                $epouxmem = $fideleRepository->findOneByFidele($epouxMembre);
                $epouxmem->setDatemariage($datemariage1);
                $epouxmem->setLieumariage($lieumariage1);
                $epouxmem->setNommariage($pouse);
                $epouxmem->setPasteurmariage($pasteurmariage1);
                $epouxmem->setStatutmatri("Marié(e)");
                $epouxmem->setEtatmariage(1);
            }

            if ($regime == 2) {
                $epouseMembre = $form['epousemembre']->getData()->getId() or null;
                $epouse = $fideleRepository->findOneByFidele($epouseMembre);
                $epouse->setDatemariage($datemariage1);
                $epouse->setLieumariage($lieumariage1);
                $epouse->setPasteurmariage($pasteurmariage1);
                $epouse->setNommariage($epoux);
                $epouse->setStatutmatri("Marié(e)");
                $epouse->setEtatmariage(1);
            }

            if ($regime == 3) {
                $epouseMembre = $form['epousemembre']->getData()->getId() or null;
                $epouxMembre = $form['epouxmembre']->getData()->getId() or null;
                $epouse = $fideleRepository->findOneByFidele($epouseMembre);
                $epouse->setDatemariage($datemariage1);
                $epouse->setLieumariage($lieumariage1);
                $epouse->setPasteurmariage($pasteurmariage1);
                $epouse->setStatutmatri("Marié(e)");
                $epouse->setEtatmariage(1);

                $epouxmem = $fideleRepository->findOneByFidele($epouxMembre);
                $epouxmem->setDatemariage($datemariage1);
                $epouxmem->setLieumariage($lieumariage1);
                $epouxmem->setPasteurmariage($pasteurmariage1);
                $epouxmem->setStatutmatri("Marié(e)");
                $epouxmem->setEtatmariage(1);

                $detail2 = new Couple();
                $detail2->setEpouse($epouse);
                $detail2->setEpoux($epouxmem);
                $detail2->setEglise($eglise);
                $detail2->setCreatedBy($user);
                $detail2->setCreatedFromIp($this->GetIp());
                $entityManager->persist($detail2);
            }

            if ($regime == 4) {
                
            }

            $mariage->setCreatedFromIp($this->GetIp());
            //            Insertion image de profile
            if ($photo = $form['photo']->getData()) {
                $filename = bin2hex(random_bytes(6)) . '.' . $photo->guessExtension();
                try {
                    $photo->move($photoDir, $filename);
                } catch (FileException $e) {
                    // unable to upload the photo, give up
                }
                $mariage->setPhotoFile($filename);
            }


            $mariage->setCreatedBy($user);
            $mariage->setEglise($eglise);
            $entityManager->persist($mariage);
            $entityManager->flush();

            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'mariage_new' : 'mariage_index';
            if ($nextAction) {
                $this->addFlash('success', 'Enregistrement avec succès.');
            }

            return $this->redirectToRoute($nextAction);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('mariage/new.html.twig', [
                    'mariage' => $mariage,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    #[Route('/{id}/show', name: 'mariage_show', methods: ['GET', 'POST'])]
    public function show(Mariage $mariage): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_CONJUGAL')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        return $this->render('mariage/show.html.twig', [
                    'mariage' => $mariage,
        ]);
    }

    #[Route('/{id}/edit', name: 'mariage_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Mariage $mariage, string $photoDir = null, FideleRepository $fideleRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_CONJUGAL')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
       
        $user = $this->getUser();

        $form = $this->createForm(EditmariageType::class, $mariage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $mariage->setUpdatedFromIp($this->GetIp());
            $user = $this->getUser();
            $mariage->setUpdatedBy($user);
            //            Insertion image de profile
            if ($photo = $form['photo']->getData()) {
                $filename = bin2hex(random_bytes(6)) . '.' . $photo->guessExtension();
                try {
                    $photo->move($photoDir, $filename);
                } catch (FileException $e) {
                    // unable to upload the photo, give up
                }
                $mariage->setPhotoFile($filename);
            }
                            $this->addFlash('success', 'Modification avec succès.');

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('mariage_index');
        }

        return $this->render('mariage/edit.html.twig', [
                    'mariage' => $mariage,
                    'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'mariage_delete', methods: ['POST'])]
    public function delete(Request $request, Mariage $mariage): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_CONJUGAL')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($this->isCsrfTokenValid('delete' . $mariage->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();

            $mariage->setDeletedFromIp($this->GetIp());
            $mariage->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $mariage->setDeletedBy($user);
             $this->addFlash('danger', 'Suppression effectuée avec succès.');
            $entityManager->flush();
        }

        return $this->redirectToRoute('mariage');
    }

}
