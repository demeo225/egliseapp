<?php

namespace App\Controller;

use App\Entity\Visite2;
use App\Form\Visite2Type;
use App\Repository\FideleRepository;
use App\Repository\Visite2Repository;
use DateTime;
use App\Traits\ClientIp;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/visite2')]
class Visite2Controller extends AbstractController {
use ClientIp;
    
    #[Route('/', name: 'app_visite2_index', methods: ['GET'])]
    public function index(Visite2Repository $visite2Repository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $visite2 = $visite2Repository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
       // $difference = $visite2Repository->getVisite2ByDates();
        return $this->render('visite2/index.html.twig', [
                    'visite2s' => $visite2,
                   // 'differences' => $difference,
        ]);
    }

    #[Route('/new', name: 'app_visite2_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Visite2Repository $visite2Repository, FideleRepository $fideleRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $visite2 = new Visite2();
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $fidele = $fideleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $form = $this->createForm(Visite2Type::class, $visite2, ['fidele' => $fidele]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


   
            $visite2->setCreatedFromIp($this->GetIp());
            $user = $this->getUser();
            $eglise = $this->getUser()->getEglise();
            $visite2->setCreatedBy($user);
            $visite2->setEglise($eglise);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($visite2);
            $entityManager->flush();
            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'app_visite2_new' : 'app_visite2_index';
            if ($nextAction) {
                $this->addFlash('success', 'Enregistrement avec succès.');
            }

            return $this->redirectToRoute($nextAction);
//            return $this->redirectToRoute('app_visite2_index', [], Response::HTTP_SEE_OTHER);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('visite2/new.html.twig', [
                    'visite2' => $visite2,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    #[Route('detail/{id}', name: 'app_visite2_show', methods: ['POST'])]
    public function show(Visite2 $visite2): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        return $this->render('visite2/show.html.twig', [
                    'visite2' => $visite2,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_visite2_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Visite2 $visite2, Visite2Repository $visite2Repository, FideleRepository $fideleRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $fidele = $fideleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $form = $this->createForm(Visite2Type::class, $visite2, ['fidele' => $fidele]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $visite2->setUpdatedFromIp($this->GetIp());
            $user = $this->getUser();
            $visite2->setUpdatedBy($user);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($visite2);
            $entityManager->flush();
            return $this->redirectToRoute('app_visite2_index', [], Response::HTTP_SEE_OTHER);
        }
        if ($request) {
            $this->addFlash('success', 'Modification avec succès.');
        }

        return $this->render('visite2/edit.html.twig', [
                    'visite2' => $visite2,
                    'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_visite2_delete', methods: ['POST'])]
    public function delete(Request $request, Visite2 $visite2, Visite2Repository $visite2Repository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici! Veuillez vous adresser au secretariat');
        }
        if ($this->isCsrfTokenValid('delete' . $visite2->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();


            $visite2->setDeletedFromIp($this->GetIp());
            $visite2->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $visite2->setDeletedBy($user);
            $entityManager->flush();
        }
        if ($request) {
            $this->addFlash('danger', 'Suppression avec succès.');
        }
        return $this->redirectToRoute('app_visite2_index', [], Response::HTTP_SEE_OTHER);
    }

}
