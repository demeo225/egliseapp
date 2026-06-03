<?php

namespace App\Controller;

use App\Entity\Timbre;
use App\Form\TimbreType;
use App\Repository\CartemembreRepository;
use App\Repository\TimbreRepository;
use App\Traits\ClientIp;
use DateTime;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/timbre')]
class TimbreController extends AbstractController
{
    use ClientIp;

    #[Route('/', name: 'app_timbre_index', methods: ['GET'])]
    public function index(TimbreRepository $timbreRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
      
        $timbre = $timbreRepository->findBy(['eglise' => $eglise, 'deletedAt' => NULL]);
        return $this->render('timbre/index.html.twig', [
                    'timbres' => $timbre,
        ]);
    }

    #[Route('/new', name: 'app_timbre_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CartemembreRepository $cartemembreRepository ,TimbreRepository $timbreRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $eglise = $this->getUser()->getEglise();
        $cartemembre = $cartemembreRepository->findBy(['eglise'=> $eglise,]);
        $timbre = new Timbre();
        $form = $this->createForm(TimbreType::class, $timbre, [ 'cartemembre'=> $cartemembre, ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //return $this->redirectToRoute('app_timbre_index', [], Response::HTTP_SEE_OTHER);
       
            $timbre->setCreatedFromIp($this->GetIp());
        
            $eglise = $this->getUser()->getEglise();
            $user = $this->getUser();
            $timbre->setCreatedBy($user);
            $timbre->setCreatedAt(new DateTime("now"));
            
            $timbre->setCreatedFromIp($this->GetIp());
            $timbre->setEglise($eglise);
            $timbre->setIdeglise($user->getEglise()->GetId());
            $timbreRepository->add($timbre);

        $nextAction = $form->get('saveAndAdd')->isClicked() ? 'app_timbre_new' : 'app_timbre_index';
        if ($nextAction) {
            $this->addFlash('success', 'Enregistrement avec succès.');
        }

        return $this->redirectToRoute($nextAction);
    }
    $response = new Response(null, $form->isSubmitted() ? 422 : 200);
    return $this->render('timbre/new.html.twig', [
                'timbre' => $timbre,
                'form' => $form->createView(),
                'response' => $response,
                    ], $response);
    }

    #[Route('/{id}', name: 'app_timbre_show', methods: ['GET'])]
    public function show(Timbre $timbre): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        return $this->render('timbre/show.html.twig', [
            'timbre' => $timbre,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_timbre_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Timbre $timbre, CartemembreRepository $cartemembreRepository , TimbreRepository $timbreRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $eglise = $this->getUser()->getEglise();
        $cartemembre = $cartemembreRepository->findBy(['eglise'=> $eglise,]);
        $timbre = new Timbre();
        $form = $this->createForm(TimbreType::class, $timbre, [ 'cartemembre'=> $cartemembre, ]);
        $form->handleRequest($request);

    
        if ($form->isSubmitted() && $form->isValid()) {

            //return $this->redirectToRoute('app_timbre_index', [], Response::HTTP_SEE_OTHER);
       
            $timbre->setCreatedFromIp($this->GetIp());
                   $user = $this->getUser();
            $timbre->setUpdatedBy($user);
            $timbre->setUpdatedAt(new DateTime("now"));

            $timbre->setUpdatedFromIp($this->GetIp());
            $timbreRepository->add($timbre);

        $nextAction = $form->get('saveAndAdd')->isClicked() ? 'app_timbre_new' : 'app_timbre_index';
        if ($nextAction) {
            $this->addFlash('success', 'Enregistrement avec succès.');
        }

        return $this->redirectToRoute($nextAction);
    }
    $response = new Response(null, $form->isSubmitted() ? 422 : 200);
    return $this->render('timbre/new.html.twig', [
                'timbre' => $timbre,
                'form' => $form->createView(),
                'response' => $response,
                    ], $response);
    }

    #[Route('/{id}', name: 'app_timbre_delete', methods: ['POST'])]
    public function delete(Request $request, Timbre $timbre, EntityManager $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if ($this->isCsrfTokenValid('delete'.$timbre->getId(), $request->request->get('_token'))) {
          
            $timbre->setDeletedFromIp($this->GetIp());
            $timbre->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $timbre->setDeletedBy($user);
            $this->addFlash('danger', 'Suppression avec succès.');
            $entityManager->flush();

        }

        return $this->redirectToRoute('app_timbre_index', [], Response::HTTP_SEE_OTHER);
    }
}
