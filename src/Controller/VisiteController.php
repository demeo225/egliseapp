<?php

namespace App\Controller;

use App\Entity\Visite;
use App\Form\VisiteType;
use App\Repository\FideleRepository;
use App\Repository\VisiteRepository;
use DateTime;
use App\Traits\ClientIp;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/visite')]
class VisiteController extends AbstractController {
use ClientIp;
    
    #[Route('/', name: 'app_visite_index', methods: ['GET'])]
    public function index(VisiteRepository $visiteRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $visite = $visiteRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $difference = $visiteRepository->getVisiteByDates();
        return $this->render('visite/index.html.twig', [
                    'visites' => $visite,
                    'differences' => $difference,
        ]);
    }

    #[Route('/new', name: 'app_visite_new', methods: ['GET', 'POST'])]
    public function new(Request $request, VisiteRepository $visiteRepository, FideleRepository $fideleRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $visite = new Visite();
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $fidele = $fideleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $form = $this->createForm(VisiteType::class, $visite, ['receptionpar' => $fidele]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


   
            $visite->setCreatedFromIp($this->GetIp());
            $user = $this->getUser();
            $eglise = $this->getUser()->getEglise();
            $visite->setCreatedBy($user);
            $visite->setEglise($eglise);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($visite);
            $entityManager->flush();
            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'app_visite_new' : 'app_visite_index';
            if ($nextAction) {
                $this->addFlash('success', 'Enregistrement avec succès.');
            }

            return $this->redirectToRoute($nextAction);
//            return $this->redirectToRoute('app_visite_index', [], Response::HTTP_SEE_OTHER);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('visite/new.html.twig', [
                    'visite' => $visite,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    #[Route('/{id}', name: 'app_visite_show', methods: ['GET'])]
    public function show(Visite $visite): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        return $this->render('visite/show.html.twig', [
                    'visite' => $visite,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_visite_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Visite $visite, VisiteRepository $visiteRepository, FideleRepository $fideleRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $fidele = $fideleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $form = $this->createForm(VisiteType::class, $visite, ['receptionpar' => $fidele]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $visite->setUpdatedFromIp($this->GetIp());
            $user = $this->getUser();
            $visite->setUpdatedBy($user);
            $visiteRepository->add($visite);
            return $this->redirectToRoute('app_visite_index', [], Response::HTTP_SEE_OTHER);
        }
        if ($request) {
            $this->addFlash('success', 'Modification avec succès.');
        }

        return $this->render('visite/edit.html.twig', [
                    'visite' => $visite,
                    'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_visite_delete', methods: ['POST'])]
    public function delete(Request $request, Visite $visite, VisiteRepository $visiteRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici! Veuillez vous adresser au secretariat');
        }
        if ($this->isCsrfTokenValid('delete' . $visite->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();


            $visite->setDeletedFromIp($this->GetIp());
            $visite->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $visite->setDeletedBy($user);
            $entityManager->flush();
        }
        if ($request) {
            $this->addFlash('danger', 'Suppression avec succès.');
        }
        return $this->redirectToRoute('app_visite_index', [], Response::HTTP_SEE_OTHER);
    }

}
