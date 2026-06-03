<?php

namespace App\Controller;

use App\Form\Searchenfant2Type;
use App\Form\SearchenfantType;
use App\Repository\CelluleRepository;
use App\Repository\EnfantRepository;
use App\Repository\EthnieRepository;
use App\Repository\FamilleRepository;
use App\Repository\FideleRepository;
use App\Repository\QuartierRepository;
use App\Repository\RegionRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RecherchechildController extends AbstractController {

//    #[Route('/recherchechild', name: 'app_recherchechild')]
//
//    public function index(): Response {
//        return $this->render('recherchechild/index.html.twig', [
//                    'controller_name' => 'RecherchechildController',
//        ]);
//    }
    #[Route('/rechercheenfant', name: 'rechercheenfant', methods: ['GET', 'POST'])]
    public function rechercheEnfant(Request $request, EnfantRepository $enfantRepository, QuartierRepository $quartierRepository,
            FamilleRepository $familleRepository, FideleRepository $fideleRepository,
            CelluleRepository $celluleRepository, EthnieRepository $ethnieRepository, DateTime $dateDebut = null, DateTime $dateFin = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();

        $enfants = [];
        $quartier = $quartierRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $cellule = $celluleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $ethnie = $ethnieRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $famille = $familleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $peremembre = $fideleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL, "sexe" => 'Homme']);
        $merembre = $fideleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL, "sexe" => 'Femme']);

        $searchenfant = $this->createForm(SearchenfantType::class, $enfants, ['quartier' => $quartier, 'cellule' => $cellule, 'ethnie' => $ethnie,
            'famille' => $famille, 'peremembre' => $peremembre, 'merembre' => $merembre]);
        if ($searchenfant->handleRequest($request)->isSubmitted()) {
            $criteria = $searchenfant->getData();

            $criteres = array();
            $criteres["eglise"] = $eglise->getId();

            if ($criteria["quartier"]) {
                $criteres["quartier"] = $criteria["quartier"];
            }



            if ($criteria["cellule"]) {
                $criteres["cellule"] = $criteria["cellule"];
            }

            if ($criteria["famille"]) {
                $criteres["famille"] = $criteria["famille"];
            }




            if ($criteria["peremembre"]) {
                $criteres["peremembre"] = $criteria["peremembre"];
            }

            if ($criteria["merembre"]) {
                $criteres["merembre"] = $criteria["merembre"];
            }

            if ($criteria["maladie"]) {
                $criteres["maladie"] = $criteria["maladie"];
            }

            if ($criteria["ethnie"]) {
                $criteres["ethnie"] = $criteria["ethnie"];
            }

            if ($criteria["nationalite"]) {
                $criteres["nationalite"] = $criteria["nationalite"];
            }



            if ($criteria["niveauetude"]) {
                $criteres["niveauetude"] = $criteria["niveauetude"];
            }


            if ($criteria["groupesang"]) {
                $criteres["groupesang"] = $criteria["groupesang"];
            }

            if ($criteria["situation"]) {
                $criteres["situation"] = $criteria["situation"];
            }

            if ($criteria["handicap"]) {
                $criteres["handicap"] = $criteria["handicap"];
            }

            if ($criteria["vieparent"]) {
                $criteres["vieparent"] = $criteria["vieparent"];
            }

            if ($criteria["sexe"]) {
                $criteres["sexe"] = $criteria["sexe"];
            }

            if ($criteria["dateDebut"]) {
                $dateDebut = ($criteria["dateDebut"]);
                //  $dateDebut = DateTime::createFromFormat('Y-m-d',$dateDebuts);
            }

            if ($criteria["dateFin"]) {
                $dateFin = $criteria["dateFin"];

                // $dateFin = DateTime::createFromFormat('Y-m-d',$dateFins);
            }


            $limit = 1000;
            $enfants = $enfantRepository->rechercheEnfant($criteres, $dateDebut, $dateFin, $limit);
        }

        return $this->render('bilan/enfant/searchenfant.html.twig', [
                    'form_recherche' => $searchenfant->createView(),
                    'enfants' => $enfants,
        ]);
    }
    
    
    
    #[Route('/rechercheenfant2', name: 'rechercheenfant2', methods: ['GET', 'POST'])]
    public function rechercheEnfantnational(Request $request, EnfantRepository $enfantRepository, RegionRepository $regionRepository, DateTime $dateDebut = null, DateTime $dateFin = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $communaute = $this->getUser()->getEglise()->getCommunaute();
        $user = $this->getUser();

        $enfants = [];
        $region = $regionRepository->findBy(["deletedAt" => NULL]);
        $searchenfant = $this->createForm(Searchenfant2Type::class, $enfants, ['region' => $region]);
        if ($searchenfant->handleRequest($request)->isSubmitted()) {
            $criteria = $searchenfant->getData();
            $criteres = array();

      
            $criteres["communaute"] = $communaute->getId();

            if ($criteria["region"]) {
                $criteres["region"] = $criteria["region"];
            }
            if ($criteria["maladie"]) {
                $criteres["maladie"] = $criteria["maladie"];
            }

       
            if ($criteria["nationalite"]) {
                $criteres["nationalite"] = $criteria["nationalite"];
            }



            if ($criteria["niveauetude"]) {
                $criteres["niveauetude"] = $criteria["niveauetude"];
            }


            if ($criteria["groupesang"]) {
                $criteres["groupesang"] = $criteria["groupesang"];
            }

            if ($criteria["situation"]) {
                $criteres["situation"] = $criteria["situation"];
            }

            if ($criteria["handicap"]) {
                $criteres["handicap"] = $criteria["handicap"];
            }

            if ($criteria["vieparent"]) {
                $criteres["vieparent"] = $criteria["vieparent"];
            }

            if ($criteria["sexe"]) {
                $criteres["sexe"] = $criteria["sexe"];
            }

            if ($criteria["dateDebut"]) {
                $dateDebut = ($criteria["dateDebut"]);
                //  $dateDebut = DateTime::createFromFormat('Y-m-d',$dateDebuts);
            }

            if ($criteria["dateFin"]) {
                $dateFin = $criteria["dateFin"];

                // $dateFin = DateTime::createFromFormat('Y-m-d',$dateFins);
            }

            $limit = 1000;
            $enfants = $enfantRepository->rechercheEnfantnational($criteres, $dateDebut, $dateFin, $limit);
        }

        return $this->render('bilan/enfant/searchenfant2.html.twig', [
                    'form_recherche' => $searchenfant->createView(),
                    'enfants' => $enfants,
        ]);
    }


}
