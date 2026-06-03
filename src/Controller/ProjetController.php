<?php

namespace App\Controller;

use App\Entity\Projet;
use App\Form\ProjetType;
use App\Repository\ProjetRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use DateTime;
use App\Traits\ClientIp;

#[Route('/projet')]
class ProjetController extends AbstractController {
    use ClientIp;
    
    #[Route('/', name: 'projet_index', methods: ['GET'])]

    public function index(ProjetRepository $projetRepository, Request $request): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
            if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $projet = $projetRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        return $this->render('projet/index.html.twig', [
                    'projets' => $projet,
        ]);
    }

    #[Route('/new', name: 'projet_new', methods: ['GET', 'POST'])]

    public function new(Request $request): Response {
            if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $projet = new Projet();
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $form = $this->createForm(ProjetType::class, $projet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $projet->setCreatedFromIp($this->GetIp());
            $eglise = $this->getUser()->getEglise();
            $user = $this->getUser();
            $projet->setCreatedBy($user);
            $projet->setEglise($eglise);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($projet);
            $entityManager->flush();
            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'projet_new' : 'projet_index';
            if ($nextAction) {
                $this->addFlash('success', 'Operation effectuée avec succès');
            }
            return $this->redirectToRoute($nextAction);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('projet/new.html.twig', [
                    'projet' => $projet,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    #[Route('/{id}', name: 'projet_show', methods: ['GET'])]

    public function show(Projet $projet): Response {
            if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        return $this->render('projet/show.html.twig', [
                    'projet' => $projet,
        ]);
    }

    #[Route('/{id}/edit', name: 'projet_edit', methods: ['GET', 'POST'])]

    public function edit(Request $request, Projet $projet): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
            if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $form = $this->createForm(ProjetType::class, $projet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $projet->setUpdatedFromIp($this->GetIp());
            $user = $this->getUser();
            $projet->setUpdatedBy($user);
            $this->getDoctrine()->getManager()->flush();
             $this->addFlash('success', 'Modification effectuée avec succès.');
            return $this->redirectToRoute('projet_index');
        }

        return $this->render('projet/edit.html.twig', [
                    'projet' => $projet,
                    'form' => $form->createView(),
        ]);
    }



    
    #[Route('/{id}', name: 'projet_delete', methods: ['POST'])]
    public function delete(Request $request, Projet $projet): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
            if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($this->isCsrfTokenValid('delete' . $projet->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();

            $projet->setDeletedFromIp($this->GetIp());
            $projet->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $projet->setDeletedBy($user);
             $this->addFlash('danger', 'Suppression effectuée avec succès.');
            $entityManager->flush();
        }

        return $this->redirectToRoute('projet_index');
    }

}
