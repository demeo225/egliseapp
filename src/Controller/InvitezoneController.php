<?php

namespace App\Controller;

use App\Entity\Invitezone;
use App\Form\InvitezoneType;
use App\Repository\ZoneRepository;
use App\Repository\InvitezoneRepository;
use App\Repository\SeancezoneRepository;
use App\Traits\ClientIp;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/invitezone')]
class InvitezoneController extends AbstractController {

    use ClientIp;

    #[Route('/', name: 'app_invitezone_index', methods: ['GET'])]
    public function index(InvitezoneRepository $invitezoneRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        $user = $this->getUser();
        $eglise = $user->getEglise();
        
        // Récupérer tous les invités
        $allInvites = $invitezoneRepository->findBy([
            'eglise' => $eglise,
            'deletedAt' => NULL
        ], ['id' => 'DESC']);
        
        // Filtrer selon les droits via le Voter
        $invites = [];
        foreach ($allInvites as $invite) {
            if ($this->isGranted('invitezone_view', $invite)) {
                $invites[] = $invite;
            }
        }
        
        return $this->render('invitezone/index.html.twig', [
            'invitezones' => $invites,
        ]);
    }

   // #[Route('/{id}/edit', name: 'app_invitezone_edit', methods: ['GET', 'POST'])]
    #[Route('/new', name: 'app_invitezone_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SeancezoneRepository $seancezoneRepository, ZoneRepository $zoneRepo, ?Invitezone $invitezone): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_ZONE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
           $user = $this->getUser();
            $zone = $zoneRepo->findOneByUser($user);
  
        if (!$zone) {
            $this->addFlash('warning', 'Vous ne disposez pas de zone / secteur à gérer.');
            return $this->redirectToRoute('app_invitezone_index');
        }
        $eglise = $this->getUser()->getEglise();
        $type = $invitezone === null ? 'new' : 'edit';
                $invitezone = $invitezone === null ? new Invitezone() : $invitezone;
        $zone = $zoneRepo->findOneByUser($user);
        $seancezone = $seancezoneRepository->findBy(["zone" => $zone, "deletedAt" => NULL], ["id" => "DESC"]);
        $form = $this->createForm(InvitezoneType::class, $invitezone, ['seancezone' => $seancezone]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //Adresse ip de l'utilisateur
            if ($type === 'new') {
                $invitezone->setCreatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setEglise($user->getEglise())
                        ->setCreatedBy($user)
                ;
            } else {
                $invitezone->setUpdatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setUpdatedBy($user)
                ;
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($invitezone);
            $entityManager->flush();
            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'app_invitezone_new' : 'app_invitezone_index';
            if ($nextAction) {
                $this->addFlash('success', 'Enregistrement avec succès.');
            }

            return $this->redirectToRoute($nextAction);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('invitezone/new.html.twig', [
                    'invitezone' => $invitezone,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    
    
    
    
    #[Route('/{id}/edit', name: 'app_invitezone_edit', methods: ['GET', 'POST'])]
   // #[Route('/new', name: 'app_invitezone_new', methods: ['GET', 'POST'])]
    public function editInvite(Request $request, SeancezoneRepository $seancezoneRepository, ZoneRepository $zoneRepo, Invitezone $invitezone): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_ZONE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $user = $this->getUser();
        $eglise = $this->getUser()->getEglise();
  
        
          $zone = $zoneRepo->findOneByUser($user);
        $seancezone = $seancezoneRepository->findBy(["zone" => $zone, "deletedAt" => NULL], ["id" => "DESC"]);
        $form = $this->createForm(InvitezoneType::class, $invitezone, ['seancezone' => $seancezone]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //Adresse ip de l'utilisateur
          
                $invitezone->setUpdatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setUpdatedBy($user)
                ;
          
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($invitezone);
            $entityManager->flush();

        
                $this->addFlash('success', 'Modification avec succès.');
            

            return $this->redirectToRoute('app_invitezone_index');
        }
        return $this->render('invitezone/edit.html.twig', [
                    'invitezone' => $invitezone,
                    'form' => $form->createView(),
                        ]);
    }
    
    #[Route('invitezone/{id}', name: 'app_invitezone_show', methods: ['GET'])]
    public function show(Invitezone $invitezone): Response {
           if (!$this->isGranted('ROLE_RESPONSABLE_ZONE')) {
                throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
            }
        return $this->render('invitezone/show.html.twig', [
                    'invitezone' => $invitezone,
        ]);
    }

//    #[Route('/{id}/edit', name: 'app_invitezone_edit', methods: ['GET', 'POST'])]
//    public function edit(Request $request, Invitezone $invitezone, InvitezoneRepository $invitezoneRepository): Response {
//        $form = $this->createForm(InvitezoneType::class, $invitezone);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $invitezoneRepository->add($invitezone);
//            return $this->redirectToRoute('app_invitezone_index', [], Response::HTTP_SEE_OTHER);
//        }
//
//        return $this->render('invitezone/edit.html.twig', [
//                    'invitezone' => $invitezone,
//                    'form' => $form->createView(),
//        ]);
//    }

    #[Route('/{id}', name: 'app_invitezone_delete', methods: ['POST'])]
    public function delete(Request $request, Invitezone $invitezone): Response {
        if ($this->isCsrfTokenValid('delete' . $invitezone->getId(), $request->request->get('_token'))) {
            if (!$this->isGranted('ROLE_RESPONSABLE_ZONE')) {
                throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
            }
            $entityManager = $this->getDoctrine()->getManager();

            $invitezone->setDeletedFromIp($this->GetIp());
            $invitezone->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $invitezone->setDeletedBy($user);
            $this->addFlash('danger', 'Suppression avec succès.');
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_invitezone_index', [], Response::HTTP_SEE_OTHER);
    }

}
