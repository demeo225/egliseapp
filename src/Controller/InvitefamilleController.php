<?php

namespace App\Controller;

use App\Entity\Invitefamille;
use App\Form\InvitefamilleType;
use App\Repository\FamilleRepository;
use App\Repository\InvitefamilleRepository;
use App\Repository\SeancefamilleRepository;
use App\Traits\ClientIp;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/invitefamille')]
class InvitefamilleController extends AbstractController {

    use ClientIp;

    #[Route('/', name: 'app_invitefamille_index', methods: ['GET'])]
    public function index(InvitefamilleRepository $invitefamilleRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_FAMILLE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $user = $this->getUser();
        $eglise = $this->getUser()->getEglise();
        $invitefamille = $invitefamilleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        return $this->render('invitefamille/index.html.twig', [
                    'invitefamilles' => $invitefamille,
        ]);
    }

   // #[Route('/{id}/edit', name: 'app_invitefamille_edit', methods: ['GET', 'POST'])]
    #[Route('/new', name: 'app_invitefamille_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SeancefamilleRepository $seancefamilleRepository, FamilleRepository $familleRepo, ?Invitefamille $invitefamille): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_FAMILLE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $user = $this->getUser();
            $cellule = $familleRepo->findOneByUser($user);
         if (!$cellule) {
            $this->addFlash('warning', 'Vous ne disposez pas de famille à gérer.');
            return $this->redirectToRoute('app_invitefamille_index');
        }
        $eglise = $this->getUser()->getEglise();
        $type = $invitefamille === null ? 'new' : 'edit';
                $invitefamille = $invitefamille === null ? new Invitefamille() : $invitefamille;

        
        $famille = $familleRepo->findOneByUser($user);
        $seancefamille = $seancefamilleRepository->findBy(["famille" => $famille, "deletedAt" => NULL], ["id" => "DESC"]);
        $form = $this->createForm(InvitefamilleType::class, $invitefamille, ['seancefamille' => $seancefamille]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $famille = $familleRepo->findOneByUser($user);
            //Adresse ip de l'utilisateur
            if ($type === 'new') {
                $invitefamille->setCreatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setEglise($user->getEglise())
                       // ->setFamille($famille)
                        ->setCreatedBy($user)
                ;
            } else {
                $invitefamille->setUpdatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setUpdatedBy($user)
                ;
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($invitefamille);
            $entityManager->flush();

            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'app_invitefamille_new' : 'app_invitefamille_index';
            if ($nextAction) {
                $this->addFlash('success', 'Enregistrement avec succès.');
            }

            return $this->redirectToRoute($nextAction);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('invitefamille/new.html.twig', [
                    'invitefamille' => $invitefamille,
                    'famille' => $user->getFamille(),
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    
    
    
    
    #[Route('/{id}/edit', name: 'app_invitefamille_edit', methods: ['GET', 'POST'])]
   // #[Route('/new', name: 'app_invitefamille_new', methods: ['GET', 'POST'])]
    public function editInvite(Request $request, SeancefamilleRepository $seancefamilleRepository, FamilleRepository $familleRepo, Invitefamille $invitefamille): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_FAMILLE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $user = $this->getUser();
        $eglise = $this->getUser()->getEglise();
  
        
        $famille = $familleRepo->findOneByUser($user);
        $seancefamille = $seancefamilleRepository->findBy(["famille" => $famille, "deletedAt" => NULL]);
        $form = $this->createForm(InvitefamilleType::class, $invitefamille, ['seancefamille' => $seancefamille]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //Adresse ip de l'utilisateur
          
                $invitefamille->setUpdatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setUpdatedBy($user)
                ;
          
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($invitefamille);
            $entityManager->flush();

        
                $this->addFlash('success', 'Modification avec succès.');
            

            return $this->redirectToRoute('app_invitefamille_index');
        }
        return $this->render('invitefamille/edit.html.twig', [
                    'invitefamille' => $invitefamille,
                    'form' => $form->createView(),
                        ]);
    }
    
    #[Route('invitefamille/{id}', name: 'app_invitefamille_show', methods: ['GET'])]
    public function show(Invitefamille $invitefamille): Response {
           if (!$this->isGranted('ROLE_RESPONSABLE_FAMILLE')) {
                throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
            }
        return $this->render('invitefamille/show.html.twig', [
                    'invitefamille' => $invitefamille,
        ]);
    }

//    #[Route('/{id}/edit', name: 'app_invitefamille_edit', methods: ['GET', 'POST'])]
//    public function edit(Request $request, Invitefamille $invitefamille, InvitefamilleRepository $invitefamilleRepository): Response {
//        $form = $this->createForm(InvitefamilleType::class, $invitefamille);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $invitefamilleRepository->add($invitefamille);
//            return $this->redirectToRoute('app_invitefamille_index', [], Response::HTTP_SEE_OTHER);
//        }
//
//        return $this->render('invitefamille/edit.html.twig', [
//                    'invitefamille' => $invitefamille,
//                    'form' => $form->createView(),
//        ]);
//    }

    #[Route('/{id}', name: 'app_invitefamille_delete', methods: ['POST'])]
    public function delete(Request $request, Invitefamille $invitefamille): Response {
        if ($this->isCsrfTokenValid('delete' . $invitefamille->getId(), $request->request->get('_token'))) {
            if (!$this->isGranted('ROLE_RESPONSABLE_FAMILLE')) {
                throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
            }
            $entityManager = $this->getDoctrine()->getManager();

            $invitefamille->setDeletedFromIp($this->GetIp());
            $invitefamille->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $invitefamille->setDeletedBy($user);
            $this->addFlash('danger', 'Suppression avec succès.');
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_invitefamille_index', [], Response::HTTP_SEE_OTHER);
    }

}
