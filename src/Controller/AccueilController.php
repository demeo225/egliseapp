<?php

namespace App\Controller;

use App\Entity\Enfant;
use App\Repository\EthnieRepository;
use App\Repository\CelluleRepository;
use App\Repository\CultecodimRepository;
use App\Repository\CulteRepository;
use App\Repository\EnfantRepository;
use App\Repository\EvangelisationRepository;
use App\Repository\FideleRepository;
use App\Repository\DepartementRepository;
use App\Repository\GroupeRepository;
use App\Repository\OffrandeRepository;
use App\Repository\SeancecelluleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccueilController extends AbstractController {
    #[Route('/', name: 'accueil')]

    public function index(EnfantRepository $enfantRepository ,CulteRepository $culteRepository , SeancecelluleRepository $seancecelluleRepository , EvangelisationRepository $evangelisationRepository , CultecodimRepository $cultecodimRepository, FideleRepository $fideleRepository, CelluleRepository $celluleRepository, EthnieRepository $ethnieRepo): Response {


        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $eglise = $this->getUser()->getEglise();
        $egliseId = $this->getUser()->getEglise()->getId();
        $user = $this->getUser(); 

        $enfant = $enfantRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL, "etatenfant" => 1]);
        $cellule = $celluleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        //$culte = $culteRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
