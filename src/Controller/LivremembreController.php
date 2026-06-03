<?php

namespace App\Controller;

use App\Entity\Livremembre;
use App\Form\LivremembreType;
use App\Repository\LivremembreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/livremembre')]
class LivremembreController extends AbstractController
{
    #[Route('/', name: 'app_livremembre_index', methods: ['GET'])]
    public function index(LivremembreRepository $livremembreRepository): Response
    {
        return $this->render('livremembre/index.html.twig', [
            'livremembres' => $livremembreRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_livremembre_new', methods: ['GET', 'POST'])]
    public function new(Request $request, LivremembreRepository $livremembreRepository): Response
    {
        $livremembre = new Livremembre();
        $form = $this->createForm(LivremembreType::class, $livremembre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $livremembreRepository->add($livremembre);
            return $this->redirectToRoute('app_livremembre_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('livremembre/new.html.twig', [
            'livremembre' => $livremembre,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_livremembre_show', methods: ['GET'])]
    public function show(Livremembre $livremembre): Response
    {
        return $this->render('livremembre/show.html.twig', [
            'livremembre' => $livremembre,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_livremembre_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Livremembre $livremembre, LivremembreRepository $livremembreRepository): Response
    {
        $form = $this->createForm(LivremembreType::class, $livremembre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $livremembreRepository->add($livremembre);
            return $this->redirectToRoute('app_livremembre_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('livremembre/edit.html.twig', [
            'livremembre' => $livremembre,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_livremembre_delete', methods: ['POST'])]
    public function delete(Request $request, Livremembre $livremembre, LivremembreRepository $livremembreRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$livremembre->getId(), $request->request->get('_token'))) {
            $livremembreRepository->remove($livremembre);
        }

        return $this->redirectToRoute('app_livremembre_index', [], Response::HTTP_SEE_OTHER);
    }
}
