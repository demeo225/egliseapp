<?php

namespace App\Controller;

use App\Entity\Invitegroupe;
use App\Form\InvitegroupeType;
use App\Repository\GroupeRepository;
use App\Repository\InvitegroupeRepository;
use App\Repository\SeancegroupeRepository;
use App\Traits\ClientIp;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/invitegroupe')]
class InvitegroupeController extends AbstractController {

    use ClientIp;

    #[Route('/', name: 'app_invitegroupe_index', methods: ['GET'])]
    public function index(InvitegroupeRepository $invitegroupeRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_GROUPE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $user = $this->getUser();
        $eglise = $this->getUser()->getEglise();
        $invitegroupe = $invitegroupeRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        return $this->render('invitegroupe/index.html.twig', [
                    'invitegroupes' => $invitegroupe,
        ]);
    }

   // #[Route('/{id}/edit', name: 'app_invitegroupe_edit', methods: ['GET', 'POST'])]
    #[Route('/new', name: 'app_invitegroupe_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SeancegroupeRepository $seancegroupeRepository, GroupeRepository $groupeRepo, ?Invitegroupe $invitegroupe): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_GROUPE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $user = $this->getUser();
            $cellule = $groupeRepo->findOneByUser($user);
         if (!$cellule) {
            $this->addFlash('warning', 'Vous ne disposez pas de sous-groupe à gérer.');
            return $this->redirectToRoute('app_invitegroupe_index');
        }
        $eglise = $this->getUser()->getEglise();
        $type = $invitegroupe === null ? 'new' : 'edit';
                $invitegroupe = $invitegroupe === null ? new Invitegroupe() : $invitegroupe;

        
        $groupe = $$groupeRepo->findOneByUser($user);
        $seancegroupe = $seancegroupeRepository->findBy(["groupe" => $groupe, "deletedAt" => NULL]);
        $form = $this->createForm(InvitegroupeType::class, $invitegroupe, ['seancegroupe' => $seancegroupe]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //Adresse ip de l'utilisateur
            if ($type === 'new') {
                $invitegroupe->setCreatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setEglise($user->getEglise())
                        ->setCreatedBy($user)
                ;
            } else {
                $invitegroupe->setUpdatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setUpdatedBy($user)
                ;
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($invitegroupe);
            $entityManager->flush();

            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'app_invitegroupe_new' : 'app_invitegroupe_index';
            if ($nextAction) {
                $this->addFlash('success', 'Enregistrement avec succès.');
            }

            return $this->redirectToRoute($nextAction);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('invitegroupe/new.html.twig', [
                    'invitegroupe' => $invitegroupe,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    
    
    
    
    #[Route('/{id}/edit', name: 'app_invitegroupe_edit', methods: ['GET', 'POST'])]
   // #[Route('/new', name: 'app_invitegroupe_new', methods: ['GET', 'POST'])]
    public function editInvite(Request $request, SeancegroupeRepository $seancegroupeRepository, GroupeRepository $groupeRepo, Invitegroupe $invitegroupe): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_GROUPE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $user = $this->getUser();
        $eglise = $this->getUser()->getEglise();
  
        
        $groupe = $groupeRepo->findOneByUser($user);
        $seancegroupe = $seancegroupeRepository->findBy(["groupe" => $groupe, "deletedAt" => NULL]);
        $form = $this->createForm(InvitegroupeType::class, $invitegroupe, ['seancegroupe' => $seancegroupe]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //Adresse ip de l'utilisateur
          
                $invitegroupe->setUpdatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setUpdatedBy($user)
                ;
          
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($invitegroupe);
            $entityManager->flush();

        
                $this->addFlash('success', 'Modification avec succès.');
            

            return $this->redirectToRoute('app_invitegroupe_index');
        }
        return $this->render('invitegroupe/edit.html.twig', [
                    'invitegroupe' => $invitegroupe,
                    'form' => $form->createView(),
                        ]);
    }
    
    #[Route('invitegroupe/{id}', name: 'app_invitegroupe_show', methods: ['GET'])]
    public function show(Invitegroupe $invitegroupe): Response {
           if (!$this->isGranted('ROLE_RESPONSABLE_GROUPE')) {
                throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
            }
        return $this->render('invitegroupe/show.html.twig', [
                    'invitegroupe' => $invitegroupe,
        ]);
    }

//    #[Route('/{id}/edit', name: 'app_invitegroupe_edit', methods: ['GET', 'POST'])]
//    public function edit(Request $request, Invitegroupe $invitegroupe, InvitegroupeRepository $invitegroupeRepository): Response {
//        $form = $this->createForm(InvitegroupeType::class, $invitegroupe);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $invitegroupeRepository->add($invitegroupe);
//            return $this->redirectToRoute('app_invitegroupe_index', [], Response::HTTP_SEE_OTHER);
//        }
//
//        return $this->render('invitegroupe/edit.html.twig', [
//                    'invitegroupe' => $invitegroupe,
//                    'form' => $form->createView(),
//        ]);
//    }

    #[Route('groupe/{id}', name: 'app_invitegroupe_delete', methods: ['POST'])]
    public function delete(Request $request, Invitegroupe $invitegroupe): Response {
        if ($this->isCsrfTokenValid('delete' . $invitegroupe->getId(), $request->request->get('_token'))) {
            if (!$this->isGranted('ROLE_RESPONSABLE_GROUPE')) {
                throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
            }
            $entityManager = $this->getDoctrine()->getManager();

            $invitegroupe->setDeletedFromIp($this->GetIp());
            $invitegroupe->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $invitegroupe->setDeletedBy($user);
            $this->addFlash('danger', 'Suppression avec succès.');
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_invitegroupe_index', [], Response::HTTP_SEE_OTHER);
    }

}
