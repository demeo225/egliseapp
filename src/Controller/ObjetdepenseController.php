<?php

namespace App\Controller;

use App\Entity\Objetdepense;
use App\Form\ObjetdepenseType;
use App\Repository\ObjetdepenseRepository;
use App\Traits\ClientIp;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/objetdepense')]
class ObjetdepenseController extends AbstractController
{
    use ClientIp;

    #[Route('/', name: 'app_objetdepense_index', methods: ['GET'])]
    public function index(ObjetdepenseRepository $objetdepenseRepository): Response
    {
        return $this->render('objetdepense/index.html.twig', [
            'objetdepenses' => $objetdepenseRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_objetdepense_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ObjetdepenseRepository $objetdepenseRepository): Response
    {
        $objetdepense = new Objetdepense();
        $form = $this->createForm(ObjetdepenseType::class, $objetdepense);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $eglise = $this->getUser()->getEglise();
            $user = $this->getUser();
            $objetdepense->setCreatedFromIp($this->GetIp()) // remplacement de la function par le trait
            ->setEglise($eglise)
            ->setIdeglise($user->getEglise()->GetId())
            ->setCreatedBy($user)
    ;
            $objetdepenseRepository->add($objetdepense);
             $this->addFlash('success', 'Enregistrement effectué avec succès.');
            return $this->redirectToRoute('app_objetdepense_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('objetdepense/new.html.twig', [
            'objetdepense' => $objetdepense,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_objetdepense_show', methods: ['GET'])]
    public function show(Objetdepense $objetdepense): Response
    {
        return $this->render('objetdepense/show.html.twig', [
            'objetdepense' => $objetdepense,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_objetdepense_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Objetdepense $objetdepense, ObjetdepenseRepository $objetdepenseRepository): Response
    {
        $form = $this->createForm(ObjetdepenseType::class, $objetdepense);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $objetdepenseRepository->add($objetdepense);
             $this->addFlash('success', 'Modification effectuée avec succès.');
            return $this->redirectToRoute('app_objetdepense_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('objetdepense/edit.html.twig', [
            'objetdepense' => $objetdepense,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_objetdepense_delete', methods: ['POST'])]
    public function delete(Request $request, Objetdepense $objetdepense, ObjetdepenseRepository $objetdepenseRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$objetdepense->getId(), $request->request->get('_token'))) {
            $objetdepenseRepository->remove($objetdepense);
        }

        return $this->redirectToRoute('app_objetdepense_index', [], Response::HTTP_SEE_OTHER);
    }
}
