<?php

namespace App\Controller;

use App\Form\SearchfidelenationalType;
use App\Repository\FideleRepository;
use App\Repository\RegionRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RecherchefidelenationalController extends AbstractController {

    #[Route('/recherchefidelenational', name: 'recherchefidelenational', methods: ['GET', 'POST'])]
    public function rechercheFidelenational(Request $request, FideleRepository $fideleRepository, RegionRepository $regionRepository, DateTime $dateDebut = null, DateTime $dateFin = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $eglise = $this->getUser()->getEglise();
        $communaute = $this->getUser()->getEglise()->getCommunaute();
        $user = $this->getUser();

        $fideles = [];
        $region = $regionRepository->findBy(["deletedAt" => NULL]);
        $searchfidele = $this->createForm(SearchfidelenationalType::class, $fideles, ['region' => $region]);
        if ($searchfidele->handleRequest($request)->isSubmitted()) {
            $criteria = $searchfidele->getData();
            $criteres = array();

            $criteres["communaute"] = $communaute->getId();

            if ($criteria["region"]) {
                $criteres["region"] = $criteria["region"]->getId();
            }

            // $criteres["communaute"] = $user->getEglise()->getCommunaute();

            if ($criteria["statutmatri"]) {
                $criteres["statutmatri"] = $criteria["statutmatri"];
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
            if ($criteria["nationalite"]) {
                $criteres["nationalite"] = $criteria["nationalite"];
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
            $fideles = $fideleRepository->rechercheFidelenational($criteres, $dateDebut, $dateFin, $limit);
        }

        return $this->render('bilan/fidele/searchfidelenational.html.twig', [
                    'form_recherche' => $searchfidele->createView(),
                    'fideles' => $fideles,
        ]);
    }

}
