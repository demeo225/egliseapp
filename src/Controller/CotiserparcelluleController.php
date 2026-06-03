<?php

namespace App\Controller;

use App\Entity\Cotiserparcellule;
use App\Entity\Detailparcellule;
use App\Form\CotiserparcelluleType;
use App\Repository\CelluleRepository;
use App\Repository\CotisationparcelluleRepository;
use App\Repository\CotiserparcelluleRepository;
use App\Repository\DetailparcelluleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use DateTime;
use App\Traits\ClientIp;


#[Route('/cotiserparcellule')]
class CotiserparcelluleController extends AbstractController {
    use ClientIp;
    
    #[Route('/', name: 'app_cotiserparcellule_index', methods: ['GET'])]
    public function index(CotiserparcelluleRepository $cotiserparcelluleRepository): Response {
        if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $cotiserparcellule = $cotiserparcelluleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        return $this->render('cotiserparcellule/index.html.twig', [
                    'cotiserparcellules' => $cotiserparcellule,
        ]);
    }

    #[Route('/new', name: 'app_cotiserparcellule_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, CotiserparcelluleRepository $cotiserparcelluleRepository, CelluleRepository $celluleRepository, CotisationparcelluleRepository $cotisationparcelluleRepository, DetailparcelluleRepository $detail): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $cotiserparcellule = new Cotiserparcellule();
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $cotiserparcellule->setEglise($eglise);
        $cellule = $celluleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $cotisationparcellule = $cotisationparcelluleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL, "etatcotiser"=>1]);
        $form = $this->createForm(CotiserparcelluleType::class, $cotiserparcellule, ['cellule' => $cellule, 'cotisationparcellule' => $cotisationparcellule]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();


            $idc = $form['cotisationparcellule']->getData();
            $date = $form['datecotiser']->getData();
            $idf = $form['cellule']->getData();
            $montant = $form['montantpayer']->getData();

            $dql = $cotiserparcelluleRepository->findBy(['cellule' => $cotiserparcellule->getCellule(), 'cotisationparcellule' => $cotiserparcellule->getCotisationparcellule()]);

            if ($dql) {

                $id = $dql[0]->getId();
                $activite = $cotiserparcelluleRepository->findOneByCotiserparcellule($id);
                $reste = $activite->getReste();
                $dejapayer = $activite->getMontantpayer();
                $a1 = 0;
                $b1 = 0;
                $a1 = ($reste - $montant);
                $b1 = ($dejapayer + $montant);
                $activite->setUpdatedFromIp($this->GetIp());
                $activite->setUpdatedBy($user);
                $activite->setMontantpayer($b1);
                $activite->setReste($a1);

                $detail2 = new Detailparcellule();
                $detail2->setCellule($idf);
                $detail2->setCotisationparcellule($idc);
                $detail2->setEglise($eglise);
                $detail2->setMontant($b1);
                $detail2->setMontantpayer($montant);
                $detail2->setCreatedBy($user);
                $detail2->setCreatedFromIp($this->GetIp());
                $detail2->setReste($a1);
                // $detail2->setEtat('1');

                $detail2->setDatedetail($date);
                $entityManager->persist($detail2);
                $entityManager->flush();
            } else {

                $cotiserparcellule = $form->getData();
                $cotiser2 = $cotisationparcelluleRepository->findOneByCotisationparcellule($idc);
                $payer = $cotiser2->getMontant();
                $cotiserparcellule->setCreatedBy($user);
                $restepayer = $payer - $montant;
                $cotiserparcellule->setReste($restepayer);
                $cotiserparcellule->setCreatedFromIp($this->GetIp());

                $detail = new Detailparcellule();
                $detail->setCellule($idf);
                $detail->setCotisationparcellule($idc);
                $detail->setEglise($eglise);
                $detail->setMontant($payer);
                $detail->setMontantpayer($montant);
                $detail->setCreatedBy($user);
                $detail->setCreatedFromIp($this->GetIp());
                $detail->setReste($restepayer);
                $detail->setDatedetail($date);
               // $detail->setEtat('1');
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($cotiserparcellule);
                $entityManager->persist($detail);
                $entityManager->flush();
            }


            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'app_cotiserparcellule_new' : 'app_cotiserparcellule_index';
            if ($nextAction) {
                $this->addFlash('cotiserparcellule', 'Enregistrement avec succès.');
            }

            return $this->redirectToRoute($nextAction);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('cotiserparcellule/new.html.twig', [
                    'cotiserparcellule' => $cotiserparcellule,
                    'details' => $detail,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    #[Route('/detailcotisation', name: 'cotiserparcellule_detailcotisation', methods: ['GET'])]
    public function detailCotisation(DetailparcelluleRepository $detailRepo): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $detailcotisation = $detailRepo->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        return $this->render('cotiserparcellule/detailcotisation.html.twig', [
                    'details' => $detailcotisation,
        ]);
    }

    #[Route('/{id}', name: 'app_cotiserparcellule_show', methods: ['GET'])]
    public function show(Cotiserparcellule $cotiserparcellule): Response {
        if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        return $this->render('cotiserparcellule/show.html.twig', [
                    'cotiserparcellule' => $cotiserparcellule,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_cotiserparcellule_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Cotiserparcellule $cotiserparcellule, CelluleRepository $celluleRepository, CotisationparcelluleRepository $cotisationparcelluleRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $cotiserparcellule->setUpdatedBy($user);
        // $$cotiserparcellule->setEglise($eglise);
        $cellule = $celluleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $cotisationparcellule = $cotisationparcelluleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $form = $this->createForm(CotiserparcelluleType::class, $cotiserparcellule, ['cellule' => $cellule, 'cotisationparcellule' => $cotisationparcellule]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $id = $form['cotisationparcellule']->getData();

            $cotise2 = $cotisationparcelluleRepository->findOneByCotisationparcellule($id);
            if ($cotise2) {
                $a = 0;
                $mont = $cotise2->getMontant();
                $montant = $form['montantpayer']->getData();
                $a = $mont - $montant;
                $cotiserparcellule->setMontantpayer($montant);
                $cotiserparcellule->setReste($a);
                $cotiserparcellule->setUpdatedFromIp($this->GetIp());
                $cotiserparcellule->setUpdatedBy($user);
                $this->getDoctrine()->getManager()->flush();
            }
            return $this->redirectToRoute('app_cotiserparcellule_index');
        }

        return $this->render('cotiserparcellule/edit.html.twig', [
                    'cotiserparcellule' => $cotiserparcellule,
                    'form' => $form->createView(),
        ]);
    }

    #[Route('cotiserparcellule/{id}', name: 'app_cotiserparcellule_delete', methods: ['POST'])]
    public function delete(Request $request, Cotiserparcellule $cotiserparcellule): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($this->isCsrfTokenValid('delete' . $cotiserparcellule->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();

            $cotiserparcellule->setDeletedFromIp($this->GetIp());
            $cotiserparcellule->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $cotiserparcellule->setDeletedBy($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_cotiserparcellule_index');
    }

}
