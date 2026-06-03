<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MenuecodimController extends AbstractController
{
    #[Route('/menuecodim', name: 'app_menuecodim')]
    public function index(): Response
    {
        return $this->render('menuecodim/index.html.twig', [
            'controller_name' => 'MenuecodimController',
        ]);
    }
}
