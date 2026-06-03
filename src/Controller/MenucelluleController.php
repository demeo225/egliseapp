<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MenucelluleController extends AbstractController
{
    #[Route('/menucellule', name: 'app_menucellule')]
    public function index(): Response
    {
        return $this->render('menucellule/index.html.twig', [
            'controller_name' => 'MenucelluleController',
        ]);
    }
}
