<?php

namespace App\Controller;

use App\Entity\Couple;
use App\Form\CoupleType;
use App\Repository\CoupleRepository;
use App\Repository\FideleRepository;
use DateTime;
use App\Traits\ClientIp;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/couple')]

class CoupleController extends AbstractController {
    use ClientIp;
    
    #[Route('/', name: 'couple_index', methods: ['GET'])]
    public function index(CoupleRepository $coupleRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_CONJUGAL')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();

        $user = $this->getUser();
        $couple = $coupleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        return $this->render('couple/index.html.twig', [
                    'couples' => $couple,
        ]);
    }

    #[Route('/new', name: 'couple_new', methods: ['GET', 'POST'])]

    public function new(Request $request, CoupleRepository $coupleRepository, FideleRepository $fideleRepository): Response {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_CONJUGAL')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $couple = new Couple();
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $epoux = $fideleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL, "sexe" => 'Homme', "etatmariage" => 1]);
        $epouse = $fideleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL, "sexe" => 'Femme', "etatmariage" => 1]);
        $form = $this->createForm(CoupleType::class, $couple, ['epoux' => $epoux, 'epouse' => $epouse]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //Adresse ip de l'utilisateur

            $couple->setCreatedFromIp($this->GetIp());

            $eglise = $this->getUser()->getEglise();
            $user = $this->getUser();
            $couple->setCreatedBy($user);
            $couple->setEglise($eglise);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($couple);
            $entityManager->flush();

            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'couple_new' : 'couple_index';
            if ($nextAction) {
                $this->addFlash('couple', 'Enregistrement avec succès.');
            }

            return $this->redirectToRoute($nextAction);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('couple/new.html.twig', [
                    'couple' => $couple,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    #[Route('/{id}', name: 'couple_show', methods: ['GET'])]

    public function show(Couple $couple): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_CONJUGAL')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        return $this->render('couple/show.html.twig', [
                    'couple' => $couple,
        ]);
    }

    #[Route('/{id}/edit', name: 'couple_edit', methods: ['GET', 'POST'])]

    public function edit(Request $request, Couple $couple, FideleRepository $fideleRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_CONJUGAL')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $epoux = $fideleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL, "sexe" => 'Homme', "statutmatri" => 'Marié(e)']);
        $epouse = $fideleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL, "sexe" => 'Femme', "statutmatri" => 'Marié(e)']);
        $form = $this->createForm(CoupleType::class, $couple, ['epoux' => $epoux, 'epouse' => $epouse]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $couple->setUpdatedFromIp($this->GetIp());


            $user = $this->getUser();
            $couple->setUpdatedBy($user);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('couple_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('couple/edit.html.twig', [
                    'couple' => $couple,
                    'form' => $form->createView(),
        ]);
    }


    #[Route('couple/{id}', name: 'couple_delete', methods: ['POST'])]
    public function delete(Request $request, Couple $couple): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici! Veuillez vous adresser au secretariat');
        }
        if ($this->isCsrfTokenValid('delete' . $couple->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();



            $couple->setDeletedFromIp($this->GetIp());
            $couple->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $couple->setDeletedBy($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('couple_index');
    }

}
