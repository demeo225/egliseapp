<?php

namespace App\Controller;

use App\Form\SearchfideleType;
use App\Repository\CelluleRepository;
use App\Repository\CommuneRepository;
use App\Repository\EthnieRepository;
use App\Repository\FamilleRepository;
use App\Repository\FideleRepository;
use App\Repository\FonctionRepository;
use App\Repository\QuartierRepository;
use App\Repository\ZoneRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RecherchefideleController extends AbstractController {

    #[Route('/recherchefidele', name: 'recherchefidele', methods: ['GET', 'POST'])]
    public function rechercheFidele(Request $request, FideleRepository $fideleRepository, QuartierRepository $quartierRepository,
            ZoneRepository $zoneRepository, FamilleRepository $familleRepository, FonctionRepository $fonctionRepository,
            CelluleRepository $celluleRepository, EthnieRepository $ethnieRepository, CommuneRepository $communeRepository, DateTime $dateDebut = null, DateTime $dateFin = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();

        $fideles = [];
        $quartier = $quartierRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $cellule = $celluleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $zone = $zoneRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $ethnie = $ethnieRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $famille = $familleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $commune = $communeRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $fonction = $fonctionRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);

        $searchfidele = $this->createForm(SearchfideleType::class, $fideles, ['quartier' => $quartier, 'cellule' => $cellule, 'zone' => $zone, 'ethnie' => $ethnie,
            'famille' => $famille, 'fonction' => $fonction, 'commune' => $commune]);
        if ($searchfidele->handleRequest($request)->isSubmitted()) {
            $criteria = $searchfidele->getData();

            $criteres = array();
            $criteres["eglise"] = $eglise->getId();
            if ($criteria["zone"]) {
                $criteres["zone"] = $criteria["zone"];
            }


            if ($criteria["quartier"]) {
                $criteres["quartier"] = $criteria["quartier"];
            }



            if ($criteria["cellule"]) {
                $criteres["cellule"] = $criteria["cellule"];
            }

            if ($criteria["famille"]) {
                $criteres["famille"] = $criteria["famille"];
            }



            if ($criteria["ethnie"]) {
                $criteres["ethnie"] = $criteria["ethnie"];
            }

            if ($criteria["nationalite"]) {
                $criteres["nationalite"] = $criteria["nationalite"];
            }

            if ($criteria["fonction"]) {
                $criteres["fonction"] = $criteria["fonction"];
            }


            if ($criteria["commune"]) {
                $criteres["commune"] = $criteria["commune"];
            }


            if ($criteria["statutmatri"]) {
                $criteres["statutmatri"] = $criteria["statutmatri"];
            }

            if ($criteria["maladie"]) {
                $criteres["maladie"] = $criteria["maladie"];
            }

            if ($criteria["bapteme"]) {
                $criteres["bapteme"] = $criteria["bapteme"];
            }

            if ($criteria["groupesang"]) {
                $criteres["groupesang"] = $criteria["groupesang"];
            }
            if ($criteria["stutbapteme"]) {
                $criteres["stutbapteme"] = $criteria["stutbapteme"];
            }

            if ($criteria["vieseul"]) {
                $criteres["vieseul"] = $criteria["vieseul"];
            }

            if ($criteria["typefidele"]) {
                $criteres["typefidele"] = $criteria["typefidele"];
            }

            if ($criteria["langue"]) {
                $criteres["langue"] = $criteria["langue"];
            }

            if ($criteria["choiculte"]) {
                $criteres["choiculte"] = $criteria["choiculte"];
            }


            if ($criteria["permis"]) {
                $criteres["permis"] = $criteria["permis"];
            }


            if ($criteria["emploi"]) {
                $criteres["emploi"] = $criteria["emploi"];
            }

            if ($criteria["cultefamille"]) {
                $criteres["cultefamille"] = $criteria["cultefamille"];
            }

            if ($criteria["priere"]) {
                $criteres["priere"] = $criteria["priere"];
            }

            if ($criteria["lecture"]) {
                $criteres["lecture"] = $criteria["lecture"];
            }

            if ($criteria["temoignage"]) {
                $criteres["temoignage"] = $criteria["temoignage"];
            }

            if ($criteria["bibleformation"]) {
                $criteres["bibleformation"] = $criteria["bibleformation"];
            }

            if ($criteria["etatparent"]) {
                $criteres["etatparent"] = $criteria["etatparent"];
            }

            if ($criteria["situation"]) {
                $criteres["situation"] = $criteria["situation"];
            }

            if ($criteria["handicap"]) {
                $criteres["handicap"] = $criteria["handicap"];
            }

            if ($criteria["etatvieparent"]) {
                $criteres["etatvieparent"] = $criteria["etatvieparent"];
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
            if ($criteria["domaineactivite"]) {
                $criteres["domaineactivite"] = $criteria["domaineactivite"];
            }

            if ($criteria["etude"]) {
                $criteres["etude"] = $criteria["etude"];
            }

            $limit = 1000;
            $fideles = $fideleRepository->rechercheFidele($criteres, $dateDebut, $dateFin, $limit);
        }

        return $this->render('bilan/fidele/searchfidele.html.twig', [
                    'form_recherche' => $searchfidele->createView(),
                    'fideles' => $fideles,
        ]);
    }

}
