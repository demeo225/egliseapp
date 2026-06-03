<?php

namespace App\Controller;

use App\Entity\Sms;
use App\Form\SmsType;
use App\Repository\SmsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Notifier\Message\SmsMessage;
use Symfony\Component\Notifier\TexterInterface;

#[Route('/sms')]

class SmsController extends AbstractController {
    #[Route('/', name: 'sms_index', methods: ['GET'])]

    public function index(SmsRepository $smsRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $roles = $user->getRoles();
        $sms = $smsRepository->findByEglise($eglise);
        return $this->render('sms/index.html.twig', [
                    'sms' => $sms,
        ]);

        return $this->render('sms/index.html.twig', [
                    'sms' => $smsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'sms_new', methods: ['GET', 'POST'])]

    public function new(Request $request): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $sms = new Sms();
        $form = $this->createForm(SmsType::class, $sms);
        '+2250757000748';
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $eglise = $this->getUser()->getEglise();
            $user = $this->getUser();
            $sms->setCreatedBy($user);
            $sms->setEglise($eglise);
//            $sentMessage = $texter->send($sms);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($sms);
            $entityManager->flush();

            return $this->redirectToRoute('sms_index');
        }

        return $this->render('sms/new.html.twig', [
                    'sms' => $sms,
                    'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'sms_show', methods: ['GET'])]

    public function show(Sms $sms): Response {
                $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        return $this->render('sms/show.html.twig', [
                    'sms' => $sms,
        ]);
    }

//    #[Route('/{id}/edit', name: 'sms_edit', methods: ['GET', 'POST'])]
//    public function edit(Request $request, Sms $sms): Response
//    {
//        $form = $this->createForm(SmsType::class, $sms);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $this->getDoctrine()->getManager()->flush();
//
//            return $this->redirectToRoute('sms_index');
//        }
//
//        return $this->render('sms/edit.html.twig', [
//            'sms' => $sms,
//            'form' => $form->createView(),
//        ]);
//    }
//
//    #[Route('/{id}', name: 'sms_delete', methods: ['POST'])]
//    public function delete(Request $request, Sms $sms): Response
//    {
//        if ($this->isCsrfTokenValid('delete'.$sms->getId(), $request->request->get('_token'))) {
//            $entityManager = $this->getDoctrine()->getManager();
//            $entityManager->remove($sms);
//            $entityManager->flush();
//        }
//
//        return $this->redirectToRoute('sms_index');
//    }
}