//        $ethnie = $ethnieRepo->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $fidele = $fideleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL, "etatfidele" => 1]);
        $fidelepareglise = $fideleRepository->getFideleByEglise($eglise);
        $fidele2 = $fideleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL, "etatfidele" => 0]);
        $stats = $enfantRepository->getStatsByEglise($eglise);
     
        
        $cultes = $culteRepository->findBy(['eglise' => $eglise], ['dateculte' => 'DESC'], 6);
        
        // Préparer les données pour le graphique
        $labels = [];
        $totalPresences = [];
        
        foreach ($cultes as $culte) {
            $labels[] = $culte->getDateculte()->format('d/m/Y');
            $totalPresences[] = $culte->getNbrefant() + $culte->getNmbrehomme()+ $culte->getNobrefemme()+ $culte->getInvite();
        }
        
        // Inverser pour avoir du plus ancien au plus récent
       // $labels = array_reverse($labels);
        //$totalPresences = array_reverse($totalPresences);

           // Compter les adultes par sexe
           // Graphe par sexe par membre
         $adultesHommes = $fideleRepository->countBySexe('Homme', $eglise);

        $adultesFemmes = $fideleRepository->countBySexe('Femme', $eglise);
        
        // Compter les enfants par sexe
        $enfantsFilles = $enfantRepository->countBySexe('Fille', $eglise);
        $enfantsGarcons = $enfantRepository->countBySexe('Garçon', $eglise);
        $totaux = $enfantsFilles  + $enfantsGarcons + $adultesFemmes + $adultesHommes ;


        // Récupérer les présences aux culte groupées par date Fidèle Invite, Enfant
        $culteData = $culteRepository->findSommeNombresParDate($eglise);
        
        // Préparer les données pour Chart.js
        $dates = [];
        $sommeNombre1 = [];
        $sommeNombre2 = [];
        $sommeNombre3 = [];
        $sommeNombre4 = [];
        $totalNombre = [];
        
        foreach ($culteData as $data) {
            $dates[] = $data['dateculte']->format('d/m/Y');
            $sommeNombre1[] = $data['sommeNombre1'];
            $sommeNombre2[] = $data['sommeNombre2'];
            $sommeNombre3[] = $data['sommeNombre3'];
            $sommeNombre4[] = $data['sommeNombre4'];
            $totalNombre = $sommeNombre1 + $sommeNombre2 + $sommeNombre3 + $sommeNombre4;
            
        }


        //Presences groupées par date uniquement Adultes
        
        $culteFidele = $culteRepository->findAdultesParDate($eglise);
        
        // Préparer les données pour Chart.js
        $dateadultes = [];
        $sommeNombre1 = [];
        $sommeNombre2 = [];
        $totalNombreadulte = [];
   
        
        foreach ($culteFidele as $data) {
            $dateadultes[] = $data['dateculte']->format('d/m/Y');
            $sommeNombre1[] = $data['homme'];
            $sommeNombre2[] = $data['femme'];
    
            $totalNombreadulte = $sommeNombre1 + $sommeNombre2;
            
        }


        //Fin graphe adulte



        //Graphe des invités aux cultes
        
        // Récupérer les présences aux culte groupées par date
        $culteDataInvite = $culteRepository->findInviteParDate($eglise);
        
        // Préparer les données pour Chart.js
        $dates = [];
        $invites = [];

        
        foreach ($culteDataInvite as $data) {
            $dates[] = $data['dateculte']->format('d/m/Y');
            $invites[] = $data['invite'];

        }


        //Presences groupées par date pour Ecodim


        //Fin graphes des inivites

        
        // Récupérer les présences aux culte groupées par date
        $culteDataecodim = $cultecodimRepository->findSommeNombresParDateEcodim(['eglise' => $eglise,]);
        
        // Préparer les données pour Chart.js
        $dateecodims = [];
        $sommeNombreecodim1 = [];
        $sommeNombreecodim2 = [];

        
        foreach ($culteDataecodim as $data) {
            $dateecodims[] = $data['dateculte']->format('d/m/Y');
            $sommeNombreecodim1[] = $data['sommeNombreecodim1'];
            $sommeNombreecodim2[] = $data['sommeNombreecodim2'];
        
        }

        //Graphe des nouvelles âmes
       $evangelisationData = $evangelisationRepository->findEvangelisationParDate($eglise);
        
        // Préparer les données pour Chart.js
        $dateops = [];
        $sommeAmes1 = [];
 
        
        foreach ($evangelisationData as $data) {
            $dateops[] = $data['dateop']->format('d/m/Y');
            $sommeAmes1[] = $data['personnes1'];

            
        }

    
        //Graphe des activités des cellules
        
     $culteecodim2 = $cultecodimRepository->findPresenteGroupeeByDate(['eglise' => $eglise,]);
        
        // Préparer les données pour le graphique
        $labelcecodims = [];
        $totalPresenceecodims = [];
        
        foreach ($culteecodim2 as $sanc2e) {
            $labelcecodims[] = $sanc2e['dateculte']->format('d/m/Y');
            $totalPresenceecodims[] = $sanc2e['totalPresentEcodim'];
        }

       

        //Fin graphe cellule

        //Graphe culte ecodim

     $seances = $seancecelluleRepository->findPresenteByDate(['eglise' => $eglise,]);
        
        // Préparer les données pour le graphique
        $labelcs = [];
        $totalPresencecs = [];
        
        foreach ($seances as $sance) {
            $labelcs[] = $sance['datesuper']->format('d/m/Y');
            $totalPresencecs[] = $sance['totalPresentcellule'];
        }

        //Fin graphe ecodim

        //Graphe des fidèles par Fonction

        // Récupérer les données des ethnies et le nombre de fideles
        $fonctionData = $fideleRepository->countPatientsByFonction($eglise);
        
        // Calculer le total pour les pourcentages
        $totalPatientfonctions = array_sum(array_column($fonctionData, 'fideleCount'));
        
        // Préparer les données pour Chart.js
        $labelfidelesfonction = [];
        $datafonction = [];
        $backgroundColorsfonction = [];
        $percentagesfonction = [];
        
        // Couleurs aléatoires pour le graphique
        $colors = [
            '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', 
            '#9966FF', '#FF9F40', '#8AC24A', '#607D8B'
        ];
        
        foreach ($fonctionData as $index => $item) {
            $labelfidelesfonction[] = $item['fonctionName'];
            $datafonction[] = $item['fideleCount'];
            $backgroundColorsfonction[] = $colors[$index % count($colors)];
            $percentagesfonction[] = $totalPatientfonctions > 0 ? round(($item['fideleCount'] / $totalPatientfonctions) * 100, 2) : 0;
        }

        //fin graphe des fidèles par fonction
        
    

        // Récupérer les données des ethnies et le nombre de fideles
        $ethnieData = $fideleRepository->countPatientsByEthnie($eglise);
        
        // Calculer le total pour les pourcentages
        $totalPatients = array_sum(array_column($ethnieData, 'fideleCount'));
        
        // Préparer les données pour Chart.js
        $labelfideles = [];
        $dataethnie = [];
        $backgroundColors = [];
        $percentages = [];
        
        // Couleurs aléatoires pour le graphique
        $colors = [
            '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', 
            '#9966FF', '#FF9F40', '#8AC24A', '#607D8B'
        ];
        
        foreach ($ethnieData as $index => $item) {
            $labelfideles[] = $item['ethnieName'];
            $dataethnie[] = $item['fideleCount'];
            $backgroundColors[] = $colors[$index % count($colors)];
            $percentages[] = $totalPatients > 0 ? round(($item['fideleCount'] / $totalPatients) * 100, 2) : 0;
        }

        //Fin graphe fidèle par fonction



         // Récupération des données par tranche d'âge
         
         // Récupérer les données groupées  // Récupérer tous les membres (ajuster selon votre entité)
        
        $allMembers = $fideleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);

        // Définir les tranches d'âge et initialiser les compteurs
        $ageRanges = ['5-20', '25-35', '36-45', '46-55', '55+'];
        $membersCount = array_fill(0, count($ageRanges), 0);

        // Calculer le nombre de membres par tranche d'âge
        foreach ($allMembers as $member) {
            $birthdate = $member->getDatenaiss(); // Supposons que vo   tre entité a une méthode getBirthdate()
            $age = $birthdate->diff(new \DateTime())->y;

            if ($age >= 5 && $age <= 20) {
                $membersCount[0]++;
            } elseif ($age >= 25 && $age <= 35) {
                $membersCount[1]++;
            } elseif ($age >= 36 && $age <= 45) {
                $membersCount[2]++;
            } elseif ($age >= 46 && $age <= 55) {
                $membersCount[3]++;
            } elseif ($age > 55) {
                $membersCount[4]++;
            }
        } 
        //Fin graphe par tranche d'age

        return $this->render('accueil/index.html.twig', [
                    'cellule' => $cellule,
                    'enfant' => $enfant,
                    'fidele' => $fidele,
                    'inscrit' => $fidele2,
//                    'chart' => $out,
                    'stats' => $stats,
                     'labels' => $labels,
                    'totalPresences' => $totalPresences,
                    'totalfidele' =>$fidelepareglise,
                    'totalfidele2' =>$totaux,

                    'adultesFemmes' => $adultesFemmes,
                    'adultesHommes' => $adultesHommes,
                    'enfantsFilles' => $enfantsFilles,
                    'enfantsGarcons' => $enfantsGarcons,
                    //Presences aux cultes groupées par date
                    'dates' => json_encode($dates),
                    'sommeNombre1' => json_encode($sommeNombre1),
                    'sommeNombre2' => json_encode($sommeNombre2),
                    'sommeNombre3' => json_encode($sommeNombre3),
                    'sommeNombre4' => json_encode($sommeNombre4),
                    'totalNombre' => json_encode($totalNombre),

                    //Graphe des adultes aux cultes

                    'dateadultes' => json_encode($dateadultes),
                     
                    'totalNombreadulte' => json_encode($totalNombreadulte),

                    //Presences pour ecodim groupées apr date
                     'dateecodims' => json_encode($dateecodims),
                    'sommeNombreecodim1' => json_encode($sommeNombreecodim1),
                    'sommeNombreecodim2' => json_encode($sommeNombreecodim2),
                        //Invite
                    'invite' => json_encode($invites),
                    //Nouvelle ame
                    'dateops' => json_encode($dateops),
                    'personnes1' => json_encode($sommeAmes1),
                    //seancecellule
    
                    'labelcellules' => $labelcs,
                    'totaltPresentCellules' => $totalPresencecs,

                    //Graphe ECODIM COmbiné

                       'labelcecodims' => $labelcecodims,
                    'totalPresenceecodims' => $totalPresenceecodims,
                    //Fidèle par ethnie
                     'labelfideles' => $labelfideles,
                     'dataethnie' => $dataethnie,
                     'backgroundColors' => $backgroundColors,
                     'percentages' => $percentages,
                     'totalPatients' => $totalPatients,
                     //Fidèle par fonction
                    'labelfidelesfonction' => $labelfidelesfonction,
                     'datafonction' => $datafonction,
                     'backgroundColorsfonction' => $backgroundColorsfonction,
                     'percentagesfonction' => $percentagesfonction,
                     'totalPatientfonctions' => $totalPatientfonctions,
                     //Grphe par tranche d'age
                     'age_ranges' => $ageRanges,
                       'members_count' => $membersCount,
                   
        ]);
    }


     
    //Liste des membres d'un groupe
    #[Route('/membregp', name: 'seancegroupe_membregp', methods: ['GET'])]
    public function listemembre(GroupeRepository $groupeRepository, FideleRepository $fideleRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_GROUPE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();

         $groupe = $groupeRepository->findOneByUser($user);
         if (!$groupe) {
            $this->addFlash('warning', 'Vous ne disposez pas sous-groupe à gérer.');
            return $this->redirectToRoute('cotisationgroupe_index');
        }
        if (!$groupe) {
            $this->addFlash('message', 'Vous ne disposez pas de groupe à gérer.');
            return $this->redirectToRoute('seancegroupe_index');
        }
        $groupes = $groupe ? [$groupe] : [];
        $idGroupe = $groupe->getId();
       
        $mebres = $fideleRepository->findFidelesByGroupe($idGroupe);
       
        return $this->render('seancegroupe/membre.html.twig', [
                    'membres' => $mebres,
                   
        ]);
    }

    //Membre des departement
    #[Route('/membredepart', name: 'seancedepartement_membredep', methods: ['GET'])]
    public function listemembredep(DepartementRepository $departementRepository, FideleRepository $fideleRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_DEPARTEMENT')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();

        //Recuperer le departement et les membres
        $departement = $departementRepository->findOneByUser($user);
     if (!$departement) {
        $this->addFlash('message', 'Vous ne disposez pas de departement à gérer.');
        return $this->redirectToRoute('seancedepartement_index');
    }
    $iddepart = $departement->getId();

        $fideledep = $fideleRepository->findFidelesByDepartement($iddepart);
      
        return $this->render('seancedepartement/membre.html.twig', [
            'membres' => $fideledep,
                   
        ]);
    }

}
