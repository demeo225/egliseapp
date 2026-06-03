<?php

namespace App\Controller;

use App\Entity\Detailcotisation;
use App\Entity\Fidelecotiser;
use App\Form\FidelecotiserType;
use App\Repository\CotisationRepository;
use App\Repository\DetailcotisationRepository;
use App\Repository\FidelecotiserRepository;
use App\Repository\FideleRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Traits\ClientIp;

#[Route('/fidelecotiser')]
class FidelecotiserController extends AbstractController {

    use ClientIp;

    #[Route('/', name: 'fidelecotiser_index', methods: ['GET'])]
    public function index(FidelecotiserRepository $fidelecotiserRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $fidelecotiser = $fidelecotiserRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        return $this->render('fidelecotiser/index.html.twig', [
                    'fidelecotisers' => $fidelecotiser,
        ]);
    }

    #[Route('detail/{id}', name: 'detailcotisation_delete', methods: ['POST'])]
    public function deleteDetail(Request $request, Detailcotisation $detail, DetailcotisationRepository $detailRepository, FidelecotiserRepository $fidelecotiserRepo, CotisationRepository $cotisationRepo): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_FINANCE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($this->isCsrfTokenValid('deletedetail' . $detail->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();

            $detail->setDeletedFromIp($this->GetIp());
            $detail->setDeletedAt(new DateTime("now"));
            $montant = $detail->getMontantpayer();
            $user = $this->getUser();

            $dql = $detailRepository->findBy(['fidele' => $detail->getFidele(), 'cotisation' => $detail->getCotisation(), 'fidelecotiser' => $detail->getFidelecotiser()]);
            // $fidele = $detail->getFidele();
            $detail->setReste(NULL);
            // $cotisation = $detail->getCotisation();
            if ($dql) {
//                $dql1 = $cotisationRepo->findBy(['deletedAt'=>NULL ]);
                $id = $dql[0]->getId();
                $activite = $fidelecotiserRepo->findOneByFidelecotiser($id);

                $reste = $activite->getRestecotiser();
                $mont = $activite->getMontpaye();
                $j = 0;
                $b = 0;
                $j = $mont - $montant;
                $b = $reste + $montant;
                $activite->setMontpaye($j);
                $activite->setRestecotiser($b);
                $entityManager->flush($activite);
            }

//            $eglise = $this->getUser()->getEglise();
//
//            $total = $detailcotisation->getMontant();
//            $dql = $soldeRepo->findBy(['eglise' => $eglise]);
//            $id = $dql[0]->getId();
//            $activite = $soldeRepo->findOneBySolde($id);
//            $mont = $activite->getMontant();
//            $j = 0;
//            $j = $mont + $total;
//            $activite->setMontant($j);
            $this->addFlash('danger', 'Suppression avec succès');
            $detail->setDeletedBy($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('fidelecotiser_detailcotisation');
    }

    #[Route('/detailcotisation', name: 'fidelecotiser_detailcotisation', methods: ['GET'])]
    public function detailCotisation(DetailcotisationRepository $detailRepo): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $detailcotisation = $detailRepo->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        return $this->render('fidelecotiser/detailcotisation.html.twig', [
                    'details' => $detailcotisation,
        ]);
    }

    #[Route('/new', name: 'fidelecotiser_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FidelecotiserRepository $fidelecotiserRepository, FideleRepository $fideleRepository, CotisationRepository $cotisationRepository, DetailcotisationRepository $detail): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $fidelecotiser = new Fidelecotiser();
        $eglise = $this->getUser()->getEglise();

        $fidelecotiser->setEglise($eglise);
        $fidele = $fideleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $cotisation = $cotisationRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL, "etatcotisation" => 1]);
        $form = $this->createForm(FidelecotiserType::class, $fidelecotiser, ['fidele' => $fidele, 'cotisation' => $cotisation]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();

            $idc = $form['cotisation']->getData();
            $date = $form['datecotiser']->getData();
            $idf = $form['fidele']->getData();
            $montant = $form['montpaye']->getData();

            $dql = $fidelecotiserRepository->findBy(['fidele' => $fidelecotiser->getFidele(), 'cotisation' => $fidelecotiser->getCotisation()]);

            if ($dql) {


                $id = $dql[0]->getId();
                $activite = $fidelecotiserRepository->findOneByFidelecotiser($id);
                $reste = $activite->getRestecotiser();
                $dejapayer = $activite->getMontpaye();
                $a1 = 0;
                $b1 = 0;
                $a1 = ($reste - $montant);
                $b1 = ($dejapayer + $montant);

                $activite->setUpdatedFromIp($this->GetIp());
                $activite->setUpdatedBy($user);
                $activite->setMontpaye($b1);
                $activite->setRestecotiser($a1);

                $detail2 = new Detailcotisation();
                $detail2->setFidele($idf);
                $detail2->setCotisation($idc);
                $detail2->setEglise($eglise);
                $detail2->setMontant($b1);
                $detail2->setMontantpayer($montant);
                $detail2->setCreatedBy($user);
                $detail2->setCreatedFromIp($this->GetIp());
                $detail2->setReste($a1);
                $detail2->setEtat('1');
//                $detail2->setFidelecotiser($fidelecotiser);
                $detail2->setDatedetail($date);
                $entityManager->persist($detail2);
                $entityManager->flush();
            } else {

                $fidelecotiser = $form->getData();
                $cotiser1 = $cotisationRepository->findOneByCotisation($idc);
                $payer = $cotiser1->getMontant();
                $fidelecotiser->setCreatedBy($user);
                $restepayer = $payer - $montant;
                $fidelecotiser->setRestecotiser($restepayer);
                $fidelecotiser->setCreatedFromIp($this->GetIp());
                $fidelecotiser->setEtatcotiser("1");

                $detail = new Detailcotisation();
                $detail->setFidele($idf);
                $detail->setCotisation($idc);
                $detail->setEglise($eglise);
                $detail->setMontant($payer);
                $detail->setMontantpayer($montant);
                $detail->setCreatedBy($user);
                $detail->setCreatedFromIp($this->GetIp());
//                $detail->setReste($restepayer);
                $detail->setDatedetail($date);
                $detail->setFidelecotiser($fidelecotiser);

                $detail->setEtat('1');
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($fidelecotiser);
                $entityManager->persist($detail);
                $entityManager->flush();
            }


            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'fidelecotiser_new' : 'fidelecotiser_index';
            if ($nextAction) {
                $this->addFlash('success', 'Enregistrement avec succès.');
            }

            return $this->redirectToRoute($nextAction);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('fidelecotiser/new.html.twig', [
                    'fidelecotiser' => $fidelecotiser,
                    'details' => $detail,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    #[Route('/{id}', name: 'fidelecotiser_show', methods: ['GET'])]
    public function show(Fidelecotiser $fidelecotiser): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        return $this->render('fidelecotiser/show.html.twig', [
                    'fidelecotiser' => $fidelecotiser,
        ]);
    }

    #[Route('/{id}/edit', name: 'fidelecotiser_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Fidelecotiser $fidelecotiser, FideleRepository $fideleRepository, CotisationRepository $cotisationRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $fidelecotiser->setUpdatedBy($user);
        // $fidelecotiser->setEglise($eglise);
        $fidele = $fideleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $cotisation = $cotisationRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $form = $this->createForm(FidelecotiserType::class, $fidelecotiser, ['fidele' => $fidele, 'cotisation' => $cotisation]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $id = $form['cotisation']->getData();

            $cotise2 = $cotisationRepository->findOneByCotisation($id);
            if ($cotise2) {
                $a = 0;
                $mont = $cotise2->getMontant();
                $montant = $form['montpaye']->getData();
                $a = $mont - $montant;
                $fidelecotiser->setMontpaye($montant);
                $fidelecotiser->setRestecotiser($a);
                $fidelecotiser->setUpdatedFromIp($this->GetIp());
                $fidelecotiser->setUpdatedBy($user);
                $this->addFlash('success', 'Modification effectuée avec succès.');
                $this->getDoctrine()->getManager()->flush();
            }
            return $this->redirectToRoute('fidelecotiser_index');
        }

        return $this->render('fidelecotiser/edit.html.twig', [
                    'fidelecotiser' => $fidelecotiser,
                    'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'fidelecotiser_delete', methods: ['POST'])]
    public function delete(Request $request, Fidelecotiser $fidelecotiser): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($this->isCsrfTokenValid('delete' . $fidelecotiser->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();

            $fidelecotiser->setDeletedFromIp($this->GetIp());
            $fidelecotiser->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $fidelecotiser->setDeletedBy($user);
            $this->addFlash('danger', 'Suppression effectuée avec succès.');
            $entityManager->flush();
        }

        return $this->redirectToRoute('fidelecotiser_index');
    }

}
