<?php

namespace App\Controller;

use App\Entity\Cotiserparfamille;
use App\Entity\Detailparfamille;
use App\Form\CotiserparfamilleType;
use App\Repository\FamilleRepository;
use App\Repository\CotisationparfamilleRepository;
use App\Repository\CotiserparfamilleRepository;
use App\Repository\DetailparfamilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use DateTime;
use App\Traits\ClientIp;


#[Route('/cotiserparfamille')]
class CotiserparfamilleController extends AbstractController {
    use ClientIp;
    
    #[Route('/', name: 'app_cotiserparfamille_index', methods: ['GET'])]
    public function index(CotiserparfamilleRepository $cotiserparfamilleRepository): Response {
        if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $cotiserparfamille = $cotiserparfamilleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        return $this->render('cotiserparfamille/index.html.twig', [
                    'cotiserparfamilles' => $cotiserparfamille,
        ]);
    }

    #[Route('/new', name: 'app_cotiserparfamille_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, CotiserparfamilleRepository $cotiserparfamilleRepository, FamilleRepository $familleRepository, CotisationparfamilleRepository $cotisationparfamilleRepository, DetailparfamilleRepository $detail): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $cotiserparfamille = new Cotiserparfamille();
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $cotiserparfamille->setEglise($eglise);
        $famille = $familleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $cotisationparfamille = $cotisationparfamilleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL, "etatcotiser"=>1]);
        $form = $this->createForm(CotiserparfamilleType::class, $cotiserparfamille, ['famille' => $famille, 'cotisationparfamille' => $cotisationparfamille]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();

            $idc = $form['cotisationparfamille']->getData();
            $date = $form['datecotiser']->getData();
            $idf = $form['famille']->getData();
            $montant = $form['montantpayer']->getData();

            $dql = $cotiserparfamilleRepository->findBy(['famille' => $cotiserparfamille->getFamille(), 'cotisationparfamille' => $cotiserparfamille->getCotisationparfamille()]);

            if ($dql) {

                $id = $dql[0]->getId();
                $activite = $cotiserparfamilleRepository->findOneByCotiserparfamille($id);
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

                $detail2 = new Detailparfamille();
                $detail2->setFamille($idf);
                $detail2->setCotisationparfamille($idc);
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

                $cotiserparfamille = $form->getData();
                $cotiser2 = $cotisationparfamilleRepository->findOneByCotisationparfamille($idc);
                $payer = $cotiser2->getMontant();
                $cotiserparfamille->setCreatedBy($user);
                $restepayer = $payer - $montant;
                $cotiserparfamille->setReste($restepayer);
                $cotiserparfamille->setCreatedFromIp($this->GetIp());

                $detail = new Detailparfamille();
                $detail->setFamille($idf);
                $detail->setCotisationparfamille($idc);
                $detail->setEglise($eglise);
                $detail->setMontant($payer);
                $detail->setMontantpayer($montant);
                $detail->setCreatedBy($user);
                $detail->setCreatedFromIp($this->GetIp());
                $detail->setReste($restepayer);
                $detail->setDatedetail($date);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($cotiserparfamille);
                $entityManager->persist($detail);
                $entityManager->flush();
            }


            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'app_cotiserparfamille_new' : 'app_cotiserparfamille_index';
            if ($nextAction) {
                $this->addFlash('cotiserparfamille', 'Enregistrement avec succès.');
            }

            return $this->redirectToRoute($nextAction);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('cotiserparfamille/new.html.twig', [
                    'cotiserparfamille' => $cotiserparfamille,
                    'details' => $detail,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    #[Route('/detailcotisation', name: 'cotiserparfamille_detailcotisation', methods: ['GET'])]
    public function detailCotisation(DetailparfamilleRepository $detailRepo): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $detailcotisation = $detailRepo->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        return $this->render('cotiserparfamille/detailcotisation.html.twig', [
                    'details' => $detailcotisation,
        ]);
    }

    #[Route('/{id}', name: 'app_cotiserparfamille_show', methods: ['GET'])]
    public function show(Cotiserparfamille $cotiserparfamille): Response {
        if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        return $this->render('cotiserparfamille/show.html.twig', [
                    'cotiserparfamille' => $cotiserparfamille,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_cotiserparfamille_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Cotiserparfamille $cotiserparfamille, FamilleRepository $familleRepository, CotisationparfamilleRepository $cotisationparfamilleRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $cotiserparfamille->setUpdatedBy($user);
        // $$cotiserparfamille->setEglise($eglise);
        $famille = $familleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $cotisationparfamille = $cotisationparfamilleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL, "etatcotiser"=>1]);
        $form = $this->createForm(CotiserparfamilleType::class, $cotiserparfamille, ['famille' => $famille, 'cotisationparfamille' => $cotisationparfamille]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $id = $form['cotisationparfamille']->getData();

            $cotise2 = $cotisationparfamilleRepository->findOneByCotisationparfamille($id);
            if ($cotise2) {
                $a = 0;
                $mont = $cotise2->getMontant();

                $montant = $form['montantpayer']->getData();
                $a = $mont - $montant;
                $cotiserparfamille->setMontantpayer($montant);
                $cotiserparfamille->setReste($a);
                $cotiserparfamille->setUpdatedFromIp($this->GetIp());
                $cotiserparfamille->setUpdatedBy($user);
                $this->getDoctrine()->getManager()->flush();
            }
            return $this->redirectToRoute('app_cotiserparfamille_index');
        }

        return $this->render('cotiserparfamille/edit.html.twig', [
                    'cotiserparfamille' => $cotiserparfamille,
                    'form' => $form->createView(),
        ]);
    }

    #[Route('cotiserparfamille/{id}', name: 'app_cotiserparfamille_delete', methods: ['POST'])]
    public function delete(Request $request, Cotiserparfamille $cotiserparfamille): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($this->isCsrfTokenValid('delete' . $cotiserparfamille->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();

            $cotiserparfamille->setDeletedFromIp($this->GetIp());
            $cotiserparfamille->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $cotiserparfamille->setDeletedBy($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_cotiserparfamille_index');
    }

}
