<?php

namespace App\Controller;

use App\Entity\Cotisersociale;
use App\Entity\Detailsociale;
use App\Form\CotisersocialeType;
use App\Repository\CotisationsocialeRepository;
use App\Repository\CotisersocialeRepository;
use App\Repository\DetailsocialeRepository;
use App\Repository\FideleRepository;
use App\Repository\TotalsocialeRepository;
use DateTime;
use App\Traits\ClientIp;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cotisersociale')]

class CotisersocialeController extends AbstractController {
    use ClientIp;
    
    #[Route('/', name: 'app_cotisersociale_index', methods: ['GET'])]

    public function index(CotisersocialeRepository $cotisersocialeRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $roles = $user->getRoles();
        $cotisersociale = $cotisersocialeRepository->findBy(['eglise' => $eglise, 'deletedAt' => NULL]);
        return $this->render('cotisersociale/index.html.twig', [
                    'cotisersociales' => $cotisersociale,
        ]);
    }

   
    #[Route('/detailsociale', name: 'app_cotisersociale_detailsociale', methods: ['GET'])]

    public function detailCotisation(DetailsocialeRepository $detailRepo): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_SOCIAL')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $detailcotisation = $detailRepo->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        return $this->render('cotisersociale/detailsociale.html.twig', [
                    'details' => $detailcotisation,
        ]);
    }

    #[Route('/new', name: 'app_cotisersociale_new', methods: ['GET', 'POST'])]

    public function new(Request $request, EntityManagerInterface $entityManager, CotisersocialeRepository $cotisersocialeRepository, FideleRepository $fideleRepository, CotisationsocialeRepository $cotisationsocialeRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_SOCIAL')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $cotisersociale = new Cotisersociale();
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $fidele = $fideleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL, "etatfidele" => 1, "editable" => 1]);
        $cotisationsociale = $cotisationsocialeRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $form = $this->createForm(CotisersocialeType::class, $cotisersociale, ['fidele' => $fidele, 'cotisationsociale' => $cotisationsociale],);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $idc = $form['cotisationsociale']->getData();
            $idf = $form['fidele']->getData();
            $montant = $form['montantpayer']->getData();
            $date = $form['datecotiser']->getData();
            $dql = $cotisersocialeRepository->findBy(['fidele' => $cotisersociale->getFidele(), 'cotisationsociale' => $cotisersociale->getCotisationsociale()]);

            if ($dql) {
                $cotisersociale = $form->getData();

                $id = $dql[0]->getId();
                $activite = $cotisersocialeRepository->findOneByCotisersociale($id);
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


                $detail2 = new Detailsociale();
                $detail2->setFidele($idf);
                $detail2->setCotisationsociale($idc);
                $detail2->setEglise($eglise);
                $detail2->setMontant($b1);
                $detail2->setMontantpayer($montant);
                $detail2->setCreatedBy($user);
                $detail2->setCreatedFromIp($this->GetIp());
                $detail2->setReste($a1);
                $detail2->setEtat('1');
                $detail2->setDatedetail($date);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($detail2);
                $entityManager->flush();
            } else {


                $cotisersociale = $form->getData();

                $cotiser1 = $cotisationsocialeRepository->findOneByCotisationsociale($idc);
                $payer = $cotiser1->getMontant();
//                    $montant = $cotiser->getMontant();
                $restepayer = $payer - $montant;

                $cotisersociale->setReste($restepayer);
                $cotisersociale->setCreatedFromIp($this->GetIp());
//                    $cotisersociale->setEtatcotiser("1");
                $cotisersociale->setEglise($eglise);
                $cotisersociale->setCreatedBy($user);
                $cotisersociale->setCreatedFromIp($this->GetIp());


                $detail = new Detailsociale();
                $detail->setFidele($idf);
                $detail->setCotisationsociale($idc);
                $detail->setEglise($eglise);
                $detail->setMontant($payer);
                $detail->setMontantpayer($montant);
                $detail->setCreatedBy($user);
                $detail->setCreatedFromIp($this->GetIp());
                $detail->setReste($restepayer);
                $detail->setDatedetail($date);
                $detail->setEtat('1');

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($detail);
                $entityManager->persist($cotisersociale);
                $entityManager->flush();
            }

            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'app_cotisersociale_new' : 'app_cotisersociale_index';
            if ($nextAction) {
                $this->addFlash('cotisersociale', 'Enregistrement avec succès.');
            }

            return $this->redirectToRoute($nextAction);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('cotisersociale/new.html.twig', [
                    'cotisersociale' => $cotisersociale,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    #[Route('/{id}', name: 'app_cotisersociale_show', methods: ['GET'])]

    public function show(Cotisersociale $cotisersociale): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_SOCIAL')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        return $this->render('cotisersociale/show.html.twig', [
                    'cotisersociale' => $cotisersociale,
        ]);
    }

    #[Route('cotisersociale/{id}/edit', name: 'app_cotisersociale_edit', methods: ['GET', 'POST'])]

    public function edit(Request $request, Cotisersociale $cotisersociale, CotisersocialeRepository $cotisersocialeRepository,  FideleRepository $fideleRepository, CotisationsocialeRepository $cotisationsocialeRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_SOCIAL')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise()->getId();
        $user = $this->getUser();
        $fidele = $fideleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL, "etatfidele" => 1, "editable" => 1]);
        $cotisationsociale = $cotisationsocialeRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $form = $this->createForm(CotisersocialeType::class, $cotisersociale, [ 'fidele' => $fidele, 'cotisationsociale' => $cotisationsociale],);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $id = $form['cotisationsociale']->getData();
            $cotise2 = $cotisationsocialeRepository->findOneByCotisationsociale($id);
            if ($cotise2) {
                $a = 0;
                $mont = $cotise2->getMontant();
                $montant = $form['montantpayer']->getData();
                $a = $mont - $montant;
                $cotisersociale->setMontantpayer($montant);
                $cotisersociale->setReste($a);
                $cotisersociale->setUpdatedFromIp($this->GetIp());
                $cotisersociale->setUpdatedBy($user);
                $this->getDoctrine()->getManager()->flush();
            }

            return $this->redirectToRoute('app_cotisersociale_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('cotisersociale/edit.html.twig', [
                    'cotisersociale' => $cotisersociale,
                    'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_cotisersociale_delete', methods: ['POST'])]

    public function delete(Request $request, Cotisersociale $cotisersociale, CotisersocialeRepository $cotisersocialeRepository): Response {
        if ($this->isCsrfTokenValid('delete' . $cotisersociale->getId(), $request->request->get('_token'))) {
            if (!$this->isGranted('ROLE_SECRETAIRE')) {
                throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici! Veuillez vous adresser au secretariat!');
            }
            $entityManager = $this->getDoctrine()->getManager();
            $cotisersociale->setDeletedFromIp($this->GetIp());
            $cotisersociale->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $cotisersociale->setDeletedBy($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_cotisersociale_index', [], Response::HTTP_SEE_OTHER);
    }


}
