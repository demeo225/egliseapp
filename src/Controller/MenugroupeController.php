<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MenugroupeController extends AbstractController
{
    #[Route('/menugroupe', name: 'app_menugroupe')]
    public function index(): Response
    {
        return $this->render('menugroupe/index.html.twig', [
            'controller_name' => 'MenugroupeController',
        ]);
    }
}
