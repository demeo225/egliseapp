<?php

namespace App\Controller;

use App\Entity\Presencecellule;
use App\Form\PresencecelluleType;
use App\Repository\CelluleRepository;
use App\Repository\FideleRepository;
use App\Repository\PresencecelluleRepository;
use App\Repository\SeancecelluleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Traits\ClientIp;


#[Route('/presencecellule')]
class PresencecelluleController extends AbstractController
{
    use ClientIp;
    
    #[Route('/', name: 'app_presencecellule_index', methods: ['GET'])]
    public function index(PresencecelluleRepository $presencecelluleRepository): Response
    {
        return $this->render('presencecellule/index.html.twig', [
            'presencecellules' => $presencecelluleRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_presencecellule_new', methods: ['GET', 'POST'])]
    public function new(Request $request, PresencecelluleRepository $presencecelluleRepository, SeancecelluleRepository $seancecelluleRepository, CelluleRepository $celluleRepository, FideleRepository $fideleRepository): Response
    {
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $fidele = $fideleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL, "etatfidele" => 1]);
        $cellule = $celluleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $seancecellule = $seancecelluleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $presencecellule = new Presencecellule();
        $form = $this->createForm(PresencecelluleType::class, $presencecellule, ['seancecellule' => $seancecellule, 'cellule' => $cellule, 'fidele' => $fidele]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            
    
            $presencecellule->setCreatedFromIp($this->GetIp());
            $user = $this->getUser();
            $eglise = $this->getUser()->getEglise();
            $presencecellule->setCreatedBy($user);
            $presencecellule->setEglise($eglise);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($presencecellule);
            $entityManager->flush();
            $presencecelluleRepository->add($presencecellule);
             $this->addFlash('success', 'Enregistrement effectué avec succès.');
            return $this->redirectToRoute('app_presencecellule_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('presencecellule/new.html.twig', [
            'presencecellule' => $presencecellule,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_presencecellule_show', methods: ['GET'])]
    public function show(Presencecellule $presencecellule): Response
    {
        return $this->render('presencecellule/show.html.twig', [
            'presencecellule' => $presencecellule,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_presencecellule_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Presencecellule $presencecellule, PresencecelluleRepository $presencecelluleRepository): Response
    {
        $form = $this->createForm(PresencecelluleType::class, $presencecellule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $presencecelluleRepository->add($presencecellule);
            return $this->redirectToRoute('app_presencecellule_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('presencecellule/edit.html.twig', [
            'presencecellule' => $presencecellule,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_presencecellule_delete', methods: ['POST'])]
    public function delete(Request $request, Presencecellule $presencecellule, PresencecelluleRepository $presencecelluleRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$presencecellule->getId(), $request->request->get('_token'))) {
            $presencecelluleRepository->remove($presencecellule);
        }

        return $this->redirectToRoute('app_presencecellule_index', [], Response::HTTP_SEE_OTHER);
    }
}
