<?php

namespace App\Controller;

use App\Entity\Nominer;
use App\Form\NominerType;
use App\Repository\NominerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/nominer')]
class NominerController extends AbstractController
{
    #[Route('/', name: 'app_nominer_index', methods: ['GET'])]
    public function index(NominerRepository $nominerRepository): Response
    {
        return $this->render('nominer/index.html.twig', [
            'nominers' => $nominerRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_nominer_new', methods: ['GET', 'POST'])]
    public function new(Request $request, NominerRepository $nominerRepository): Response
    {
        $nominer = new Nominer();
        $form = $this->createForm(NominerType::class, $nominer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $nominerRepository->add($nominer);
            return $this->redirectToRoute('app_nominer_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('nominer/new.html.twig', [
            'nominer' => $nominer,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_nominer_show', methods: ['GET'])]
    public function show(Nominer $nominer): Response
    {
        return $this->render('nominer/show.html.twig', [
            'nominer' => $nominer,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_nominer_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Nominer $nominer, NominerRepository $nominerRepository): Response
    {
        $form = $this->createForm(NominerType::class, $nominer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $nominerRepository->add($nominer);
            return $this->redirectToRoute('app_nominer_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('nominer/edit.html.twig', [
            'nominer' => $nominer,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_nominer_delete', methods: ['POST'])]
    public function delete(Request $request, Nominer $nominer, NominerRepository $nominerRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$nominer->getId(), $request->request->get('_token'))) {
            $nominerRepository->remove($nominer);
        }

        return $this->redirectToRoute('app_nominer_index', [], Response::HTTP_SEE_OTHER);
    }
}
