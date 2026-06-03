<?php

namespace App\Controller;

use App\Entity\Presenceculte;
use App\Form\PresenceculteType;
use App\Repository\CulteRepository;
use App\Repository\FideleRepository;
use App\Repository\PresenceculteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Traits\ClientIp;


#[Route('/presenceculte')]

class PresenceculteController extends AbstractController {
   use ClientIp;
    
    #[Route('/', name: 'app_presenceculte_index', methods: ['GET'])]
    public function index(PresenceculteRepository $presenceculteRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $presenceculte = $presenceculteRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);

        return $this->render('presenceculte/index.html.twig', [
                   'presenceculte' => $presenceculte,
        ]);
    }

    #[Route('/new', name: 'app_presenceculte_new', methods: ['GET', 'POST'])]

    public function new(Request $request, PresenceculteRepository $presenceculteRepository, CulteRepository $culteRepository, FideleRepository $fideleRepository): Response {
          if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $fidele = $fideleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL, "etatfidele" => 1]);
        $culte = $culteRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $presenceculte = new Presenceculte();
        $form = $this->createForm(PresenceculteType::class, $presenceculte, ['culte' => $culte, 'fidele' => $fidele]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $presenceculteRepository->add($presenceculte);
            return $this->redirectToRoute('app_presenceculte_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('presenceculte/new.html.twig', [
                    'presenceculte' => $presenceculte,
                    'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_presenceculte_show', methods: ['GET'])]

    public function show(Presenceculte $presenceculte): Response {
           if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        return $this->render('presenceculte/show.html.twig', [
                    'presenceculte' => $presenceculte,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_presenceculte_edit', methods: ['GET', 'POST'])]

    public function edit(Request $request, Presenceculte $presenceculte, PresenceculteRepository $presenceculteRepository): Response {
         if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $form = $this->createForm(PresenceculteType::class, $presenceculte);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $presenceculteRepository->add($presenceculte);
            return $this->redirectToRoute('app_presenceculte_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('presenceculte/edit.html.twig', [
                    'presenceculte' => $presenceculte,
                    'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_presenceculte_delete', methods: ['POST'])]

    public function delete(Request $request, Presenceculte $presenceculte, PresenceculteRepository $presenceculteRepository): Response {
           if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($this->isCsrfTokenValid('delete' . $presenceculte->getId(), $request->request->get('_token'))) {
            $presenceculteRepository->remove($presenceculte);
        }

        return $this->redirectToRoute('app_presenceculte_index', [], Response::HTTP_SEE_OTHER);
    }

}
