<?php

namespace App\Controller;

use App\Entity\Officiant;
use App\Form\OfficiantType;
use App\Repository\OfficiantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use DateTime;
use App\Traits\ClientIp;


#[Route('/officiant')]

class OfficiantController extends AbstractController {
    #[Route('/', name: 'officiant_index', methods: ['GET'])]

    public function index(OfficiantRepository $officiantRepository, Request $request): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $officiant = $officiantRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        return $this->render('officiant/index.html.twig', [
                    'officiants' => $officiant,
        ]);
    }

    #[Route('/new', name: 'officiant_new', methods: ['GET', 'POST'])]

    public function new(Request $request): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $officiant = new Officiant();
        $form = $this->createForm(OfficiantType::class, $officiant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //Adresse ip de l'utilisateur
            function getIp() {
                if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                    $ip = $_SERVER['HTTP_CLIENT_IP'];
                } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
                } else {
                    $ip = $_SERVER['REMOTE_ADDR'];
                }
                return $ip;
            }

            $ip = getIp();
            $officiant->setCreatedFromIp($ip);
            $entityManager = $this->getDoctrine()->getManager();
            $eglise = $this->getUser()->getEglise();
            $user = $this->getUser();
            $officiant->setCreatedBy($user);
            $officiant->setEglise($eglise);
            $entityManager->persist($officiant);
            $entityManager->flush();

            return $this->redirectToRoute('officiant_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('officiant/new.html.twig', [
                    'officiant' => $officiant,
                    'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'officiant_show', methods: ['GET'])]

    public function show(Officiant $officiant): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        return $this->render('officiant/show.html.twig', [
                    'officiant' => $officiant,
        ]);
    }

    #[Route('/{id}/edit', name: 'officiant_edit', methods: ['GET', 'POST'])]

    public function edit(Request $request, Officiant $officiant): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $form = $this->createForm(OfficiantType::class, $officiant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //Adresse ip de l'utilisateur
            function getIp() {
                if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                    $ip = $_SERVER['HTTP_CLIENT_IP'];
                } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
                } else {
                    $ip = $_SERVER['REMOTE_ADDR'];
                }
                return $ip;
            }

            $ip = getIp();
            $officiant->setUpdatedFromIp($ip);
            $user = $this->getUser();
            $officiant->setUpdatedBy($user);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('officiant_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('officiant/edit.html.twig', [
                    'officiant' => $officiant,
                    'form' => $form->createView(),
        ]);
    }

    
    
      #[Route('/{id}', name: 'officiant_delete', methods: ['POST'])]
    public function delete(Request $request, Officiant $officiant): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
            if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($this->isCsrfTokenValid('delete' . $officiant->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();
            function getIp() {
                if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                    $ip = $_SERVER['HTTP_CLIENT_IP'];
                } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
                } else {
                    $ip = $_SERVER['REMOTE_ADDR'];
                }
                return $ip;
            }

            $ip = getIp();
            $officiant->setDeletedFromIp($ip);
            $officiant->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $officiant->setDeletedBy($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('officiant_index');
    }

}
