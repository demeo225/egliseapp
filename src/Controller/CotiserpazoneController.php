<?php

namespace App\Controller;

use App\Entity\Cotiserpazone;
use App\Entity\Detailparzone;
use App\Form\CotiserpazoneType;
use App\Repository\ZoneRepository;
use App\Repository\CotisationparzoneRepository;
use App\Repository\CotiserpazoneRepository;
use App\Repository\DetailparzoneRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use DateTime;
use App\Traits\ClientIp;


#[Route('/cotiserpazone')]
class CotiserpazoneController extends AbstractController {
    use ClientIp;
    
    #[Route('/', name: 'app_cotiserpazone_index', methods: ['GET'])]
    public function index(CotiserpazoneRepository $cotiserpazoneRepository): Response {
        if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $cotiserpazone = $cotiserpazoneRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        return $this->render('cotiserpazone/index.html.twig', [
                    'cotiserpazones' => $cotiserpazone,
        ]);
    }

    #[Route('/new', name: 'app_cotiserpazone_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, CotiserpazoneRepository $cotiserpazoneRepository, ZoneRepository $zoneRepository, CotisationparzoneRepository $cotisationparzoneRepository, DetailparzoneRepository $detail): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $cotiserpazone = new Cotiserpazone();
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $cotiserpazone->setEglise($eglise);
        $zone = $zoneRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $cotisationparzone = $cotisationparzoneRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL, "etatcotiser"=>1]);
        $form = $this->createForm(CotiserpazoneType::class, $cotiserpazone, ['zone' => $zone, 'cotisationparzone' => $cotisationparzone]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();


            $idc = $form['cotisationparzone']->getData();
            $date = $form['datecotiser']->getData();
            $idf = $form['zone']->getData();
            $montant = $form['montantpayer']->getData();

            $dql = $cotiserpazoneRepository->findBy(['zone' => $cotiserpazone->getZone(), 'cotisationparzone' => $cotiserpazone->getCotisationparzone()]);

            if ($dql) {

                $id = $dql[0]->getId();
                $activite = $cotiserpazoneRepository->findOneByCotiserpazone($id);
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

                $detail2 = new Detailparzone();
                $detail2->setZone($idf);
                $detail2->setCotisationparzone($idc);
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

                $cotiserpazone = $form->getData();
                $cotiser2 = $cotisationparzoneRepository->findOneByCotisationparzone($idc);
                $payer = $cotiser2->getMontant();
                $cotiserpazone->setCreatedBy($user);
                $restepayer = $payer - $montant;
                $cotiserpazone->setReste($restepayer);
                $cotiserpazone->setCreatedFromIp($this->GetIp());

                $detail = new Detailparzone();
                $detail->setZone($idf);
                $detail->setCotisationparzone($idc);
                $detail->setEglise($eglise);
                $detail->setMontant($payer);
                $detail->setMontantpayer($montant);
                $detail->setCreatedBy($user);
                $detail->setCreatedFromIp($this->GetIp());
                $detail->setReste($restepayer);
                $detail->setDatedetail($date);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($cotiserpazone);
                $entityManager->persist($detail);
                $entityManager->flush();
            }


            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'app_cotiserpazone_new' : 'app_cotiserpazone_index';
            if ($nextAction) {
                $this->addFlash('cotiserpazone', 'Enregistrement avec succès.');
            }

            return $this->redirectToRoute($nextAction);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('cotiserpazone/new.html.twig', [
                    'cotiserpazone' => $cotiserpazone,
                    'details' => $detail,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    #[Route('/detailcotisation', name: 'cotiserpazone_detailcotisation', methods: ['GET'])]
    public function detailCotisation(DetailparzoneRepository $detailRepo): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $detailcotisation = $detailRepo->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        return $this->render('cotiserpazone/detailcotisation.html.twig', [
                    'details' => $detailcotisation,
        ]);
    }

    #[Route('/{id}', name: 'app_cotiserpazone_show', methods: ['GET'])]
    public function show(Cotiserpazone $cotiserpazone): Response {
        if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        return $this->render('cotiserpazone/show.html.twig', [
                    'cotiserpazone' => $cotiserpazone,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_cotiserpazone_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Cotiserpazone $cotiserpazone, ZoneRepository $zoneRepository, CotisationparzoneRepository $cotisationparzoneRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $cotiserpazone->setUpdatedBy($user);
        // $$cotiserpazone->setEglise($eglise);
        $zone = $zoneRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $cotisationparzone = $cotisationparzoneRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL, "etatcotiser"=>1]);
        $form = $this->createForm(CotiserpazoneType::class, $cotiserpazone, ['zone' => $zone, 'cotisationparzone' => $cotisationparzone]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $id = $form['cotisationparzone']->getData();
            $cotise2 = $cotisationparzoneRepository->findOneByCotisationparzone($id);
            if ($cotise2) {
                $a = 0;
                $mont = $cotise2->getMontant();

                $montant = $form['montantpayer']->getData();
                $a = $mont - $montant;
                $cotiserpazone->setMontantpayer($montant);
                $cotiserpazone->setReste($a);
                $cotiserpazone->setUpdatedFromIp($this->GetIp());
                $cotiserpazone->setUpdatedBy($user);
                $this->getDoctrine()->getManager()->flush();
            }
            return $this->redirectToRoute('app_cotiserpazone_index');
        }

        return $this->render('cotiserpazone/edit.html.twig', [
                    'cotiserpazone' => $cotiserpazone,
                    'form' => $form->createView(),
        ]);
    }

    #[Route('cotiserpazone/{id}', name: 'app_cotiserpazone_delete', methods: ['POST'])]
    public function delete(Request $request, Cotiserpazone $cotiserpazone): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($this->isCsrfTokenValid('delete' . $cotiserpazone->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();

            $cotiserpazone->setDeletedFromIp($this->GetIp());
            $cotiserpazone->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $cotiserpazone->setDeletedBy($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_cotiserpazone_index');
    }

}
