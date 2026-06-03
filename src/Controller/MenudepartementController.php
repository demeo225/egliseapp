<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MenudepartementController extends AbstractController
{
    #[Route('/menudepartement', name: 'app_menudepartement')]
    public function index(): Response
    {
        return $this->render('menudepartement/index.html.twig', [
            'controller_name' => 'MenudepartementController',
        ]);
    }
}
