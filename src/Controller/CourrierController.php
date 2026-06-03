<?php

namespace App\Controller;

use App\Entity\Courrier;
use App\Form\CourrierType;
use App\Repository\CourrierRepository;
use App\Service\FileUploader;
use App\Traits\ClientIp;
use DateTime;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/courrier')]
class CourrierController extends AbstractController
{
    use ClientIp;

    #[Route('/', name: 'app_courrier_index', methods: ['GET'])]
    public function index(CourrierRepository $courrierRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
      
        $courrier = $courrierRepository->findBy(['eglise' => $eglise, 'deletedAt' => NULL]);
        return $this->render('courrier/index.html.twig', [
                    'courriers' => $courrier,
        ]);
    }

    #[Route('/new', name: 'app_courrier_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CourrierRepository $courrierRepository, FileUploader $fileUploader): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $courrier = new Courrier();
        $form = $this->createForm(CourrierType::class, $courrier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //return $this->redirectToRoute('app_courrier_index', [], Response::HTTP_SEE_OTHER);
       
            $courrier->setCreatedFromIp($this->GetIp());
            $brochureFile = $form->get('photo')->getData();
            if ($brochureFile) {
                $brochureFileName = $fileUploader->upload($brochureFile);
                $courrier->setPhoto($brochureFileName);
            }
            $eglise = $this->getUser()->getEglise();
            $user = $this->getUser();
            $courrier->setCreatedBy($user);
            $courrier->setCreatedAt(new DateTime("now"));
            
            $courrier->setCreatedFromIp($this->GetIp());
            $courrier->setEglise($eglise);
            $courrier->setIdeglise($user->getEglise()->GetId());
            $courrierRepository->add($courrier);

        $nextAction = $form->get('saveAndAdd')->isClicked() ? 'app_courrier_new' : 'app_courrier_index';
        if ($nextAction) {
            $this->addFlash('message', 'Enregistrement avec succès.');
        }

        return $this->redirectToRoute($nextAction);
    }
    $response = new Response(null, $form->isSubmitted() ? 422 : 200);
    return $this->render('courrier/new.html.twig', [
                'courrier' => $courrier,
                'form' => $form->createView(),
                'response' => $response,
                    ], $response);
    }

    #[Route('/{id}', name: 'app_courrier_show', methods: ['GET'])]
    public function show(Courrier $courrier): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        return $this->render('courrier/show.html.twig', [
            'courrier' => $courrier,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_courrier_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Courrier $courrier, CourrierRepository $courrierRepository, FileUploader $fileUploader): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $form = $this->createForm(CourrierType::class, $courrier);
        $form->handleRequest($request);

    
        if ($form->isSubmitted() && $form->isValid()) {

            //return $this->redirectToRoute('app_courrier_index', [], Response::HTTP_SEE_OTHER);
       
            $courrier->setCreatedFromIp($this->GetIp());
            $brochureFile = $form->get('photo')->getData();
            if ($brochureFile) {
                $brochureFileName = $fileUploader->upload($brochureFile);
                $courrier->setPhoto($brochureFileName);
            }
           
            $user = $this->getUser();
            $courrier->setUpdatedBy($user);
            $courrier->setUpdatedAt(new DateTime("now"));

            $courrier->setUpdatedFromIp($this->GetIp());
            $courrierRepository->add($courrier);

        $nextAction = $form->get('saveAndAdd')->isClicked() ? 'app_courrier_new' : 'app_courrier_index';
        if ($nextAction) {
            $this->addFlash('message', 'Enregistrement avec succès.');
        }

        return $this->redirectToRoute($nextAction);
    }
    $response = new Response(null, $form->isSubmitted() ? 422 : 200);
    return $this->render('courrier/new.html.twig', [
                'courrier' => $courrier,
                'form' => $form->createView(),
                'response' => $response,
                    ], $response);
    }

    #[Route('/{id}', name: 'app_courrier_delete', methods: ['POST'])]
    public function delete(Request $request, Courrier $courrier, EntityManager $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if ($this->isCsrfTokenValid('delete'.$courrier->getId(), $request->request->get('_token'))) {
          
            $courrier->setDeletedFromIp($this->GetIp());
            $courrier->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $courrier->setDeletedBy($user);
            $this->addFlash('message', 'Suppression avec succès.');
            $entityManager->flush();

        }

        return $this->redirectToRoute('app_courrier_index', [], Response::HTTP_SEE_OTHER);
    }
}
