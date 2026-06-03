<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MenufamilleController extends AbstractController
{
    #[Route('/menufamille', name: 'app_menufamille')]
    public function index(): Response
    {
        return $this->render('menufamille/index.html.twig', [
            'controller_name' => 'MenufamilleController',
        ]);
    }
}
