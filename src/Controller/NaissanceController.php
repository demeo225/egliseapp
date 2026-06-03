<?php

namespace App\Controller;

use App\Entity\Naissance;
use App\Form\NaissanceType;
use App\Repository\FideleRepository;
use App\Repository\NaissanceRepository;
use App\Traits\ClientIp;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function Sodium\bin2hex;

#[Route('/naissance')]
class NaissanceController extends AbstractController {

    use ClientIp;

    #[Route('/', name: 'naissance_index', methods: ['GET'])]
    public function index(NaissanceRepository $naissanceRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_CONJUGAL')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $naissance = $naissanceRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        return $this->render('naissance/index.html.twig', [
                    'naissances' => $naissance,
        ]);
    }

    #[Route('/{id}/edit', name: 'naissance_edit', methods: ['GET', 'POST'])]
    #[Route('/new', name: 'naissance_new', methods: ['GET', 'POST'])]
    public function new(EntityManagerInterface $entityManager, Request $request, string $photoDir = null, FideleRepository $fideleRepository, ?Naissance $naissance = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_CONJUGAL')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $user = $this->getUser();
        $type = $naissance === null ? 'new' : 'edit';
        $naissance = $naissance === null ? new Naissance() : $naissance;
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $perenaisse = $fideleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL, "sexe" => 'Homme']);
        $merenaisse = $fideleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL, "sexe" => 'Femme']);
        $form = $this->createForm(NaissanceType::class, $naissance, ['perenaisse' => $perenaisse, 'merenaisse' => $merenaisse]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($type === 'new') {
                $naissance->setCreatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setEglise($user->getEglise())
                        ->setCreatedBy($user)
                ;
            } else {
                $naissance->setUpdatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setUpdatedBy($user)
                ;
            }

            //Calcul d'age en fonction de la date de naissance et la date d'ohjodui
            $naiss = $form['datenaissance']->getData();

            $aujourdhui = new DateTime("now");

            if ($aujourdhui < $naiss) {
                $this->addFlash('warning', 'Veuillez verifier la date de naissance.');
                return $this->redirect('new');
            }


            //            Insertion image de profile
            if ($photo = $form['photo']->getData()) {
                $filename = bin2hex(random_bytes(6)) . '.' . $photo->guessExtension();
                try {
                    $photo->move($photoDir, $filename);
                } catch (FileException $e) {
                    // unable to upload the photo, give up
                }
                $naissance->setPhotoFile($filename);
            }


            $entityManager->persist($naissance);
            $entityManager->flush();

            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'naissance_new' : 'naissance_index';
            if ($nextAction) {
                $this->addFlash('naissance', 'Action effectuée avec succès.');
            }

            return $this->redirectToRoute($nextAction);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('naissance/new.html.twig', [
                    'naissance' => $naissance,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

   
    #[Route('/{id}/show', name: 'naissance_show', methods: ['GET', 'POST'])]
    public function show(Naissance $naissance): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_CONJUGAL')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        return $this->render('naissance/show.html.twig', [
                    'naissance' => $naissance,
        ]);
    }

    #[Route('/{id}', name: 'naissance_delete', methods: ['POST'])]
    public function delete(Request $request, Naissance $naissance): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_CONJUGAL')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($this->isCsrfTokenValid('delete' . $naissance->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();

   
            $naissance->setDeletedFromIp($this->GetIp());
            $naissance->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $naissance->setDeletedBy($user);
                $this->addFlash('danger', 'Suppression avec succès.');
            $entityManager->flush();
        }

        return $this->redirectToRoute('naissance_index');
    }

}
