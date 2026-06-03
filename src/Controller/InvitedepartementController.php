<?php

namespace App\Controller;

use App\Entity\Invitedepartement;
use App\Form\InvitedepartementType;
use App\Repository\DepartementRepository;
use App\Repository\InvitedepartementRepository;
use App\Repository\SeancedepartementRepository;
use App\Traits\ClientIp;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/invitedepartement')]
class InvitedepartementController extends AbstractController {

    use ClientIp;

    #[Route('/', name: 'app_invitedepartement_index', methods: ['GET'])]
    public function index(InvitedepartementRepository $invitedepartementRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_DEPARTEMENT')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $user = $this->getUser();
        $eglise = $this->getUser()->getEglise();
        $invitedepartement = $invitedepartementRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        return $this->render('invitedepartement/index.html.twig', [
                    'invitedepartements' => $invitedepartement,
        ]);
    }

   // #[Route('/{id}/edit', name: 'app_invitedepartement_edit', methods: ['GET', 'POST'])]
    #[Route('/new', name: 'app_invitedepartement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SeancedepartementRepository $seancedepartementRepository, DepartementRepository $departementRepo, ?Invitedepartement $invitedepartement): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_DEPARTEMENT')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $user = $this->getUser();
           $departement = $departementRepo->findOneByUser($user);
         if (!$departement) {
            $this->addFlash('warning', 'Vous ne disposez pas département à gérer.');
            return $this->redirectToRoute('app_invitedepartement_index');
        }
        $eglise = $this->getUser()->getEglise();
        $type = $invitedepartement === null ? 'new' : 'edit';
                $invitedepartement = $invitedepartement === null ? new Invitedepartement() : $invitedepartement;

        
        $departement = $departementRepo->findBy(['eglise' => $eglise, "user" => $user, "deletedAt" => NULL]);
        $seancedepartement = $seancedepartementRepository->findBy(["departement" => $departement, "deletedAt" => NULL]);
        $form = $this->createForm(InvitedepartementType::class, $invitedepartement, ['seancedepartement' => $seancedepartement]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //Adresse ip de l'utilisateur
            if ($type === 'new') {
                $invitedepartement->setCreatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setEglise($user->getEglise())
                        ->setCreatedBy($user)
                ;
            } else {
                $invitedepartement->setUpdatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setUpdatedBy($user)
                ;
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($invitedepartement);
            $entityManager->flush();

            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'app_invitedepartement_new' : 'app_invitedepartement_index';
            if ($nextAction) {
                $this->addFlash('success', 'Enregistrement avec succès.');
            }

            return $this->redirectToRoute($nextAction);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('invitedepartement/new.html.twig', [
                    'invitedepartement' => $invitedepartement,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    
    #[Route('/{id}/edit', name: 'app_invitedepartement_edit', methods: ['GET', 'POST'])]
   // #[Route('/new', name: 'app_invitedepartement_new', methods: ['GET', 'POST'])]
    public function editInvite(Request $request, SeancedepartementRepository $seancedepartementRepository, DepartementRepository $departementRepo, Invitedepartement $invitedepartement): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_DEPARTEMENT')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $user = $this->getUser();
        $eglise = $this->getUser()->getEglise();
  
        
        $departement = $departementRepo->findOneByUser($user);
        $seancedepartement = $seancedepartementRepository->findBy(["departement" => $departement, "deletedAt" => NULL]);
        $form = $this->createForm(InvitedepartementType::class, $invitedepartement, ['seancedepartement' => $seancedepartement]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //Adresse ip de l'utilisateur
          
                $invitedepartement->setUpdatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setUpdatedBy($user)
                ;
          
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($invitedepartement);
            $entityManager->flush();

        
                $this->addFlash('success', 'Modification avec succès.');
            

            return $this->redirectToRoute('app_invitedepartement_index');
        }
        return $this->render('invitedepartement/edit.html.twig', [
                    'invitedepartement' => $invitedepartement,
                    'form' => $form->createView(),
                        ]);
    }
    
    #[Route('invitedepartement/{id}', name: 'app_invitedepartement_show', methods: ['GET'])]
    public function show(Invitedepartement $invitedepartement): Response {
           if (!$this->isGranted('ROLE_RESPONSABLE_DEPARTEMENT')) {
                throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
            }
        return $this->render('invitedepartement/show.html.twig', [
                    'invitedepartement' => $invitedepartement,
        ]);
    }

//    #[Route('/{id}/edit', name: 'app_invitedepartement_edit', methods: ['GET', 'POST'])]
//    public function edit(Request $request, Invitedepartement $invitedepartement, InvitedepartementRepository $invitedepartementRepository): Response {
//        $form = $this->createForm(InvitedepartementType::class, $invitedepartement);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $invitedepartementRepository->add($invitedepartement);
//            return $this->redirectToRoute('app_invitedepartement_index', [], Response::HTTP_SEE_OTHER);
//        }
//
//        return $this->render('invitedepartement/edit.html.twig', [
//                    'invitedepartement' => $invitedepartement,
//                    'form' => $form->createView(),
//        ]);
//    }

    #[Route('/{id}', name: 'app_invitedepartement_delete', methods: ['POST'])]
    public function delete(Request $request, Invitedepartement $invitedepartement): Response {
        if ($this->isCsrfTokenValid('delete' . $invitedepartement->getId(), $request->request->get('_token'))) {
            if (!$this->isGranted('ROLE_RESPONSABLE_DEPARTEMENT')) {
                throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
            }
            $entityManager = $this->getDoctrine()->getManager();

            $invitedepartement->setDeletedFromIp($this->GetIp());
            $invitedepartement->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $invitedepartement->setDeletedBy($user);
            $this->addFlash('danger', 'Suppression avec succès.');
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_invitedepartement_index', [], Response::HTTP_SEE_OTHER);
    }

}
