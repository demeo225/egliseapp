<?php

namespace App\Controller;

use App\Entity\Eglise;
use App\Form\EglisedemandeType;
use App\Form\EgliseType;
use App\Repository\EgliseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function Sodium\bin2hex;

class CompteController extends AbstractController
{
    #[Route('/compte', name: 'compte')]
    public function index(): Response
    {
        return $this->render('compte/index.html.twig', [
            'controller_name' => 'CompteController',
        ]);
    }
    
     #[Route('/demande', name: 'compte_demande', methods: ['GET', 'POST'])]

    public function demande(Request $request, string $photoDir = null, EgliseRepository $egliseRepository): Response {
                $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $eglise = new Eglise();
        $form = $this->createForm(EglisedemandeType::class, $eglise);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Etablir le lien entre le Code et l'ID de l'Eglise
            $listeeglise = $egliseRepository->findBy(array(), array('id' => 'desc'), 1, 0);
            $id = 0;
            foreach ($listeeglise as $value) {
                $id = $value->getId();
            }
            $val = $id + 1;
            $eglisenom = $form['denomination']->getData();
            $denomoniation = substr($eglisenom, 0, 3);

            $code1 = $denomoniation . $val;

            $eglise->setCode($code1);
            $eglise->setEtat("0");

            // Insertion image de profile
            if ($photo = $form['photo']->getData()) {
                $filename = bin2hex(random_bytes(6)) . '.' . $photo->guessExtension();
                try {
                    $photo->move($photoDir, $filename);
                } catch (FileException $e) {
                    // unable to upload the photo, give up
                }
                $eglise->setLogo($filename);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($eglise);
            $entityManager->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('eglise/demande.html.twig', [
                    'eglise' => $eglise,
                    'form' => $form->createView(),
        ]);
    }

    #[Route('/new', name: 'eglise_new', methods: ['GET', 'POST'])]

    public function new(Request $request, string $photoDir = null, EgliseRepository $egliseRepository): Response {
        $eglise = new Eglise();
        $form = $this->createForm(EgliseType::class, $eglise);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Etablir le lien entre le Code et l'ID de l'Eglise
            $listeeglise = $egliseRepository->findBy(array(), array('id' => 'desc'), 1, 0);
            $id = 0;
            foreach ($listeeglise as $value) {
                $id = $value->getId();
            }
            $val = $id + 1;
            $eglisenom = $form['denomination']->getData();
            $denomoniation = substr($eglisenom, 0, 3);

            $code1 = $denomoniation . $val;

            $eglise->setCode($code1);
            $eglise->setEtat("1");

            // Insertion image de profile
            if ($photo = $form['photo']->getData()) {
                $filename = bin2hex(random_bytes(6)) . '.' . $photo->guessExtension();
                try {
                    $photo->move($photoDir, $filename);
                } catch (FileException $e) {
                    // unable to upload the photo, give up
                }
                $eglise->setLogo($filename);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($eglise);
            $entityManager->flush();

            return $this->redirectToRoute('eglise');
        }

        return $this->render('eglise/new.html.twig', [
                    'eglise' => $eglise,
                    'form' => $form->createView(),
        ]);
    }
}
