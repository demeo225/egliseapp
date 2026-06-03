<?php

namespace App\Controller;

use App\Entity\Paiement;
use App\Form\PaiementType;
use App\Repository\PaiementRepository;
use DateTime;
use App\Traits\ClientIp;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/paiement')]

class PaiementController extends AbstractController {
   use ClientIp;
    
    #[Route('/', name: 'app_paiement_index', methods: ['GET'])]

    public function index(PaiementRepository $paiementRepository): Response {
        if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        return $this->render('paiement/index.html.twig', [
                    'paiements' => $paiementRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_paiement_new', methods: ['GET', 'POST'])]

    public function new(Request $request, PaiementRepository $paiementRepository): Response {
        if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $paiement = new Paiement();
        $form = $this->createForm(PaiementType::class, $paiement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $paiement->setCreatedFromIp($this->GetIp());
            $user = $this->getUser();
            $paiement->getCreatedBy($user);
             $this->addFlash('success', 'Création effectuée avec succès.');
            $paiementRepository->add($paiement);
            return $this->redirectToRoute('app_paiement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('paiement/new.html.twig', [
                    'paiement' => $paiement,
                    'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_paiement_show', methods: ['GET'])]

    public function show(Paiement $paiement): Response {
        if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        return $this->render('paiement/show.html.twig', [
                    'paiement' => $paiement,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_paiement_edit', methods: ['GET', 'POST'])]

    public function edit(Request $request, Paiement $paiement, PaiementRepository $paiementRepository): Response {
        if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $form = $this->createForm(PaiementType::class, $paiement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $paiement->setUpdatedFromIp($this->GetIp());
            $paiementRepository->add($paiement);
            return $this->redirectToRoute('app_paiement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('paiement/edit.html.twig', [
                    'paiement' => $paiement,
                    'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_paiement_delete', methods: ['POST'])]

    public function delete(Request $request, Paiement $paiement): Response {
        if ($this->isCsrfTokenValid('delete' . $paiement->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();

            if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
                throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
            }


            $paiement->setDeletedFromIp($this->GetIp());
            $paiement->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $paiement->setDeletedBy($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_paiement_index', [], Response::HTTP_SEE_OTHER);
        ;
    }


}
