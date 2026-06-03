<?php

namespace App\Controller;

use App\Entity\Cotiserpardepartement;
use App\Entity\Detailpardepartement;
use App\Form\CotiserpardepartementType;
use App\Repository\DepartementRepository;
use App\Repository\CotisationpardepartementRepository;
use App\Repository\CotiserpardepartementRepository;
use App\Repository\DetailpardepartementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use DateTime;
use App\Traits\ClientIp;


#[Route('/cotiserpardepartement')]
class CotiserpardepartementController extends AbstractController {
    use ClientIp;
    
    
    #[Route('/', name: 'app_cotiserpardepartement_index', methods: ['GET'])]
    public function index(CotiserpardepartementRepository $cotiserpardepartementRepository): Response {
        if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $cotiserpardepartement = $cotiserpardepartementRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        return $this->render('cotiserpardepartement/index.html.twig', [
                    'cotiserpardepartements' => $cotiserpardepartement,
        ]);
    }

    #[Route('/new', name: 'app_cotiserpardepartement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, CotiserpardepartementRepository $cotiserpardepartementRepository, DepartementRepository $departementRepository, CotisationpardepartementRepository $cotisationpardepartementRepository, DetailpardepartementRepository $detail): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $cotiserpardepartement = new Cotiserpardepartement();
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $cotiserpardepartement->setEglise($eglise);
        $departement = $departementRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $cotisationpardepartement = $cotisationpardepartementRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL, "etatcotiser"=>1]);
        $form = $this->createForm(CotiserpardepartementType::class, $cotiserpardepartement, ['departement' => $departement, 'cotisationpardepartement' => $cotisationpardepartement]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();


            $idc = $form['cotisationpardepartement']->getData();
            $date = $form['datecotiser']->getData();
            $idf = $form['departement']->getData();
            $montant = $form['montantpayer']->getData();

            $dql = $cotiserpardepartementRepository->findBy(['departement' => $cotiserpardepartement->getDepartement(), 'cotisationpardepartement' => $cotiserpardepartement->getCotisationpardepartement()]);

            if ($dql) {

                $id = $dql[0]->getId();
                $activite = $cotiserpardepartementRepository->findOneByCotiserpardepartement($id);
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

                $detail2 = new Detailpardepartement();
                $detail2->setDepartement($idf);
                $detail2->setCotisationpardepartement($idc);
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

                $cotiserpardepartement = $form->getData();
                $cotiser2 = $cotisationpardepartementRepository->findOneByCotisationpardepartement($idc);
                $payer = $cotiser2->getMontant();
                $cotiserpardepartement->setCreatedBy($user);
                $restepayer = $payer - $montant;
                $cotiserpardepartement->setReste($restepayer);
                $cotiserpardepartement->setCreatedFromIp($this->GetIp());
                //$cotiserpardepartement->setEtatcotiser("1");

                $detail = new Detailpardepartement();
                $detail->setDepartement($idf);
                $detail->setCotisationpardepartement($idc);
                $detail->setEglise($eglise);
                $detail->setMontant($payer);
                $detail->setMontantpayer($montant);
                $detail->setCreatedBy($user);
                $detail->setCreatedFromIp($this->GetIp());
                $detail->setReste($restepayer);
                $detail->setDatedetail($date);
               // $detail->setEtat('1');
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($cotiserpardepartement);
                $entityManager->persist($detail);
                $entityManager->flush();
            }


            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'app_cotiserpardepartement_new' : 'app_cotiserpardepartement_index';
            if ($nextAction) {
                $this->addFlash('cotiserpardepartement', 'Enregistrement avec succès.');
            }

            return $this->redirectToRoute($nextAction);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('cotiserpardepartement/new.html.twig', [
                    'cotiserpardepartement' => $cotiserpardepartement,
                    'details' => $detail,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    #[Route('/detailcotisation', name: 'cotiserpardepartement_detailcotisation', methods: ['GET'])]
    public function detailCotisation(DetailpardepartementRepository $detailRepo): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $detailcotisation = $detailRepo->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        return $this->render('cotiserpardepartement/detailcotisation.html.twig', [
                    'details' => $detailcotisation,
        ]);
    }

    #[Route('/{id}', name: 'app_cotiserpardepartement_show', methods: ['GET'])]
    public function show(Cotiserpardepartement $cotiserpardepartement): Response {
        if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        return $this->render('cotiserpardepartement/show.html.twig', [
                    'cotiserpardepartement' => $cotiserpardepartement,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_cotiserpardepartement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Cotiserpardepartement $cotiserpardepartement, DepartementRepository $departementRepository, CotisationpardepartementRepository $cotisationpardepartementRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $cotiserpardepartement->setUpdatedBy($user);
        // $$cotiserpardepartement->setEglise($eglise);
        $departement = $departementRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $cotisationpardepartement = $cotisationpardepartementRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL, "etatcotiser"=>1]);
        $form = $this->createForm(CotiserpardepartementType::class, $cotiserpardepartement, ['departement' => $departement, 'cotisationpardepartement' => $cotisationpardepartement]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $id = $form['cotisationpardepartement']->getData();

            $cotise2 = $cotisationpardepartementRepository->findOneByCotisationpardepartement($id);
            if ($cotise2) {
                $a = 0;
                $mont = $cotise2->getMontant();
                $montant = $form['montantpayer']->getData();
                $a = $mont - $montant;
                $cotiserpardepartement->setMontantpayer($montant);
                $cotiserpardepartement->setReste($a);
                $cotiserpardepartement->setUpdatedFromIp($this->GetIp());
                $cotiserpardepartement->setUpdatedBy($user);
                $this->getDoctrine()->getManager()->flush();
            }
            return $this->redirectToRoute('app_cotiserpardepartement_index');
        }

        return $this->render('cotiserpardepartement/edit.html.twig', [
                    'cotiserpardepartement' => $cotiserpardepartement,
                    'form' => $form->createView(),
        ]);
    }

    #[Route('cotiserpardepartement/{id}', name: 'app_cotiserpardepartement_delete', methods: ['POST'])]
    public function delete(Request $request, Cotiserpardepartement $cotiserpardepartement): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($this->isCsrfTokenValid('delete' . $cotiserpardepartement->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();

            $cotiserpardepartement->setDeletedFromIp($this->GetIp());
            $cotiserpardepartement->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $cotiserpardepartement->setDeletedBy($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_cotiserpardepartement_index');
    }

}
