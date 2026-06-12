<?php

namespace App\Controller;

use App\Entity\Invitecellule;
use App\Form\InvitecelluleType;
use App\Repository\CelluleRepository;
use App\Repository\InvitecelluleRepository;
use App\Repository\SeancecelluleRepository;
use App\Traits\ClientIp;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/invitecellule')]
class InvitecelluleController extends AbstractController {

    use ClientIp;

    #[Route('/', name: 'app_invitecellule_index', methods: ['GET'])]
    public function index(InvitecelluleRepository $invitecelluleRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_CELLULE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $user = $this->getUser();
        $eglise = $this->getUser()->getEglise();
        $invitecellule = $invitecelluleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        return $this->render('invitecellule/index.html.twig', [
                    'invitecellules' => $invitecellule,
        ]);
    }

   // #[Route('/{id}/edit', name: 'app_invitecellule_edit', methods: ['GET', 'POST'])]
    #[Route('/new', name: 'app_invitecellule_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SeancecelluleRepository $seancecelluleRepository, CelluleRepository $celluleRepo, ?Invitecellule $invitecellule): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_CELLULE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $user = $this->getUser(); 
            $cellule = $celluleRepo->findOneByUser($user);
         if (!$cellule) {
            $this->addFlash('warning', 'Vous ne disposez pas de cellule à gérer.');
            return $this->redirectToRoute('app_invitecellule_index');
        }
        $eglise = $this->getUser()->getEglise();
        $type = $invitecellule === null ? 'new' : 'edit';
                $invitecellule = $invitecellule === null ? new Invitecellule() : $invitecellule;

        
        $cellule = $celluleRepo->findOneByUser($user);
        $seancecellule = $seancecelluleRepository->findBy(["cellule" => $cellule, "deletedAt" => NULL], ["id" => "DESC"]);
        $form = $this->createForm(InvitecelluleType::class, $invitecellule, ['seancecellule' => $seancecellule]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //Adresse ip de l'utilisateur
            if ($type === 'new') {
                $invitecellule->setCreatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setEglise($user->getEglise())
                        ->setCreatedBy($user)
                ;
            } else {
                $invitecellule->setUpdatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setUpdatedBy($user)
                ;
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($invitecellule);
            $entityManager->flush();

            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'app_invitecellule_new' : 'app_invitecellule_index';
            if ($nextAction) {
                $this->addFlash('success', 'Enregistrement avec succès.');
            }

            return $this->redirectToRoute($nextAction);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('invitecellule/new.html.twig', [
                    'invitecellule' => $invitecellule,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    
    
    
    
    #[Route('/{id}/edit', name: 'app_invitecellule_edit', methods: ['GET', 'POST'])]
   // #[Route('/new', name: 'app_invitecellule_new', methods: ['GET', 'POST'])]
    public function editInvite(Request $request, SeancecelluleRepository $seancecelluleRepository, CelluleRepository $celluleRepo, Invitecellule $invitecellule): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_CELLULE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $user = $this->getUser();
        $eglise = $this->getUser()->getEglise();
  
        
        $cellule = $celluleRepo->findOneByUser($user);
        $seancecellule = $seancecelluleRepository->findBy(["cellule" => $cellule, "deletedAt" => NULL]);
        $form = $this->createForm(InvitecelluleType::class, $invitecellule, ['seancecellule' => $seancecellule]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //Adresse ip de l'utilisateur
          
                $invitecellule->setUpdatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setUpdatedBy($user)
                ;
          
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($invitecellule);
            $entityManager->flush();

        
                $this->addFlash('success', 'Modification avec succès.');
            

            return $this->redirectToRoute('app_invitecellule_index');
        }
        return $this->render('invitecellule/edit.html.twig', [
                    'invitecellule' => $invitecellule,
                    'form' => $form->createView(),
                        ]);
    }
    
    #[Route('invitecellule/{id}', name: 'app_invitecellule_show', methods: ['GET'])]
    public function show(Invitecellule $invitecellule): Response {
           if (!$this->isGranted('ROLE_RESPONSABLE_CELLULE')) {
                throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
            }
        return $this->render('invitecellule/show.html.twig', [
                    'invitecellule' => $invitecellule,
        ]);
    }

//    #[Route('/{id}/edit', name: 'app_invitecellule_edit', methods: ['GET', 'POST'])]
//    public function edit(Request $request, Invitecellule $invitecellule, InvitecelluleRepository $invitecelluleRepository): Response {
//        $form = $this->createForm(InvitecelluleType::class, $invitecellule);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $invitecelluleRepository->add($invitecellule);
//            return $this->redirectToRoute('app_invitecellule_index', [], Response::HTTP_SEE_OTHER);
//        }
//
//        return $this->render('invitecellule/edit.html.twig', [
//                    'invitecellule' => $invitecellule,
//                    'form' => $form->createView(),
//        ]);
//    }

    #[Route('celulle/{id}', name: 'app_invitecellule_delete', methods: ['POST'])]
    public function delete(Request $request, Invitecellule $invitecellule): Response {
        if ($this->isCsrfTokenValid('delete' . $invitecellule->getId(), $request->request->get('_token'))) {
            if (!$this->isGranted('ROLE_RESPONSABLE_CELLULE')) {
                throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
            }
            $entityManager = $this->getDoctrine()->getManager();

            $invitecellule->setDeletedFromIp($this->GetIp());
            $invitecellule->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $invitecellule->setDeletedBy($user);
            $this->addFlash('danger', 'Suppression avec succès.');
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_invitecellule_index', [], Response::HTTP_SEE_OTHER);
    }

}
