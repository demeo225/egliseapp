<?php

namespace App\Controller;

use App\Form\BilanactiongracenType;
use App\Form\BilanactiongraceType;
use App\Form\BilanbaptemenType;
use App\Form\BilanbaptemeType;
use App\Form\BilancelluleuserType;
use App\Form\BilancenenType;
use App\Form\BilanceneType;
use App\Form\BilancongenType;
use App\Form\BilancongeType;
use App\Form\BilancotisationexceptionnType;
use App\Form\BilancotisationexceptionType;
use App\Form\BilancotisationnType;
use App\Form\BilancotisationType;
use App\Form\BilanculteecodimnType;
use App\Form\BilancultenType;
use App\Form\BilanculteType;
use App\Form\BilandecesnType;
use App\Form\BilandecesType;
use App\Form\BilandepensecodimType;
use App\Form\BilandimeglobalenType;
use App\Form\BilandimeglobaleType;
use App\Form\BilandimenType;
use App\Form\BilandimeType;
use App\Form\BilandisciplinenType;
use App\Form\BilandisciplineType;
use App\Form\BilandonnType;
use App\Form\BilandonType;
use App\Form\BilanecodimType;
use App\Form\BilanemariageType;
use App\Form\BilanevangelisationnType;
use App\Form\BilanevangelisationType;
use App\Form\BilanglobalType;
use App\Form\BilaninvitenType;
use App\Form\BilaninviteType;
use App\Form\BilanmariagenType;
use App\Form\BilannaissancenType;
use App\Form\BilannaissanceType;
use App\Form\BilannomminationnType;
use App\Form\BilannomminationType;
use App\Form\BilanoffrandenType;
use App\Form\BilanoffrandeType;
use App\Form\BilanoperationnType;
use App\Form\BilanoperationType;
use App\Form\BilanpastoralenType;
use App\Form\BilanpastoraleType;
use App\Form\BilanpatrimoinenType;
use App\Form\BilanpatrimoineType;
use App\Form\BilanprogrammenType;
use App\Form\BilanprogrammeType;
use App\Form\BilanrecommandationnType;
use App\Form\BilanrecommandationType;
use App\Form\BilansocialenType;
use App\Form\BilansocialeType;
use App\Form\BilanType;
use App\Form\Bilanvisite2nType;
use App\Form\Bilanvisite2Type;
use App\Form\BilanvisitenType;
use App\Form\BilanvisiteType;
use App\Form\RecherchecelluleType;
use App\Form\RecherchedepartementType;
use App\Form\RecherchefamilleType;
use App\Form\RecherchegroupeType;
use App\Form\Recherchegroupe2Type;
use App\Form\RecherchezoneType;
use App\Repository\ActiongraceRepository;
use App\Repository\ActivitesocialeRepository;
use App\Repository\BaptemeRepository;
use App\Repository\CelluleRepository;
use App\Repository\ClassecodimRepository;
use App\Repository\CongeRepository;
use App\Repository\CotisationexceptionnelleRepository;
use App\Repository\CotisationRepository;
use App\Repository\CultecodimRepository;
use App\Repository\CulteRepository;
use App\Repository\DecesRepository;
use App\Repository\DepartementRepository;
use App\Repository\DepensecodimRepository;
use App\Repository\DimeglobaleRepository;
use App\Repository\DimeRepository;
use App\Repository\DisciplineRepository;
use App\Repository\DonRepository;
use App\Repository\EvangelisationRepository;
use App\Repository\FamilleRepository;
use App\Repository\FideleRepository;
use App\Repository\GroupeRepository;
use App\Repository\InviteRepository;
use App\Repository\MariageRepository;
use App\Repository\NaissanceRepository;
use App\Repository\NomminationRepository;
use App\Repository\OffrandeRepository;
use App\Repository\OperationRepository;
use App\Repository\PastoraleRepository;
use App\Repository\PatrimoineRepository;
use App\Repository\ProgrammeRepository;
use App\Repository\RecommandationRepository;
use App\Repository\SceneRepository;
use App\Repository\SeancecelluleRepository;
use App\Repository\SeancedepartementRepository;
use App\Repository\SeancefamilleRepository;
use App\Repository\SeancegroupeRepository;
use App\Repository\SeancezoneRepository;
use App\Repository\Visite2Repository;
use App\Repository\VisiteRepository;
use App\Repository\ZoneRepository;
use App\Repository\RegionRepository;
use App\Repository\TypeculteRepository;
use App\Repository\CotisationdepartementRepository;
use App\Repository\DetailcotisationdepartementRepository;
use App\Repository\CotiserdepartementRepository;
use App\Repository\CotisercelluleRepository;
use App\Repository\DepensedepartementRepository;
use App\Repository\DepensecelluleRepository;
use App\Repository\DepensefamilleRepository;
use App\Repository\DepensezoneRepository;
use App\Repository\PresencedepartementRepository;
use App\Repository\PresencecelluleRepository;
use App\Repository\PresencefamilleRepository;
use App\Repository\PresencezoneRepository;
use App\Repository\InvitecelluleRepository;
use App\Repository\InvitegroupeRepository;
use App\Repository\InvitefamilleRepository;
use App\Repository\InvitedepartementRepository;
use App\Repository\InvitezoneRepository;
use App\Repository\DetailcotisationcelluleRepository;
use App\Repository\DetailcotisationgroupeRepository;
use App\Repository\DetailcotisationfamilleRepository;
use App\Repository\DetailcotisationzoneRepository;

use App\Repository\CotisationgroupeRepository;
use App\Repository\CotisationcelluleRepository;
use App\Repository\CotisationfamilleRepository;
use App\Repository\CotisationzoneRepository;
use App\Repository\DepensegroupeRepository;
use App\Repository\CotisergroupeRepository;
use App\Repository\CotiserfamilleRepository;
use App\Repository\CotiserzoneRepository;
use App\Repository\PresencegroupeRepository;

use App\DTO\BilanCulteDTO;
use App\DTO\BilanDepartementCompletDTO;
use App\DTO\CotiserDepartementDTO;
use App\DTO\BilanGroupeCompletDTO;
use App\DTO\BilanCelluleDTO;
use App\DTO\BilanFamilleDTO;
use App\DTO\BilanZoneDTO;

use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BilanController extends AbstractController {

//    #[Route('/bilan', name: 'app_bilan')]
//    public function index(): Response
//    {
//        return $this->render('bilan/index.html.twig', [
//            'controller_name' => 'BilanController',
//        ]);
//    }


    //Nouveau bilan cellule

     #[Route('/bilancellule', name: 'app_bilan_cellule', methods: ['GET', 'POST'])]
    public function rechercheCellule(
        Request $request,
        SeancecelluleRepository $seanceRepository,
        CelluleRepository $celluleRepository,
        CotisationcelluleRepository $cotisationRepository,
        DepensecelluleRepository $depenseRepository,
        CotisercelluleRepository $cotiserRepository,
        PresencecelluleRepository $presenceRepository
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        $eglise = $user->getEglise();
        
        if (!$eglise) {
            $this->addFlash('error', 'Aucune église associée à votre compte.');
            return $this->redirectToRoute('app_dashboard');
        }
        
        $cellules = $celluleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        
        $form = $this->createForm(RecherchecelluleType::class);
        $form->handleRequest($request);
        
        $bilanComplet = null;
          $soldeDisponible = 0;
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            
            $dateDebut = $data['dateDebut'] ?? null;
            $dateFin = $data['dateFin'] ?? null;
            $celluleFiltre = $data['cellule'] ?? null;
            
            if ($dateDebut && $dateFin && $dateDebut > $dateFin) {
                $this->addFlash('warning', 'La date de début doit être antérieure à la date de fin.');
            } else {
                // 1. RECHERCHE DES SÉANCES
                $qbSeances = $seanceRepository->createQueryBuilder('s')
                    ->where('s.eglise = :eglise')
                    ->andWhere('s.deletedAt IS NULL')
                    ->setParameter('eglise', $eglise);
                
                if ($celluleFiltre) {
                    $qbSeances->andWhere('s.cellule = :cellule')
                        ->setParameter('cellule', $celluleFiltre);
                }
                if ($dateDebut) {
                    $qbSeances->andWhere('s.datesuper >= :dateDebut')
                        ->setParameter('dateDebut', $dateDebut);
                }
                if ($dateFin) {
                    $qbSeances->andWhere('s.datesuper <= :dateFin')
                        ->setParameter('dateFin', $dateFin);
                }
                
                $seances = $qbSeances->getQuery()->getResult();
                
                // 2. RECHERCHE DES COTISATIONS
                $qbCotisations = $cotisationRepository->createQueryBuilder('c')
                    ->where('c.eglise = :eglise')
                    ->andWhere('c.deletedAt IS NULL')
                    ->setParameter('eglise', $eglise);
                
                if ($celluleFiltre) {
                    $qbCotisations->andWhere('c.cellule = :cellule')
                        ->setParameter('cellule', $celluleFiltre);
                }
                if ($dateDebut) {
                    $qbCotisations->andWhere('c.createAt >= :dateDebut')
                        ->setParameter('dateDebut', $dateDebut);
                }
                if ($dateFin) {
                    $qbCotisations->andWhere('c.createAt <= :dateFin')
                        ->setParameter('dateFin', $dateFin);
                }
                
                $cotisations = $qbCotisations->getQuery()->getResult();
                
                // 3. RECHERCHE DES DÉPENSES
                $qbDepenses = $depenseRepository->createQueryBuilder('d')
                    ->where('d.eglise = :eglise')
                    ->andWhere('d.deletedAt IS NULL')
                    ->setParameter('eglise', $eglise);
                
                if ($celluleFiltre) {
                    $qbDepenses->andWhere('d.cellule = :cellule')
                        ->setParameter('cellule', $celluleFiltre);
                }
                if ($dateDebut) {
                    $qbDepenses->andWhere('d.datedepense >= :dateDebut')
                        ->setParameter('dateDebut', $dateDebut);
                }
                if ($dateFin) {
                    $qbDepenses->andWhere('d.datedepense <= :dateFin')
                        ->setParameter('dateFin', $dateFin);
                }
                
                $depenses = $qbDepenses->getQuery()->getResult();

                
                // 4. RECHERCHE DES PAIEMENTS
                $cotisationIds = array_map(fn($c) => $c->getId(), $cotisations);
                $paiements = [];
                if (!empty($cotisationIds)) {
                    $paiements = $cotiserRepository->createQueryBuilder('p')
                        ->where('p.cotisationcellule IN (:cotisationIds)')
                        ->andWhere('p.deletedAt IS NULL')
                        ->setParameter('cotisationIds', $cotisationIds)
                        ->getQuery()
                        ->getResult();
                }
                
                // 5. RECHERCHE DES PRÉSENCES
                $seanceIds = array_map(fn($s) => $s->getId(), $seances);
                $presences = [];
                if (!empty($seanceIds)) {
                    $presences = $presenceRepository->createQueryBuilder('p')
                        ->where('p.eglise = :eglise')
                        ->andWhere('p.deletedAt IS NULL')
                        ->andWhere('p.seancecellule IN (:seanceIds)')
                        ->setParameter('eglise', $eglise)
                        ->setParameter('seanceIds', $seanceIds)
                        ->getQuery()
                        ->getResult();
                }

                //6.SOLDE DE LA CELLULE OU DES CELLULES
             //  $celluleSelectionnee = $formRecherche->get('cellule')->getData();

           $soldeDisponible = 0;

            if ($celluleFiltre) {

                // Solde de la cellule sélectionnée
                foreach ($celluleFiltre->getSolecellules() as $solecellule) {

                    $soldeDisponible += (float) $solecellule->getMontant();
                }

            } else {

                // Somme des soldes des cellules de la même église
                $cellules = $celluleRepository->findBy([
                    'eglise' => $eglise,
                    'deletedAt' => null
                ]);

                foreach ($cellules as $cellule) {

                    foreach ($cellule->getSolecellules() as $solecellule) {

                        $soldeDisponible += (float) $solecellule->getMontant();
                    }
                }

                
            }
                
                $bilanComplet = new BilanCelluleDTO(
                    $seances,
                    $cotisations,
                    $depenses,
                    $paiements,
                    $presences,
                    
                    $celluleFiltre ? [$celluleFiltre] : null
                );
                
                
                if ($bilanComplet->isEmpty()) {
                    $this->addFlash('info', 'Aucune donnée trouvée pour les critères sélectionnés.');
                }
            }
        }
        
        return $this->render('bilan/activite/activite_cellule.html.twig', [
            'form_recherche' => $form->createView(),
            'bilanComplet' => $bilanComplet,
            'cellules' => $cellules,
            'soldeDisponible' => $soldeDisponible,
        ]);
    }

     //Bilan cellule par le User
       #[Route('/bilancellulebyuser', name: 'app_bilan_cellule2', methods: ['GET', 'POST'])]
        public function rechercheCelluleByUser(
            Request $request,
            SeancecelluleRepository $seancecelluleRepository,
            CotisationcelluleRepository $cotisationcelluleRepository,
            PresencecelluleRepository $presencecelluleRepository,
            DepensecelluleRepository $depensecelluleRepository,
            CotisercelluleRepository $cotisercelluleRepository,
            InvitecelluleRepository $invitecelluleRepository,
            DetailcotisationcelluleRepository $detailcotisationcelluleRepository,
            CelluleRepository $celluleRepository,
            FideleRepository $fideleRepository
        ): Response {
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
            
            $user = $this->getUser();
            $eglise = $user->getEglise();
            
            // Récupérer la cellule de l'utilisateur connecté
            $cellule = $celluleRepository->findOneByUser($user);
            
            if (!$cellule) {
                $this->addFlash('warning', 'Aucune cellule associée à votre compte.');
                return $this->redirectToRoute('seancecellule_index');
            }
            
            // Initialisation des variables
            $seancecellules = [];
            $cotisations = [];
            $depenses = [];
            $presences = [];
            $paiements = [];
            $invitecellules = [];
            $detailcotisations = [];
            $totalCotisations = 0;
            $totalDepenses = 0;
            $totalPresences = 0;
            $totalInvites = 0;
            $totalDetailCotisations = 0;
            $totalMontantPayeDetails = 0;
            
            // Création du formulaire
            $form = $this->createForm(BilancelluleuserType::class);
            $form->handleRequest($request);
            
            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();
                $dateDebut = $data['dateDebut'] ?? null;
                $dateFin = $data['dateFin'] ?? null;
                
                // Validation des dates
                if ($dateDebut && $dateFin && $dateDebut > $dateFin) {
                    $this->addFlash('warning', 'La date de début doit être antérieure à la date de fin.');
                } else {
                    // 1. RECHERCHE DES SÉANCES DE LA CELLULE
                    $qbSeances = $seancecelluleRepository->createQueryBuilder('s')
                        ->where('s.eglise = :eglise')
                        ->andWhere('s.cellule = :cellule')
                        ->andWhere('s.deletedAt IS NULL')
                        ->setParameter('eglise', $eglise)
                        ->setParameter('cellule', $cellule);
                    
                    if ($dateDebut) {
                        $qbSeances->andWhere('s.datesuper >= :dateDebut')
                            ->setParameter('dateDebut', $dateDebut);
                    }
                    if ($dateFin) {
                        $qbSeances->andWhere('s.datesuper <= :dateFin')
                            ->setParameter('dateFin', $dateFin);
                    }
                    
                    $seancecellules = $qbSeances->orderBy('s.datesuper', 'DESC')->getQuery()->getResult();
                    
                    // 2. RECHERCHE DES COTISATIONS
                    $qbCotisations = $cotisationcelluleRepository->createQueryBuilder('c')
                        ->where('c.eglise = :eglise')
                        ->andWhere('c.cellule = :cellule')
                        ->andWhere('c.deletedAt IS NULL')
                        ->setParameter('eglise', $eglise)
                        ->setParameter('cellule', $cellule);
                    
                    if ($dateDebut) {
                        $qbCotisations->andWhere('c.createAt >= :dateDebut')
                            ->setParameter('dateDebut', $dateDebut);
                    }
                    if ($dateFin) {
                        $qbCotisations->andWhere('c.createAt <= :dateFin')
                            ->setParameter('dateFin', $dateFin);
                    }
                    
                    $cotisations = $qbCotisations->getQuery()->getResult();
                    
                    // Calcul du total des cotisations
                    foreach ($cotisations as $cotisation) {
                        $totalCotisations += $cotisation->getMontant() ?? 0;
                    }
                    
                    // 3. RECHERCHE DES DÉPENSES
                    $qbDepenses = $depensecelluleRepository->createQueryBuilder('d')
                        ->where('d.eglise = :eglise')
                        ->andWhere('d.cellule = :cellule')
                        ->andWhere('d.deletedAt IS NULL')
                        ->setParameter('eglise', $eglise)
                        ->setParameter('cellule', $cellule);
                    
                    if ($dateDebut) {
                        $qbDepenses->andWhere('d.datedepense >= :dateDebut')
                            ->setParameter('dateDebut', $dateDebut);
                    }
                    if ($dateFin) {
                        $qbDepenses->andWhere('d.datedepense <= :dateFin')
                            ->setParameter('dateFin', $dateFin);
                    }
                    
                    $depenses = $qbDepenses->getQuery()->getResult();
                    
                    // Calcul du total des dépenses
                    foreach ($depenses as $depense) {
                        $totalDepenses += $depense->getMontant() ?? 0;
                    }
                    
                    // 4. RECHERCHE DES PRÉSENCES
                    $seanceIds = array_map(fn($s) => $s->getId(), $seancecellules);
                    if (!empty($seanceIds)) {
                        $presences = $presencecelluleRepository->createQueryBuilder('p')
                            ->where('p.eglise = :eglise')
                            ->andWhere('p.cellule = :cellule')
                            ->andWhere('p.deletedAt IS NULL')
                            ->andWhere('p.seancecellule IN (:seanceIds)')
                            ->setParameter('eglise', $eglise)
                            ->setParameter('cellule', $cellule)
                            ->setParameter('seanceIds', $seanceIds)
                            ->getQuery()
                            ->getResult();
                        
                        $totalPresences = count($presences);
                    }
                    
                    // 5. RECHERCHE DES PAIEMENTS (Cotisercellule)
                    $cotisationIds = array_map(fn($c) => $c->getId(), $cotisations);
                    if (!empty($cotisationIds)) {
                        $paiements = $cotisercelluleRepository->createQueryBuilder('p')
                            ->where('p.cotisationcellule IN (:cotisationIds)')
                            ->andWhere('p.deletedAt IS NULL')
                            ->setParameter('cotisationIds', $cotisationIds)
                            ->getQuery()
                            ->getResult();
                    }

                    // 6. RECHERCHE DES INVITES (à partir des séances)
                    if (!empty($seanceIds)) {
                        $invitecellules = $invitecelluleRepository->createQueryBuilder('i')
                            ->where('i.eglise = :eglise')
                            ->andWhere('i.deletedAt IS NULL')
                            ->andWhere('i.seancecellule IN (:seanceIds)')
                            ->setParameter('eglise', $eglise)
                            ->setParameter('seanceIds', $seanceIds)
                            ->getQuery()
                            ->getResult();
                        
                        $totalInvites = count($invitecellules);
                    }
                    
                    // 7. RECHERCHE DES DETAILS DE COTISATION (Detailcotisationcellule)
                    // Detailcotisationcellule a une relation directe avec Cellule
                    $qbDetailCotisations = $detailcotisationcelluleRepository->createQueryBuilder('dc')
                        ->where('dc.eglise = :eglise')
                        ->andWhere('dc.cellule = :cellule')
                        ->andWhere('dc.deletedAt IS NULL')
                        ->setParameter('eglise', $eglise)
                        ->setParameter('cellule', $cellule);
                    
                    if ($dateDebut) {
                        $qbDetailCotisations->andWhere('dc.datedetail >= :dateDebut')
                            ->setParameter('dateDebut', $dateDebut);
                    }
                    if ($dateFin) {
                        $qbDetailCotisations->andWhere('dc.datedetail <= :dateFin')
                            ->setParameter('dateFin', $dateFin);
                    }
                    
                    $detailcotisations = $qbDetailCotisations->orderBy('dc.datedetail', 'DESC')->getQuery()->getResult();
                    
                    // Calcul des totaux des détails de cotisation
                    foreach ($detailcotisations as $detail) {
                        $totalDetailCotisations += $detail->getMontant() ?? 0;
                        $totalMontantPayeDetails += $detail->getMontantpayer() ?? 0;
                    }
                }
            }
            
            // Récupérer les membres de la cellule
            $membres = $fideleRepository->findBy(['cellule' => $cellule, 'deletedAt' => NULL]);
            
            // Récupérer les soldes
            $soldes = $cellule->getSolecellules();
            $soldeTotal = 0;
            foreach ($soldes as $solde) {
                $soldeTotal += (float) $solde->getMontant();
            }
            
            return $this->render('bilan/activite/activite_cellulebyuser.html.twig', [
                'form_recherche' => $form->createView(),
                'seancecellules' => $seancecellules,
                'cotisations' => $cotisations,
                'depenses' => $depenses,
                'invitecellules' => $invitecellules,
                'presences' => $presences,
                'paiements' => $paiements,
                'detailcotisations' => $detailcotisations,
                'cellule' => $cellule,
                'membres' => $membres,
                'totalCotisations' => $totalCotisations,
                'totalDepenses' => $totalDepenses,
                'totalPresences' => $totalPresences,
                'totalInvites' => $totalInvites,
                'totalDetailCotisations' => $totalDetailCotisations,
                'totalMontantPayeDetails' => $totalMontantPayeDetails,
                'soldeTotal' => $soldeTotal,
                'nbMembres' => count($membres),
                'nbSeances' => count($seancecellules),
                'nbCotisations' => count($cotisations),
                'nbDepenses' => count($depenses),
                'nbPaiements' => count($paiements),
                'nbInvites' => count($invitecellules),
                'nbDetailCotisations' => count($detailcotisations),
            ]);
        }


     #[Route('/bilandepartementbyuser', name: 'app_bilan_departement2', methods: ['GET', 'POST'])]
        public function rechercheDepartementByUser(
            Request $request,
            SeancedepartementRepository $seancedepartementRepository,
            CotisationdepartementRepository $cotisationdepartementRepository,
            PresencedepartementRepository $presencedepartementRepository,
            DepensedepartementRepository $depensedepartementRepository,
            CotiserdepartementRepository $cotiserdepartementRepository,
            InvitedepartementRepository $invitedepartementRepository,
            DetailcotisationdepartementRepository $detailcotisationdepartementRepository,
            DepartementRepository $departementRepository,
            FideleRepository $fideleRepository
        ): Response {
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
            
            $user = $this->getUser();
            $eglise = $user->getEglise();
            
            // Récupérer la departement de l'utilisateur connecté
            $departement = $departementRepository->findOneByUser($user);
            
            if (!$departement) {
                $this->addFlash('warning', 'Aucune departement associée à votre compte.');
                return $this->redirectToRoute('seancedepartement_index');
            }
            
            // Initialisation des variables
            $seancedepartements = [];
            $cotisations = [];
            $depenses = [];
            $presences = [];
            $paiements = [];
            $invitedepartements = [];
            $detailcotisations = [];
            $totalCotisations = 0;
            $totalDepenses = 0;
            $totalPresences = 0;
            $totalInvites = 0;
            $totalDetailCotisations = 0;
            $totalMontantPayeDetails = 0;
            
            // Création du formulaire
            $form = $this->createForm(BilancelluleuserType::class);
            $form->handleRequest($request);
            
            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();
                $dateDebut = $data['dateDebut'] ?? null;
                $dateFin = $data['dateFin'] ?? null;
                
                // Validation des dates
                if ($dateDebut && $dateFin && $dateDebut > $dateFin) {
                    $this->addFlash('warning', 'La date de début doit être antérieure à la date de fin.');
                } else {
                    // 1. RECHERCHE DES SÉANCES DE LA CELLULE
                    $qbSeances = $seancedepartementRepository->createQueryBuilder('s')
                        ->where('s.eglise = :eglise')
                        ->andWhere('s.departement = :departement')
                        ->andWhere('s.deletedAt IS NULL')
                        ->setParameter('eglise', $eglise)
                        ->setParameter('departement', $departement);
                    
                    if ($dateDebut) {
                        $qbSeances->andWhere('s.datesuper >= :dateDebut')
                            ->setParameter('dateDebut', $dateDebut);
                    }
                    if ($dateFin) {
                        $qbSeances->andWhere('s.datesuper <= :dateFin')
                            ->setParameter('dateFin', $dateFin);
                    }
                    
                    $seancedepartements = $qbSeances->orderBy('s.datesuper', 'DESC')->getQuery()->getResult();
                    
                    // 2. RECHERCHE DES COTISATIONS
                    $qbCotisations = $cotisationdepartementRepository->createQueryBuilder('c')
                        ->where('c.eglise = :eglise')
                        ->andWhere('c.departement = :departement')
                        ->andWhere('c.deletedAt IS NULL')
                        ->setParameter('eglise', $eglise)
                        ->setParameter('departement', $departement);
                    
                    if ($dateDebut) {
                        $qbCotisations->andWhere('c.createAt >= :dateDebut')
                            ->setParameter('dateDebut', $dateDebut);
                    }
                    if ($dateFin) {
                        $qbCotisations->andWhere('c.createAt <= :dateFin')
                            ->setParameter('dateFin', $dateFin);
                    }
                    
                    $cotisations = $qbCotisations->getQuery()->getResult();
                    
                    // Calcul du total des cotisations
                    foreach ($cotisations as $cotisation) {
                        $totalCotisations += $cotisation->getMontant() ?? 0;
                    }
                    
                    // 3. RECHERCHE DES DÉPENSES
                    $qbDepenses = $depensedepartementRepository->createQueryBuilder('d')
                        ->where('d.eglise = :eglise')
                        ->andWhere('d.departement = :departement')
                        ->andWhere('d.deletedAt IS NULL')
                        ->setParameter('eglise', $eglise)
                        ->setParameter('departement', $departement);
                    
                    if ($dateDebut) {
                        $qbDepenses->andWhere('d.datedepense >= :dateDebut')
                            ->setParameter('dateDebut', $dateDebut);
                    }
                    if ($dateFin) {
                        $qbDepenses->andWhere('d.datedepense <= :dateFin')
                            ->setParameter('dateFin', $dateFin);
                    }
                    
                    $depenses = $qbDepenses->getQuery()->getResult();
                    
                    // Calcul du total des dépenses
                    foreach ($depenses as $depense) {
                        $totalDepenses += $depense->getMontant() ?? 0;
                    }
                    
                    // 4. RECHERCHE DES PRÉSENCES
                    $seanceIds = array_map(fn($s) => $s->getId(), $seancedepartements);
                    if (!empty($seanceIds)) {
                        $presences = $presencedepartementRepository->createQueryBuilder('p')
                            ->where('p.eglise = :eglise')
                            ->andWhere('p.departement = :departement')
                            ->andWhere('p.deletedAt IS NULL')
                            ->andWhere('p.seancedepartement IN (:seanceIds)')
                            ->setParameter('eglise', $eglise)
                            ->setParameter('departement', $departement)
                            ->setParameter('seanceIds', $seanceIds)
                            ->getQuery()
                            ->getResult();
                        
                        $totalPresences = count($presences);
                    }
                    
                    // 5. RECHERCHE DES PAIEMENTS (Cotiserdepartement)
                    $cotisationIds = array_map(fn($c) => $c->getId(), $cotisations);
                    if (!empty($cotisationIds)) {
                        $paiements = $cotiserdepartementRepository->createQueryBuilder('p')
                            ->where('p.cotisationdepartement IN (:cotisationIds)')
                            ->andWhere('p.deletedAt IS NULL')
                            ->setParameter('cotisationIds', $cotisationIds)
                            ->getQuery()
                            ->getResult();
                    }

                    // 6. RECHERCHE DES INVITES (à partir des séances)
                    if (!empty($seanceIds)) {
                        $invitedepartements = $invitedepartementRepository->createQueryBuilder('i')
                            ->where('i.eglise = :eglise')
                            ->andWhere('i.deletedAt IS NULL')
                            ->andWhere('i.seancedepartement IN (:seanceIds)')
                            ->setParameter('eglise', $eglise)
                            ->setParameter('seanceIds', $seanceIds)
                            ->getQuery()
                            ->getResult();
                        
                        $totalInvites = count($invitedepartements);
                    }
                    
                    // 7. RECHERCHE DES DETAILS DE COTISATION (Detailcotisationdepartement)
                    // Detailcotisationdepartement a une relation directe avec Departement
                    $qbDetailCotisations = $detailcotisationdepartementRepository->createQueryBuilder('dc')
                        ->where('dc.eglise = :eglise')
                        ->andWhere('dc.departement = :departement')
                        ->andWhere('dc.deletedAt IS NULL')
                        ->setParameter('eglise', $eglise)
                        ->setParameter('departement', $departement);
                    
                    if ($dateDebut) {
                        $qbDetailCotisations->andWhere('dc.datedetail >= :dateDebut')
                            ->setParameter('dateDebut', $dateDebut);
                    }
                    if ($dateFin) {
                        $qbDetailCotisations->andWhere('dc.datedetail <= :dateFin')
                            ->setParameter('dateFin', $dateFin);
                    }
                    
                    $detailcotisations = $qbDetailCotisations->orderBy('dc.datedetail', 'DESC')->getQuery()->getResult();
                    
                    // Calcul des totaux des détails de cotisation
                    foreach ($detailcotisations as $detail) {
                        $totalDetailCotisations += $detail->getMontant() ?? 0;
                        $totalMontantPayeDetails += $detail->getMontantpayer() ?? 0;
                    }
                }
            }
            
            // Récupérer les membres de la departement
            
            $membres = $fideleRepository->findFidelesByDepartement($user->getDepartement()->getId());
            
            // Récupérer les soldes
            $soldes = $departement->getSoldedepartements();
            $soldeTotal = 0;
            foreach ($soldes as $solde) {
                $soldeTotal += (float) $solde->getMontant();
            }
            
            return $this->render('bilan/activite/activite_departementbyuser.html.twig', [
                'form_recherche' => $form->createView(),
                'seancedepartements' => $seancedepartements,
                'cotisations' => $cotisations,
                'depenses' => $depenses,
                'invitedepartements' => $invitedepartements,
                'presences' => $presences,
                'paiements' => $paiements,
                'detailcotisations' => $detailcotisations,
                'departement' => $departement,
                'membres' => $membres,
                'totalCotisations' => $totalCotisations,
                'totalDepenses' => $totalDepenses,
                'totalPresences' => $totalPresences,
                'totalInvites' => $totalInvites,
                'totalDetailCotisations' => $totalDetailCotisations,
                'totalMontantPayeDetails' => $totalMontantPayeDetails,
                'soldeTotal' => $soldeTotal,
                'nbMembres' => count($membres),
                'nbSeances' => count($seancedepartements),
                'nbCotisations' => count($cotisations),
                'nbDepenses' => count($depenses),
                'nbPaiements' => count($paiements),
                'nbInvites' => count($invitedepartements),
                'nbDetailCotisations' => count($detailcotisations),
            ]);
        }

    //Bilan departement
     #[Route('/bilandepartement', name: 'app_bilan_departement', methods: ['GET', 'POST'])]
    public function rechercheDepartement(
        Request $request, 
        SeancedepartementRepository $seancedepartementRepository,
        DepartementRepository $departementRepository,
        CotisationdepartementRepository $cotisationRepository,
        DepensedepartementRepository $depenseRepository,
        CotiserdepartementRepository $cotiserRepository,
        DetailcotisationdepartementRepository $detailRepository,
        PresencedepartementRepository $presenceRepository

    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        $eglise = $user->getEglise();
        
        if (!$eglise) {
            $this->addFlash('warning', 'Aucune église associée à votre compte.');
            return $this->redirectToRoute('seancedepartement_listeparticipant');
        }
         
        // Récupérer tous les départements pour l'église
        $departements = $departementRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        
        // Créer le formulaire
        $form = $this->createForm(RecherchedepartementType::class);
        $form->handleRequest($request);
        
        $bilanComplet = null;
        $seancedepartements = [];
        $cotisations = [];
        $depenses = [];
        $allPaiements = [];
        $selectedCotisation = null;
        $cotisationDetails = [];
        $versements = [];
          $presences = [];
        
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            
            $dateDebut = $data['dateDebut'] ?? null;
            $dateFin = $data['dateFin'] ?? null;
            $departementFiltre = $data['departement'] ?? null;
            
            // Validation des dates
            if ($dateDebut && $dateFin && $dateDebut > $dateFin) {
                $this->addFlash('warning', 'La date de début doit être antérieure à la date de fin.');
            } else {
                $limit = 1000;
                
                // 1. RECHERCHE DES ACTIVITÉS
                $criteresActivites = ["eglise" => $eglise->getId()];
                if ($departementFiltre) {
                    $criteresActivites["departement"] = $departementFiltre->getId();
                }
                $seancedepartements = $seancedepartementRepository->rechercheDepartement(
                    $criteresActivites, 
                    $dateDebut, 
                    $dateFin, 
                    $limit
                );
                
                // 2. RECHERCHE DES COTISATIONS
                $criteresCotisations = ["eglise" => $eglise->getId()];
                if ($departementFiltre) {
                    $criteresCotisations["departement"] = $departementFiltre->getId();
                }
                $cotisations = $cotisationRepository->rechercheCotisationsByDepartement(
                    $criteresCotisations,
                    $dateDebut,
                    $dateFin,
                    $limit
                );
                
                // 3. RECHERCHE DES DÉPENSES
                $criteresDepenses = ["eglise" => $eglise->getId()];
                if ($departementFiltre) {
                    $criteresDepenses["departement"] = $departementFiltre->getId();
                }
                $depenses = $depenseRepository->rechercheDepenses(
                    $criteresDepenses,
                    $dateDebut,
                    $dateFin,
                    $limit
                );
                
                // 4. RECHERCHE DES PAIEMENTS (Cotiserdepartement)
                // Récupérer tous les paiements pour les périodes sélectionnées
                $allPaiements = [];
                $cotisationIds = [];
                
                foreach ($cotisations as $cotisation) {
                    $cotisationIds[] = $cotisation->getId();
                }
                
                if (!empty($cotisationIds)) {
                    // Requête pour récupérer tous les paiements liés aux cotisations trouvées
                    $allPaiements = $cotiserRepository->createQueryBuilder('c')
                        ->where('c.cotisationdepartement IN (:cotisationIds)')
                        ->andWhere('c.deletedAt IS NULL')
                        ->setParameter('cotisationIds', $cotisationIds)
                        ->getQuery()
                        ->getResult();
                    
                    // Filtrer par date si nécessaire
                    $seanceIds = array_map(fn($seance) => $seance->getId(), $seancedepartements);
                     

                        $presences = [];
                    $seanceIds = array_map(fn($seance) => $seance->getId(), $seancedepartements);
                    
                    if (!empty($seanceIds)) {
                        $presences = $presenceRepository->createQueryBuilder('p')
                            ->where('p.eglise = :eglise')
                            ->andWhere('p.deletedAt IS NULL')
                            ->andWhere('p.seancedepartement IN (:seanceIds)')
                            ->setParameter('eglise', $eglise)
                            ->setParameter('seanceIds', $seanceIds)
                            ->getQuery()
                            ->getResult();
                    }
                        if ($dateDebut && $dateFin) {
                        $allPaiements = array_filter($allPaiements, function($paiement) use ($dateDebut, $dateFin) {
                            $datePaiement = $paiement->getDatecotiser();
                            return $datePaiement && $datePaiement >= $dateDebut && $datePaiement <= $dateFin;
                        });
                    }
                }
                
                // Calculer les montants payés et restes pour chaque cotisation
                foreach ($cotisations as $cotisation) {
                    $totalPaye = 0;
                    foreach ($allPaiements as $paiement) {
                        if ($paiement->getCotisationdepartement() && 
                            $paiement->getCotisationdepartement()->getId() == $cotisation->getId()) {
                            $totalPaye += $paiement->getMontantpayer() ?? 0;
                        }
                    }
                    // Stocker les valeurs calculées
                    $cotisation->montantPayeCalcule = $totalPaye;
                    $cotisation->resteCalcule = $cotisation->getMontant() - $totalPaye;
                }
            }
            
            // Créer le DTO principal avec toutes les données
            $bilanComplet = new BilanDepartementCompletDTO(
                $seancedepartements, 
                $cotisations, 
                $depenses,
                $allPaiements,
                 $presences   // Passer directement les paiements au constructeur
            );
            
            if ($bilanComplet->isEmpty()) {
                $this->addFlash('info', 'Aucune donnée trouvée pour les critères sélectionnés.');
            } else {
                $this->addFlash('success', sprintf(
                    'Bilan généré : %d activité(s), %d cotisation(s), %d dépense(s), %d paiement(s)',
                    count($seancedepartements),
                    count($cotisations),
                    count($depenses),
                    count($allPaiements),
                    count($presences)
                ));
            }
        }
        
        // Gestion de l'affichage des détails d'une cotisation (AJAX)
        if ($request->isXmlHttpRequest() && $request->get('cotisation_id')) {
            $cotisationId = $request->get('cotisation_id');
            $selectedCotisation = $cotisationRepository->find($cotisationId);
            
            if ($selectedCotisation) {
                // Récupérer les paiements (Cotiserdepartement) pour cette cotisation
                $versements = $cotiserRepository->findBy(
                    ['cotisationdepartement' => $selectedCotisation, 'deletedAt' => null],
                    ['datecotiser' => 'DESC']
                );
                
                // Calculer le total payé
                $totalPaye = 0;
                foreach ($versements as $versement) {
                    $totalPaye += $versement->getMontantpayer() ?? 0;
                }
                
                $reste = $selectedCotisation->getMontant() - $totalPaye;
                
                return $this->render('bilan/activite/_cotisation_details_modal.html.twig', [
                    'cotisation' => $selectedCotisation,
                    'versements' => $versements,
                    'totalPaye' => $totalPaye,
                    'reste' => $reste,
                ]);
            }
            
            return $this->json(['error' => 'Cotisation non trouvée'], 404);
        }
        
        return $this->render('bilan/activite/activite_departement.html.twig', [
            'form_recherche' => $form->createView(),
            'bilanComplet' => $bilanComplet,
            'departements' => $departements,
            'presences' => $presences
             
        ]);
    }
   

      #[Route('/bilangroupe', name: 'app_bilan_groupe', methods: ['GET', 'POST'])]
    public function rechercheGroupe(
        Request $request,
        SeancegroupeRepository $seancegroupeRepository,
        GroupeRepository $groupeRepository,
        CotisationgroupeRepository $cotisationRepository,
        DepensegroupeRepository $depenseRepository,
        CotisergroupeRepository $cotiserRepository,
        PresencegroupeRepository $presenceRepository
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        $eglise = $user->getEglise();
        
        if (!$eglise) {
            $this->addFlash('error', 'Aucune église associée à votre compte.');
            return $this->redirectToRoute('app_login');
        }
        
        $groupes = $groupeRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        
        $form = $this->createForm(Recherchegroupe2Type::class);
        $form->handleRequest($request);
        
        $bilanComplet = null;
        $seances = [];
        $cotisations = [];
        $depenses = [];
        $allPaiements = [];
        $presences = [];
        
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            
            $dateDebut = $data['dateDebut'] ?? null;
            $dateFin = $data['dateFin'] ?? null;
            $groupeFiltre = $data['groupe'] ?? null;
            
            if ($dateDebut && $dateFin && $dateDebut > $dateFin) {
                $this->addFlash('warning', 'La date de début doit être antérieure à la date de fin.');
            } else {
                // 1. RECHERCHE DES SÉANCES
                $qbSeances = $seancegroupeRepository->createQueryBuilder('s')
                    ->where('s.eglise = :eglise')
                    ->andWhere('s.deletedAt IS NULL')
                    ->setParameter('eglise', $eglise);
                
                if ($groupeFiltre) {
                    $qbSeances->andWhere('s.groupe = :groupe')
                        ->setParameter('groupe', $groupeFiltre);
                }
                if ($dateDebut) {
                    $qbSeances->andWhere('s.datesuper >= :dateDebut')
                        ->setParameter('dateDebut', $dateDebut);
                }
                if ($dateFin) {
                    $qbSeances->andWhere('s.datesuper <= :dateFin')
                        ->setParameter('dateFin', $dateFin);
                }
                
                $seances = $qbSeances->getQuery()->getResult();
                
                // 2. RECHERCHE DES COTISATIONS
                $qbCotisations = $cotisationRepository->createQueryBuilder('c')
                    ->where('c.eglise = :eglise')
                    ->andWhere('c.deletedAt IS NULL')
                    ->setParameter('eglise', $eglise);
                
                if ($groupeFiltre) {
                    $qbCotisations->andWhere('c.groupe = :groupe')
                        ->setParameter('groupe', $groupeFiltre);
                }
                if ($dateDebut) {
                    $qbCotisations->andWhere('c.createAt >= :dateDebut')
                        ->setParameter('dateDebut', $dateDebut);
                }
                if ($dateFin) {
                    $qbCotisations->andWhere('c.createAt <= :dateFin')
                        ->setParameter('dateFin', $dateFin);
                }
                
                $cotisations = $qbCotisations->getQuery()->getResult();
                
                // 3. RECHERCHE DES DÉPENSES
                $qbDepenses = $depenseRepository->createQueryBuilder('d')
                    ->where('d.eglise = :eglise')
                    ->andWhere('d.deletedAt IS NULL')
                    ->setParameter('eglise', $eglise);
                
                if ($groupeFiltre) {
                    $qbDepenses->andWhere('d.groupe = :groupe')
                        ->setParameter('groupe', $groupeFiltre);
                }
                if ($dateDebut) {
                    $qbDepenses->andWhere('d.datedepense >= :dateDebut')
                        ->setParameter('dateDebut', $dateDebut);
                }
                if ($dateFin) {
                    $qbDepenses->andWhere('d.datedepense <= :dateFin')
                        ->setParameter('dateFin', $dateFin);
                }
                
                $depenses = $qbDepenses->getQuery()->getResult();
                
                // 4. RECHERCHE DES PAIEMENTS
                $cotisationIds = array_map(fn($c) => $c->getId(), $cotisations);
                if (!empty($cotisationIds)) {
                    $allPaiements = $cotiserRepository->createQueryBuilder('p')
                        ->where('p.cotisationgroupe IN (:cotisationIds)')
                        ->andWhere('p.deletedAt IS NULL')
                        ->setParameter('cotisationIds', $cotisationIds)
                        ->getQuery()
                        ->getResult();
                }
                
                // 5. RECHERCHE DES PRÉSENCES
                $seanceIds = array_map(fn($s) => $s->getId(), $seances);
                if (!empty($seanceIds)) {
                    $presences = $presenceRepository->createQueryBuilder('p')
                        ->where('p.eglise = :eglise')
                        ->andWhere('p.deletedAt IS NULL')
                        ->andWhere('p.seancegroupe IN (:seanceIds)')
                        ->setParameter('eglise', $eglise)
                        ->setParameter('seanceIds', $seanceIds)
                        ->getQuery()
                        ->getResult();
                }
                
                // Calculer les montants payés pour chaque cotisation
                foreach ($cotisations as $cotisation) {
                    $totalPaye = 0;
                    foreach ($allPaiements as $paiement) {
                        if ($paiement->getCotisationgroupe() && 
                            $paiement->getCotisationgroupe()->getId() == $cotisation->getId()) {
                            $totalPaye += $paiement->getMontantpayer() ?? 0;
                        }
                    }
                    $cotisation->montantPayeCalcule = $totalPaye;
                    $cotisation->resteCalcule = $cotisation->getMontant() - $totalPaye;
                }
            }
            
            $bilanComplet = new BilanGroupeCompletDTO(
                $seances,
                $cotisations,
                $depenses,
                $allPaiements,
                $presences,
                $groupes
            );
            
            if ($bilanComplet->isEmpty()) {
                $this->addFlash('info', 'Aucune donnée trouvée pour les critères sélectionnés.');
            }
        }
        
        return $this->render('bilan/activite/activite_groupe.html.twig', [
            'form_recherche' => $form->createView(),
            'bilanComplet' => $bilanComplet,
            'groupes' => $groupes,
        ]);
    }

    #[Route('/bilangroupebyuser', name: 'app_bilan_groupe2', methods: ['GET', 'POST'])]
public function rechercheGroupeByUser(
    Request $request,
    SeancegroupeRepository $seancegroupeRepository,
    CotisationgroupeRepository $cotisationgroupeRepository,
    PresencegroupeRepository $presencegroupeRepository,
    DepensegroupeRepository $depensegroupeRepository,
    CotisergroupeRepository $cotisergroupeRepository,
    InvitegroupeRepository $invitegroupeRepository,
    DetailcotisationgroupeRepository $detailcotisationgroupeRepository,
    GroupeRepository $groupeRepository,
    FideleRepository $fideleRepository
): Response {
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    
    $user = $this->getUser();
    $eglise = $user->getEglise();
    
    // Récupérer le groupe de l'utilisateur connecté
    $groupe = $groupeRepository->findOneByUser($user);
    
    if (!$groupe) {
        $this->addFlash('warning', 'Aucun groupe associé à votre compte.');
        return $this->redirectToRoute('seancegroupe_index');
    }
    
    // Initialisation des variables
    $seancegroupes = [];
    $cotisations = [];
    $depenses = [];
    $presences = [];
    $paiements = [];
    $invitegroupes = [];
    $detailcotisations = [];
    $totalCotisations = 0;
    $totalDepenses = 0;
    $totalPresences = 0;
    $totalInvites = 0;
    $totalDetailCotisations = 0;
    $totalMontantPayeDetails = 0;
    
    // Création du formulaire
    $form = $this->createForm(BilancelluleuserType::class);
    $form->handleRequest($request);
    
    if ($form->isSubmitted() && $form->isValid()) {
        $data = $form->getData();
        $dateDebut = $data['dateDebut'] ?? null;
        $dateFin = $data['dateFin'] ?? null;
        
        // Validation des dates
        if ($dateDebut && $dateFin && $dateDebut > $dateFin) {
            $this->addFlash('warning', 'La date de début doit être antérieure à la date de fin.');
        } else {
            // 1. RECHERCHE DES SÉANCES DU GROUPE
            $qbSeances = $seancegroupeRepository->createQueryBuilder('s')
                ->where('s.eglise = :eglise')
                ->andWhere('s.groupe = :groupe')
                ->andWhere('s.deletedAt IS NULL')
                ->setParameter('eglise', $eglise)
                ->setParameter('groupe', $groupe);
            
            if ($dateDebut) {
                $qbSeances->andWhere('s.datesuper >= :dateDebut')
                    ->setParameter('dateDebut', $dateDebut);
            }
            if ($dateFin) {
                $qbSeances->andWhere('s.datesuper <= :dateFin')
                    ->setParameter('dateFin', $dateFin);
            }
            
            $seancegroupes = $qbSeances->orderBy('s.datesuper', 'DESC')->getQuery()->getResult();
            
            // DEBUG: Vérifier les séances trouvées
            // dump('Séances trouvées: ' . count($seancegroupes));
            
            // 2. RECHERCHE DES COTISATIONS
            $qbCotisations = $cotisationgroupeRepository->createQueryBuilder('c')
                ->where('c.eglise = :eglise')
                ->andWhere('c.groupe = :groupe')
                ->andWhere('c.deletedAt IS NULL')
                ->setParameter('eglise', $eglise)
                ->setParameter('groupe', $groupe);
            
            if ($dateDebut) {
                $qbCotisations->andWhere('c.createAt >= :dateDebut')
                    ->setParameter('dateDebut', $dateDebut);
            }
            if ($dateFin) {
                $qbCotisations->andWhere('c.createAt <= :dateFin')
                    ->setParameter('dateFin', $dateFin);
            }
            
            $cotisations = $qbCotisations->getQuery()->getResult();
            
            // DEBUG: Vérifier les cotisations trouvées
            // dump('Cotisations trouvées: ' . count($cotisations));
            
            // Calcul du total des cotisations
            foreach ($cotisations as $cotisation) {
                $totalCotisations += $cotisation->getMontant() ?? 0;
            }
            
            // 3. RECHERCHE DES DÉPENSES
            $qbDepenses = $depensegroupeRepository->createQueryBuilder('d')
                ->where('d.eglise = :eglise')
                ->andWhere('d.groupe = :groupe')
                ->andWhere('d.deletedAt IS NULL')
                ->setParameter('eglise', $eglise)
                ->setParameter('groupe', $groupe);
            
            if ($dateDebut) {
                $qbDepenses->andWhere('d.datedepense >= :dateDebut')
                    ->setParameter('dateDebut', $dateDebut);
            }
            if ($dateFin) {
                $qbDepenses->andWhere('d.datedepense <= :dateFin')
                    ->setParameter('dateFin', $dateFin);
            }
            
            $depenses = $qbDepenses->getQuery()->getResult();
            
            // Calcul du total des dépenses
            foreach ($depenses as $depense) {
                $totalDepenses += $depense->getMontant() ?? 0;
            }
            
            // 4. RECHERCHE DES PRÉSENCES
            $seanceIds = array_map(fn($s) => $s->getId(), $seancegroupes);
            if (!empty($seanceIds)) {
                $presences = $presencegroupeRepository->createQueryBuilder('p')
                    ->where('p.eglise = :eglise')
                    ->andWhere('p.groupe = :groupe')
                    ->andWhere('p.deletedAt IS NULL')
                    ->andWhere('p.seancegroupe IN (:seanceIds)')
                    ->setParameter('eglise', $eglise)
                    ->setParameter('groupe', $groupe)
                    ->setParameter('seanceIds', $seanceIds)
                    ->getQuery()
                    ->getResult();
                
                $totalPresences = count($presences);
            }
            
            // DEBUG: Afficher les IDs des séances
            // dump('IDs des séances: ', $seanceIds);
            
            // 5. RECHERCHE DES PAIEMENTS (Cotisergroupe)
            $cotisationIds = array_map(fn($c) => $c->getId(), $cotisations);
            if (!empty($cotisationIds)) {
                $paiements = $cotisergroupeRepository->createQueryBuilder('p')
                    ->where('p.cotisationgroupe IN (:cotisationIds)')
                    ->andWhere('p.deletedAt IS NULL')
                    ->setParameter('cotisationIds', $cotisationIds)
                    ->getQuery()
                    ->getResult();
            }
            
            // DEBUG: Vérifier les IDs des cotisations
            // dump('IDs des cotisations: ', $cotisationIds);
            // dump('Paiements trouvés: ' . count($paiements));

            // 6. RECHERCHE DES INVITES (à partir des séances)
            if (!empty($seanceIds)) {
                // Vérifions d'abord s'il y a des invités dans la base
                $allInvites = $invitegroupeRepository->findAll();
                // dump('Total invités dans la base: ' . count($allInvites));
                
                $invitegroupes = $invitegroupeRepository->createQueryBuilder('i')
                    ->where('i.eglise = :eglise')
                    ->andWhere('i.deletedAt IS NULL')
                    ->andWhere('i.seancegroupe IN (:seanceIds)')
                    ->setParameter('eglise', $eglise)
                    ->setParameter('seanceIds', $seanceIds)
                    ->getQuery()
                    ->getResult();
                
                $totalInvites = count($invitegroupes);
                
                // DEBUG: Vérifier les invités trouvés
                dump('Invités trouvés pour ces séances: ' . $totalInvites);
            }
            
            // 7. RECHERCHE DES DETAILS DE COTISATION
            $qbDetailCotisations = $detailcotisationgroupeRepository->createQueryBuilder('dc')
                ->where('dc.eglise = :eglise')
                ->andWhere('dc.groupe = :groupe')
                ->andWhere('dc.deletedAt IS NULL')
                ->setParameter('eglise', $eglise)
                ->setParameter('groupe', $groupe);
            
            if ($dateDebut) {
                $qbDetailCotisations->andWhere('dc.datecotiser >= :dateDebut')
                    ->setParameter('dateDebut', $dateDebut);
            }
            if ($dateFin) {
                $qbDetailCotisations->andWhere('dc.datecotiser <= :dateFin')
                    ->setParameter('dateFin', $dateFin);
            }
            
            $detailcotisations = $qbDetailCotisations->orderBy('dc.datecotiser', 'DESC')->getQuery()->getResult();
            
            // Calcul des totaux des détails de cotisation
            foreach ($detailcotisations as $detail) {
                $totalDetailCotisations += $detail->getMontant() ?? 0;
                $totalMontantPayeDetails += $detail->getMontantpayer() ?? 0;
            }
        }
    }
    
    // Récupérer les membres du groupe
    $groupeUser = $user->getGroupe();
    if ($groupeUser) {
        $membres = $fideleRepository->findFidelesByGroupe($groupeUser->getId());
    } else {
        $membres = [];
    }
    
    // Récupérer les soldes
    $soldes = $groupe->getSoldegroupes();
    $soldeTotal = 0;
    foreach ($soldes as $solde) {
        $soldeTotal += (float) $solde->getMontant();
    }
    
    // Afficher le résumé du débogage (à retirer après)
    // dd([
    //     'seances' => count($seancegroupes),
    //     'cotisations' => count($cotisations),
    //     'cotisationIds' => $cotisationIds ?? [],
    //     'paiements' => count($paiements),
    //     'seanceIds' => $seanceIds ?? [],
    //     'invites' => count($invitegroupes),
    //     'detailcotisations' => count($detailcotisations),
    // ]);
    
    return $this->render('bilan/activite/activite_groupebyuser.html.twig', [
        'form_recherche' => $form->createView(),
        'seancegroupes' => $seancegroupes,
        'cotisations' => $cotisations,
        'depenses' => $depenses,
        'invitegroupes' => $invitegroupes,
        'presences' => $presences,
        'paiements' => $paiements,
        'detailcotisations' => $detailcotisations,
        'groupe' => $groupe,
        'membres' => $membres,
        'totalCotisations' => $totalCotisations,
        'totalDepenses' => $totalDepenses,
        'totalPresences' => $totalPresences,
        'totalInvites' => $totalInvites,
        'totalDetailCotisations' => $totalDetailCotisations,
        'totalMontantPayeDetails' => $totalMontantPayeDetails,
        'soldeTotal' => $soldeTotal,
        'nbMembres' => count($membres),
        'nbSeances' => count($seancegroupes),
        'nbCotisations' => count($cotisations),
        'nbDepenses' => count($depenses),
        'nbPaiements' => count($paiements),
        'nbInvites' => count($invitegroupes),
        'nbDetailCotisations' => count($detailcotisations),
    ]);
}
  
     #[Route('/bilanzone', name: 'app_bilan_zone', methods: ['GET', 'POST'])]
    public function rechercheZone(
        Request $request,
        SeancezoneRepository $seanceRepository,
        ZoneRepository $zoneRepository,
        CotisationzoneRepository $cotisationRepository,
        DepensezoneRepository $depenseRepository,
        CotiserzoneRepository $cotiserRepository,
        PresencezoneRepository $presenceRepository
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        $eglise = $user->getEglise();
        
        if (!$eglise) {
            $this->addFlash('error', 'Aucune église associée à votre compte.');
            return $this->redirectToRoute('app_dashboard');
        }
        
        $zones = $zoneRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        
        $form = $this->createForm(RecherchezoneType::class);
        $form->handleRequest($request);
        
        $bilanComplet = null;
          $soldeDisponible = 0;
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            
            $dateDebut = $data['dateDebut'] ?? null;
            $dateFin = $data['dateFin'] ?? null;
            $zoneFiltre = $data['zone'] ?? null;
            
            if ($dateDebut && $dateFin && $dateDebut > $dateFin) {
                $this->addFlash('warning', 'La date de début doit être antérieure à la date de fin.');
            } else {
                // 1. RECHERCHE DES SÉANCES
                $qbSeances = $seanceRepository->createQueryBuilder('s')
                    ->where('s.eglise = :eglise')
                    ->andWhere('s.deletedAt IS NULL')
                    ->setParameter('eglise', $eglise);
                
                if ($zoneFiltre) {
                    $qbSeances->andWhere('s.zone = :zone')
                        ->setParameter('zone', $zoneFiltre);
                }
                if ($dateDebut) {
                    $qbSeances->andWhere('s.datesuper >= :dateDebut')
                        ->setParameter('dateDebut', $dateDebut);
                }
                if ($dateFin) {
                    $qbSeances->andWhere('s.datesuper <= :dateFin')
                        ->setParameter('dateFin', $dateFin);
                }
                
                $seances = $qbSeances->getQuery()->getResult();
                
                // 2. RECHERCHE DES COTISATIONS
                $qbCotisations = $cotisationRepository->createQueryBuilder('c')
                    ->where('c.eglise = :eglise')
                    ->andWhere('c.deletedAt IS NULL')
                    ->setParameter('eglise', $eglise);
                
                if ($zoneFiltre) {
                    $qbCotisations->andWhere('c.zone = :zone')
                        ->setParameter('zone', $zoneFiltre);
                }
                if ($dateDebut) {
                    $qbCotisations->andWhere('c.createAt >= :dateDebut')
                        ->setParameter('dateDebut', $dateDebut);
                }
                if ($dateFin) {
                    $qbCotisations->andWhere('c.createAt <= :dateFin')
                        ->setParameter('dateFin', $dateFin);
                }
                
                $cotisations = $qbCotisations->getQuery()->getResult();
                
                // 3. RECHERCHE DES DÉPENSES
                $qbDepenses = $depenseRepository->createQueryBuilder('d')
                    ->where('d.eglise = :eglise')
                    ->andWhere('d.deletedAt IS NULL')
                    ->setParameter('eglise', $eglise);
                
                if ($zoneFiltre) {
                    $qbDepenses->andWhere('d.zone = :zone')
                        ->setParameter('zone', $zoneFiltre);
                }
                if ($dateDebut) {
                    $qbDepenses->andWhere('d.datedepense >= :dateDebut')
                        ->setParameter('dateDebut', $dateDebut);
                }
                if ($dateFin) {
                    $qbDepenses->andWhere('d.datedepense <= :dateFin')
                        ->setParameter('dateFin', $dateFin);
                }
                
                $depenses = $qbDepenses->getQuery()->getResult();

                
                // 4. RECHERCHE DES PAIEMENTS
                $cotisationIds = array_map(fn($c) => $c->getId(), $cotisations);
                $paiements = [];
                if (!empty($cotisationIds)) {
                    $paiements = $cotiserRepository->createQueryBuilder('p')
                        ->where('p.cotisationzone IN (:cotisationIds)')
                        ->andWhere('p.deletedAt IS NULL')
                        ->setParameter('cotisationIds', $cotisationIds)
                        ->getQuery()
                        ->getResult();
                }
                
                // 5. RECHERCHE DES PRÉSENCES
                $seanceIds = array_map(fn($s) => $s->getId(), $seances);
                $presences = [];
                if (!empty($seanceIds)) {
                    $presences = $presenceRepository->createQueryBuilder('p')
                        ->where('p.eglise = :eglise')
                        ->andWhere('p.deletedAt IS NULL')
                        ->andWhere('p.seancezone IN (:seanceIds)')
                        ->setParameter('eglise', $eglise)
                        ->setParameter('seanceIds', $seanceIds)
                        ->getQuery()
                        ->getResult();
                }

                //6.SOLDE DE LA CELLULE OU DES CELLULES
             //  $zoneSelectionnee = $formRecherche->get('zone')->getData();

           $soldeDisponible = 0;

            if ($zoneFiltre) {

                // Solde de la zone sélectionnée
                foreach ($zoneFiltre->getSoldezones() as $solezone) {

                    $soldeDisponible += (float) $solezone->getMontant();
                }

            } else {

                // Somme des soldes des zones de la même église
                $zones = $zoneRepository->findBy([
                    'eglise' => $eglise,
                    'deletedAt' => null
                ]);

                foreach ($zones as $zone) {

                    foreach ($zone->getSoldezones() as $solezone) {

                        $soldeDisponible += (float) $solezone->getMontant();
                    }
                }

                
            }
                
                $bilanComplet = new BilanZoneDTO(
                    $seances,
                    $cotisations,
                    $depenses,
                    $paiements,
                    $presences,
                    
                    $zoneFiltre ? [$zoneFiltre] : null
                );
                
                
                if ($bilanComplet->isEmpty()) {
                    $this->addFlash('info', 'Aucune donnée trouvée pour les critères sélectionnés.');
                }
            }
        }
        
        return $this->render('bilan/activite/activite_zone.html.twig', [
            'form_recherche' => $form->createView(),
            'bilanComplet' => $bilanComplet,
            'zones' => $zones,
            'soldeDisponible' => $soldeDisponible,
        ]);
    }

     #[Route('/bilanzonebyuser', name: 'app_bilan_zone2', methods: ['GET', 'POST'])]
        public function rechercheZoneByUser(
            Request $request,
            SeancezoneRepository $seancezoneRepository,
            CotisationzoneRepository $cotisationzoneRepository,
            PresencezoneRepository $presencezoneRepository,
            DepensezoneRepository $depensezoneRepository,
            CotiserzoneRepository $cotiserzoneRepository,
            InvitezoneRepository $invitezoneRepository,
            DetailcotisationzoneRepository $detailcotisationzoneRepository,
            ZoneRepository $zoneRepository,
            FideleRepository $fideleRepository
        ): Response {
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
            
            $user = $this->getUser();
            $eglise = $user->getEglise();
            
            // Récupérer la zone de l'utilisateur connecté
            $zone = $zoneRepository->findOneByUser($user);
            
            if (!$zone) {
                $this->addFlash('warning', 'Aucune zone associée à votre compte.');
                return $this->redirectToRoute('seancezone_index');
            }
            
            // Initialisation des variables
            $seancezones = [];
            $cotisations = [];
            $depenses = [];
            $presences = [];
            $paiements = [];
            $invitezones = [];
            $detailcotisations = [];
            $totalCotisations = 0;
            $totalDepenses = 0;
            $totalPresences = 0;
            $totalInvites = 0;
            $totalDetailCotisations = 0;
            $totalMontantPayeDetails = 0;
            
            // Création du formulaire
            $form = $this->createForm(BilancelluleuserType::class);
            $form->handleRequest($request);
            
            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();
                $dateDebut = $data['dateDebut'] ?? null;
                $dateFin = $data['dateFin'] ?? null;
                
                // Validation des dates
                if ($dateDebut && $dateFin && $dateDebut > $dateFin) {
                    $this->addFlash('warning', 'La date de début doit être antérieure à la date de fin.');
                } else {
                    // 1. RECHERCHE DES SÉANCES DE LA CELLULE
                    $qbSeances = $seancezoneRepository->createQueryBuilder('s')
                        ->where('s.eglise = :eglise')
                        ->andWhere('s.zone = :zone')
                        ->andWhere('s.deletedAt IS NULL')
                        ->setParameter('eglise', $eglise)
                        ->setParameter('zone', $zone);
                    
                    if ($dateDebut) {
                        $qbSeances->andWhere('s.datesuper >= :dateDebut')
                            ->setParameter('dateDebut', $dateDebut);
                    }
                    if ($dateFin) {
                        $qbSeances->andWhere('s.datesuper <= :dateFin')
                            ->setParameter('dateFin', $dateFin);
                    }
                    
                    $seancezones = $qbSeances->orderBy('s.datesuper', 'DESC')->getQuery()->getResult();
                    
                    // 2. RECHERCHE DES COTISATIONS
                    $qbCotisations = $cotisationzoneRepository->createQueryBuilder('c')
                        ->where('c.eglise = :eglise')
                        ->andWhere('c.zone = :zone')
                        ->andWhere('c.deletedAt IS NULL')
                        ->setParameter('eglise', $eglise)
                        ->setParameter('zone', $zone);
                    
                    if ($dateDebut) {
                        $qbCotisations->andWhere('c.createAt >= :dateDebut')
                            ->setParameter('dateDebut', $dateDebut);
                    }
                    if ($dateFin) {
                        $qbCotisations->andWhere('c.createAt <= :dateFin')
                            ->setParameter('dateFin', $dateFin);
                    }
                    
                    $cotisations = $qbCotisations->getQuery()->getResult();
                    
                    // Calcul du total des cotisations
                    foreach ($cotisations as $cotisation) {
                        $totalCotisations += $cotisation->getMontant() ?? 0;
                    }
                    
                    // 3. RECHERCHE DES DÉPENSES
                    $qbDepenses = $depensezoneRepository->createQueryBuilder('d')
                        ->where('d.eglise = :eglise')
                        ->andWhere('d.zone = :zone')
                        ->andWhere('d.deletedAt IS NULL')
                        ->setParameter('eglise', $eglise)
                        ->setParameter('zone', $zone);
                    
                    if ($dateDebut) {
                        $qbDepenses->andWhere('d.datedepense >= :dateDebut')
                            ->setParameter('dateDebut', $dateDebut);
                    }
                    if ($dateFin) {
                        $qbDepenses->andWhere('d.datedepense <= :dateFin')
                            ->setParameter('dateFin', $dateFin);
                    }
                    
                    $depenses = $qbDepenses->getQuery()->getResult();
                    
                    // Calcul du total des dépenses
                    foreach ($depenses as $depense) {
                        $totalDepenses += $depense->getMontant() ?? 0;
                    }
                    
                    // 4. RECHERCHE DES PRÉSENCES
                    $seanceIds = array_map(fn($s) => $s->getId(), $seancezones);
                    if (!empty($seanceIds)) {
                        $presences = $presencezoneRepository->createQueryBuilder('p')
                            ->where('p.eglise = :eglise')
                            ->andWhere('p.zone = :zone')
                            ->andWhere('p.deletedAt IS NULL')
                            ->andWhere('p.seancezone IN (:seanceIds)')
                            ->setParameter('eglise', $eglise)
                            ->setParameter('zone', $zone)
                            ->setParameter('seanceIds', $seanceIds)
                            ->getQuery()
                            ->getResult();
                        
                        $totalPresences = count($presences);
                    }
                    
                    // 5. RECHERCHE DES PAIEMENTS (Cotiserzone)
                    $cotisationIds = array_map(fn($c) => $c->getId(), $cotisations);
                    if (!empty($cotisationIds)) {
                        $paiements = $cotiserzoneRepository->createQueryBuilder('p')
                            ->where('p.cotisationzone IN (:cotisationIds)')
                            ->andWhere('p.deletedAt IS NULL')
                            ->setParameter('cotisationIds', $cotisationIds)
                            ->getQuery()
                            ->getResult();
                    }

                    // 6. RECHERCHE DES INVITES (à partir des séances)
                    if (!empty($seanceIds)) {
                        $invitezones = $invitezoneRepository->createQueryBuilder('i')
                            ->where('i.eglise = :eglise')
                            ->andWhere('i.deletedAt IS NULL')
                            ->andWhere('i.seancezone IN (:seanceIds)')
                            ->setParameter('eglise', $eglise)
                            ->setParameter('seanceIds', $seanceIds)
                            ->getQuery()
                            ->getResult();
                        
                        $totalInvites = count($invitezones);
                    }
                    
                    // 7. RECHERCHE DES DETAILS DE COTISATION (Detailcotisationzone)
                    // Detailcotisationzone a une relation directe avec Zone
                    $qbDetailCotisations = $detailcotisationzoneRepository->createQueryBuilder('dc')
                        ->where('dc.eglise = :eglise')
                        ->andWhere('dc.zone = :zone')
                        ->andWhere('dc.deletedAt IS NULL')
                        ->setParameter('eglise', $eglise)
                        ->setParameter('zone', $zone);
                    
                    if ($dateDebut) {
                        $qbDetailCotisations->andWhere('dc.datedetail >= :dateDebut')
                            ->setParameter('dateDebut', $dateDebut);
                    }
                    if ($dateFin) {
                        $qbDetailCotisations->andWhere('dc.datedetail <= :dateFin')
                            ->setParameter('dateFin', $dateFin);
                    }
                    
                    $detailcotisations = $qbDetailCotisations->orderBy('dc.datedetail', 'DESC')->getQuery()->getResult();
                    
                    // Calcul des totaux des détails de cotisation
                    foreach ($detailcotisations as $detail) {
                        $totalDetailCotisations += $detail->getMontant() ?? 0;
                        $totalMontantPayeDetails += $detail->getMontantpayer() ?? 0;
                    }
                }
            }
            
            // Récupérer les membres de la zone
            $membres = $fideleRepository->findBy(['zone' => $zone, 'deletedAt' => NULL]);
            
            // Récupérer les soldes
            $soldes = $zone->getSoldezones();
            $soldeTotal = 0;
            foreach ($soldes as $solde) {
                $soldeTotal += (float) $solde->getMontant();
            }
            
            return $this->render('bilan/activite/activite_zonebyuser.html.twig', [
                'form_recherche' => $form->createView(),
                'seancezones' => $seancezones,
                'cotisations' => $cotisations,
                'depenses' => $depenses,
                'invitezones' => $invitezones,
                'presences' => $presences,
                'paiements' => $paiements,
                'detailcotisations' => $detailcotisations,
                'zone' => $zone,
                'membres' => $membres,
                'totalCotisations' => $totalCotisations,
                'totalDepenses' => $totalDepenses,
                'totalPresences' => $totalPresences,
                'totalInvites' => $totalInvites,
                'totalDetailCotisations' => $totalDetailCotisations,
                'totalMontantPayeDetails' => $totalMontantPayeDetails,
                'soldeTotal' => $soldeTotal,
                'nbMembres' => count($membres),
                'nbSeances' => count($seancezones),
                'nbCotisations' => count($cotisations),
                'nbDepenses' => count($depenses),
                'nbPaiements' => count($paiements),
                'nbInvites' => count($invitezones),
                'nbDetailCotisations' => count($detailcotisations),
            ]);
        }

    
     #[Route('/bilanfamille', name: 'app_bilan_famille', methods: ['GET', 'POST'])]
    public function rechercheFamille(
        Request $request,
        SeancefamilleRepository $seanceRepository,
        FamilleRepository $familleRepository,
        CotisationfamilleRepository $cotisationRepository,
        DepensefamilleRepository $depenseRepository,
        CotiserfamilleRepository $cotiserRepository,
        PresencefamilleRepository $presenceRepository
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        $eglise = $user->getEglise();
        
        if (!$eglise) {
            $this->addFlash('error', 'Aucune église associée à votre compte.');
            return $this->redirectToRoute('app_dashboard');
        }
        
        $familles = $familleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        
        $form = $this->createForm(RecherchefamilleType::class);
        $form->handleRequest($request);
        
        $bilanComplet = null;
          $soldeDisponible = 0;
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            
            $dateDebut = $data['dateDebut'] ?? null;
            $dateFin = $data['dateFin'] ?? null;
            $familleFiltre = $data['famille'] ?? null;
            
            if ($dateDebut && $dateFin && $dateDebut > $dateFin) {
                $this->addFlash('warning', 'La date de début doit être antérieure à la date de fin.');
            } else {
                // 1. RECHERCHE DES SÉANCES
                $qbSeances = $seanceRepository->createQueryBuilder('s')
                    ->where('s.eglise = :eglise')
                    ->andWhere('s.deletedAt IS NULL')
                    ->setParameter('eglise', $eglise);
                
                if ($familleFiltre) {
                    $qbSeances->andWhere('s.famille = :famille')
                        ->setParameter('famille', $familleFiltre);
                }
                if ($dateDebut) {
                    $qbSeances->andWhere('s.datesuper >= :dateDebut')
                        ->setParameter('dateDebut', $dateDebut);
                }
                if ($dateFin) {
                    $qbSeances->andWhere('s.datesuper <= :dateFin')
                        ->setParameter('dateFin', $dateFin);
                }
                
                $seances = $qbSeances->getQuery()->getResult();
                
                // 2. RECHERCHE DES COTISATIONS
                $qbCotisations = $cotisationRepository->createQueryBuilder('c')
                    ->where('c.eglise = :eglise')
                    ->andWhere('c.deletedAt IS NULL')
                    ->setParameter('eglise', $eglise);
                
                if ($familleFiltre) {
                    $qbCotisations->andWhere('c.famille = :famille')
                        ->setParameter('famille', $familleFiltre);
                }
                if ($dateDebut) {
                    $qbCotisations->andWhere('c.createAt >= :dateDebut')
                        ->setParameter('dateDebut', $dateDebut);
                }
                if ($dateFin) {
                    $qbCotisations->andWhere('c.createAt <= :dateFin')
                        ->setParameter('dateFin', $dateFin);
                }
                
                $cotisations = $qbCotisations->getQuery()->getResult();
                
                // 3. RECHERCHE DES DÉPENSES
                $qbDepenses = $depenseRepository->createQueryBuilder('d')
                    ->where('d.eglise = :eglise')
                    ->andWhere('d.deletedAt IS NULL')
                    ->setParameter('eglise', $eglise);
                
                if ($familleFiltre) {
                    $qbDepenses->andWhere('d.famille = :famille')
                        ->setParameter('famille', $familleFiltre);
                }
                if ($dateDebut) {
                    $qbDepenses->andWhere('d.datedepense >= :dateDebut')
                        ->setParameter('dateDebut', $dateDebut);
                }
                if ($dateFin) {
                    $qbDepenses->andWhere('d.datedepense <= :dateFin')
                        ->setParameter('dateFin', $dateFin);
                }
                
                $depenses = $qbDepenses->getQuery()->getResult();

                
                // 4. RECHERCHE DES PAIEMENTS
                $cotisationIds = array_map(fn($c) => $c->getId(), $cotisations);
                $paiements = [];
                if (!empty($cotisationIds)) {
                    $paiements = $cotiserRepository->createQueryBuilder('p')
                        ->where('p.cotisationfamille IN (:cotisationIds)')
                        ->andWhere('p.deletedAt IS NULL')
                        ->setParameter('cotisationIds', $cotisationIds)
                        ->getQuery()
                        ->getResult();
                }
                
                // 5. RECHERCHE DES PRÉSENCES
                $seanceIds = array_map(fn($s) => $s->getId(), $seances);
                $presences = [];
                if (!empty($seanceIds)) {
                    $presences = $presenceRepository->createQueryBuilder('p')
                        ->where('p.eglise = :eglise')
                        ->andWhere('p.deletedAt IS NULL')
                        ->andWhere('p.senacefamille IN (:seanceIds)')
                        ->setParameter('eglise', $eglise)
                        ->setParameter('seanceIds', $seanceIds)
                        ->getQuery()
                        ->getResult();
                }

                //6.SOLDE DE LA CELLULE OU DES CELLULES
             //  $familleSelectionnee = $formRecherche->get('famille')->getData();

           $soldeDisponible = 0;

            if ($familleFiltre) {

                // Solde de la famille sélectionnée
                foreach ($familleFiltre->getSoldefamilles() as $solefamille) {

                    $soldeDisponible += (float) $solefamille->getMontant();
                }

            } else {

                // Somme des soldes des familles de la même église
                $familles = $familleRepository->findBy([
                    'eglise' => $eglise,
                    'deletedAt' => null
                ]);

                foreach ($familles as $famille) {

                    foreach ($famille->getSoldefamilles() as $solefamille) {

                        $soldeDisponible += (float) $solefamille->getMontant();
                    }
                }

                
            }
                
                $bilanComplet = new BilanFamilleDTO(
                    $seances,
                    $cotisations,
                    $depenses,
                    $paiements,
                    $presences,
                    
                    $familleFiltre ? [$familleFiltre] : null
                );
                
                
                if ($bilanComplet->isEmpty()) {
                    $this->addFlash('info', 'Aucune donnée trouvée pour les critères sélectionnés.');
                }
            }
        }
        
        return $this->render('bilan/activite/activite_famille.html.twig', [
            'form_recherche' => $form->createView(),
            'bilanComplet' => $bilanComplet,
            'familles' => $familles,
            'soldeDisponible' => $soldeDisponible,
        ]);
    }

     //Bilan famille par le User
       #[Route('/bilanfamillebyuser', name: 'app_bilan_famille2', methods: ['GET', 'POST'])]
        public function rechercheFamilleByUser(
            Request $request,
            SeancefamilleRepository $seancefamilleRepository,
            CotisationfamilleRepository $cotisationfamilleRepository,
            PresencefamilleRepository $presencefamilleRepository,
            DepensefamilleRepository $depensefamilleRepository,
            CotiserfamilleRepository $cotiserfamilleRepository,
            InvitefamilleRepository $invitefamilleRepository,
            DetailcotisationfamilleRepository $detailcotisationfamilleRepository,
            FamilleRepository $familleRepository,
            FideleRepository $fideleRepository
        ): Response {
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
            
            $user = $this->getUser();
            $eglise = $user->getEglise();
            
            // Récupérer la famille de l'utilisateur connecté
            $famille = $familleRepository->findOneByUser($user);
            
            if (!$famille) {
                $this->addFlash('warning', 'Aucune famille associée à votre compte.');
                return $this->redirectToRoute('seancefamille_index');
            }
            
            // Initialisation des variables
            $seancefamilles = [];
            $cotisations = [];
            $depenses = [];
            $presences = [];
            $paiements = [];
            $invitefamilles = [];
            $detailcotisations = [];
            $totalCotisations = 0;
            $totalDepenses = 0;
            $totalPresences = 0;
            $totalInvites = 0;
            $totalDetailCotisations = 0;
            $totalMontantPayeDetails = 0;
            
            // Création du formulaire
            $form = $this->createForm(BilancelluleuserType::class);
            $form->handleRequest($request);
            
            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();
                $dateDebut = $data['dateDebut'] ?? null;
                $dateFin = $data['dateFin'] ?? null;
                
                // Validation des dates
                if ($dateDebut && $dateFin && $dateDebut > $dateFin) {
                    $this->addFlash('warning', 'La date de début doit être antérieure à la date de fin.');
                } else {
                    // 1. RECHERCHE DES SÉANCES DE LA CELLULE
                    $qbSeances = $seancefamilleRepository->createQueryBuilder('s')
                        ->where('s.eglise = :eglise')
                        ->andWhere('s.famille = :famille')
                        ->andWhere('s.deletedAt IS NULL')
                        ->setParameter('eglise', $eglise)
                        ->setParameter('famille', $famille);
                    
                    if ($dateDebut) {
                        $qbSeances->andWhere('s.datesuper >= :dateDebut')
                            ->setParameter('dateDebut', $dateDebut);
                    }
                    if ($dateFin) {
                        $qbSeances->andWhere('s.datesuper <= :dateFin')
                            ->setParameter('dateFin', $dateFin);
                    }
                    
                    $seancefamilles = $qbSeances->orderBy('s.datesuper', 'DESC')->getQuery()->getResult();
                    
                    // 2. RECHERCHE DES COTISATIONS
                    $qbCotisations = $cotisationfamilleRepository->createQueryBuilder('c')
                        ->where('c.eglise = :eglise')
                        ->andWhere('c.famille = :famille')
                        ->andWhere('c.deletedAt IS NULL')
                        ->setParameter('eglise', $eglise)
                        ->setParameter('famille', $famille);
                    
                    if ($dateDebut) {
                        $qbCotisations->andWhere('c.createAt >= :dateDebut')
                            ->setParameter('dateDebut', $dateDebut);
                    }
                    if ($dateFin) {
                        $qbCotisations->andWhere('c.createAt <= :dateFin')
                            ->setParameter('dateFin', $dateFin);
                    }
                    
                    $cotisations = $qbCotisations->getQuery()->getResult();
                    
                    // Calcul du total des cotisations
                    foreach ($cotisations as $cotisation) {
                        $totalCotisations += $cotisation->getMontant() ?? 0;
                    }
                    
                    // 3. RECHERCHE DES DÉPENSES
                    $qbDepenses = $depensefamilleRepository->createQueryBuilder('d')
                        ->where('d.eglise = :eglise')
                        ->andWhere('d.famille = :famille')
                        ->andWhere('d.deletedAt IS NULL')
                        ->setParameter('eglise', $eglise)
                        ->setParameter('famille', $famille);
                    
                    if ($dateDebut) {
                        $qbDepenses->andWhere('d.datedepense >= :dateDebut')
                            ->setParameter('dateDebut', $dateDebut);
                    }
                    if ($dateFin) {
                        $qbDepenses->andWhere('d.datedepense <= :dateFin')
                            ->setParameter('dateFin', $dateFin);
                    }
                    
                    $depenses = $qbDepenses->getQuery()->getResult();
                    
                    // Calcul du total des dépenses
                    foreach ($depenses as $depense) {
                        $totalDepenses += $depense->getMontant() ?? 0;
                    }
                    
                    // 4. RECHERCHE DES PRÉSENCES
                    $seanceIds = array_map(fn($s) => $s->getId(), $seancefamilles);
                    if (!empty($seanceIds)) {
                        $presences = $presencefamilleRepository->createQueryBuilder('p')
                            ->where('p.eglise = :eglise')
                            ->andWhere('p.famille = :famille')
                            ->andWhere('p.deletedAt IS NULL')
                            ->andWhere('p.seancefamille IN (:seanceIds)')
                            ->setParameter('eglise', $eglise)
                            ->setParameter('famille', $famille)
                            ->setParameter('seanceIds', $seanceIds)
                            ->getQuery()
                            ->getResult();
                        
                        $totalPresences = count($presences);
                    }
                    
                    // 5. RECHERCHE DES PAIEMENTS (Cotiserfamille)
                    $cotisationIds = array_map(fn($c) => $c->getId(), $cotisations);
                    if (!empty($cotisationIds)) {
                        $paiements = $cotiserfamilleRepository->createQueryBuilder('p')
                            ->where('p.cotisationfamille IN (:cotisationIds)')
                            ->andWhere('p.deletedAt IS NULL')
                            ->setParameter('cotisationIds', $cotisationIds)
                            ->getQuery()
                            ->getResult();
                    }

                    // 6. RECHERCHE DES INVITES (à partir des séances)
                    if (!empty($seanceIds)) {
                        $invitefamilles = $invitefamilleRepository->createQueryBuilder('i')
                            ->where('i.eglise = :eglise')
                            ->andWhere('i.deletedAt IS NULL')
                            ->andWhere('i.seancefamille IN (:seanceIds)')
                            ->setParameter('eglise', $eglise)
                            ->setParameter('seanceIds', $seanceIds)
                            ->getQuery()
                            ->getResult();
                        
                        $totalInvites = count($invitefamilles);
                    }
                    
                    // 7. RECHERCHE DES DETAILS DE COTISATION (Detailcotisationfamille)
                    // Detailcotisationfamille a une relation directe avec Famille
                    $qbDetailCotisations = $detailcotisationfamilleRepository->createQueryBuilder('dc')
                        ->where('dc.eglise = :eglise')
                        ->andWhere('dc.famille = :famille')
                        ->andWhere('dc.deletedAt IS NULL')
                        ->setParameter('eglise', $eglise)
                        ->setParameter('famille', $famille);
                    
                    if ($dateDebut) {
                        $qbDetailCotisations->andWhere('dc.datedetail >= :dateDebut')
                            ->setParameter('dateDebut', $dateDebut);
                    }
                    if ($dateFin) {
                        $qbDetailCotisations->andWhere('dc.datedetail <= :dateFin')
                            ->setParameter('dateFin', $dateFin);
                    }
                    
                    $detailcotisations = $qbDetailCotisations->orderBy('dc.datedetail', 'DESC')->getQuery()->getResult();
                    
                    // Calcul des totaux des détails de cotisation
                    foreach ($detailcotisations as $detail) {
                        $totalDetailCotisations += $detail->getMontant() ?? 0;
                        $totalMontantPayeDetails += $detail->getMontantpayer() ?? 0;
                    }
                }
            }
            
            // Récupérer les membres de la famille
            $membres = $fideleRepository->findBy(['famille' => $famille, 'deletedAt' => NULL]);
            
            // Récupérer les soldes
            $soldes = $famille->getSoldefamilles();
            $soldeTotal = 0;
            foreach ($soldes as $solde) {
                $soldeTotal += (float) $solde->getMontant();
            }
            
            return $this->render('bilan/activite/activite_famillebyuser.html.twig', [
                'form_recherche' => $form->createView(),
                'seancefamilles' => $seancefamilles,
                'cotisations' => $cotisations,
                'depenses' => $depenses,
                'invitefamilles' => $invitefamilles,
                'presences' => $presences,
                'paiements' => $paiements,
                'detailcotisations' => $detailcotisations,
                'famille' => $famille,
                'membres' => $membres,
                'totalCotisations' => $totalCotisations,
                'totalDepenses' => $totalDepenses,
                'totalPresences' => $totalPresences,
                'totalInvites' => $totalInvites,
                'totalDetailCotisations' => $totalDetailCotisations,
                'totalMontantPayeDetails' => $totalMontantPayeDetails,
                'soldeTotal' => $soldeTotal,
                'nbMembres' => count($membres),
                'nbSeances' => count($seancefamilles),
                'nbCotisations' => count($cotisations),
                'nbDepenses' => count($depenses),
                'nbPaiements' => count($paiements),
                'nbInvites' => count($invitefamilles),
                'nbDetailCotisations' => count($detailcotisations),
            ]);
        }

    #[Route('/bilandepensecodim', name: 'app_bilan_depensecodim', methods: ['GET', 'POST'])]
    public function rechercheDepensecodim(Request $request, DepensecodimRepository $depensecodimRepository,
            DateTime $dateDebut = null, DateTime $dateFin = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $eglise = $this->getUser()->getEglise();

        $depensecodims = [];

//        $difference = $cotisationRepository->getSeanceByDates();
        $searchcotisation = $this->createForm(BilandepensecodimType::class, $depensecodims);
        if ($searchcotisation->handleRequest($request)->isSubmitted()) {
            $criteria = $searchcotisation->getData();

            $criteres = array();

            $criteres["eglise"] = $eglise->getId();

            if ($criteria["dateDebut"]) {
                $dateDebut = ($criteria["dateDebut"]);
                //  $dateDebut = DateTime::createFromFormat('Y-m-d',$dateDebuts);
            }

            if ($criteria["dateFin"]) {
                $dateFin = $criteria["dateFin"];

                // $dateFin = DateTime::createFromFormat('Y-m-d',$dateFins);
            }




            $limit = 1000;
            $depensecodims = $depensecodimRepository->rechercheDepensecodim($criteres, $dateDebut, $dateFin, $limit);
        }

        return $this->render('bilan/annuel/bilandepensecodim.html.twig', [
                    'form_recherche' => $searchcotisation->createView(),
                    'depensecodims' => $depensecodims,
        ]);
    }

    #[Route('/bilandepensecodimn', name: 'app_bilan_depensecodimn', methods: ['GET', 'POST'])]
    public function rechercheDepensecodimn(Request $request, DepensecodimRepository $depensecodimRepository,
            DateTime $dateDebut = null, DateTime $dateFin = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $communaute = $this->getUser()->getEglise()->getCommunaute();
        $user = $this->getUser();

        $depensecodims = [];

//        $difference = $cotisationRepository->getSeanceByDates();
        $searchcotisation = $this->createForm(BilandepensecodimType::class, $depensecodims);
        if ($searchcotisation->handleRequest($request)->isSubmitted()) {
            $criteria = $searchcotisation->getData();

            $criteres = array();

            $criteres["communaute"] = $communaute->getId();

            if ($criteria["dateDebut"]) {
                $dateDebut = ($criteria["dateDebut"]);
                //  $dateDebut = DateTime::createFromFormat('Y-m-d',$dateDebuts);
            }

            if ($criteria["dateFin"]) {
                $dateFin = $criteria["dateFin"];

                // $dateFin = DateTime::createFromFormat('Y-m-d',$dateFins);
            }




            $limit = 1000;
            $depensecodims = $depensecodimRepository->rechercheDepensecodimn($criteres, $dateDebut, $dateFin, $limit);
        }

        return $this->render('bilan/national/bilandepensecodim.html.twig', [
                    'form_recherche' => $searchcotisation->createView(),
                    'depensecodims' => $depensecodims,
        ]);
    }

    #[Route('/bilancotisationexception', name: 'app_bilan_cotisationexception', methods: ['GET', 'POST'])]
    public function rechercheCotisationexception(Request $request, CotisationexceptionnelleRepository $cotisationexceptionRepository,
            DateTime $dateDebut = null, DateTime $dateFin = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $eglise = $this->getUser()->getEglise();

        $cotisationexceptions = [];

//        $difference = $cotisationRepository->getSeanceByDates();
        $searchcotisation = $this->createForm(BilancotisationexceptionType::class, $cotisationexceptions);
        if ($searchcotisation->handleRequest($request)->isSubmitted()) {
            $criteria = $searchcotisation->getData();

            $criteres = array();

            $criteres["eglise"] = $eglise->getId();

            if ($criteria["dateDebut"]) {
                $dateDebut = ($criteria["dateDebut"]);
                //  $dateDebut = DateTime::createFromFormat('Y-m-d',$dateDebuts);
            }

            if ($criteria["dateFin"]) {
                $dateFin = $criteria["dateFin"];

                // $dateFin = DateTime::createFromFormat('Y-m-d',$dateFins);
            }


            $limit = 1000;
            $cotisationexceptions = $cotisationexceptionRepository->rechercheCotisationException($criteres, $dateDebut, $dateFin, $limit);
        }

        return $this->render('bilan/finance/cotisationexception.html.twig', [
                    'form_recherche' => $searchcotisation->createView(),
                    'cotisationexceptions' => $cotisationexceptions,
        ]);
    }

    #[Route('/bilancotisationexceptionn', name: 'app_bilan_cotisationexceptionn', methods: ['GET', 'POST'])]
    public function rechercheCotisationexceptionn(Request $request, CotisationexceptionnelleRepository $cotisationexceptionRepository,
            DateTime $dateDebut = null, DateTime $dateFin = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $communaute = $this->getUser()->getEglise()->getCommunaute();
        $user = $this->getUser();

        $cotisationexceptions = [];

//        $difference = $cotisationRepository->getSeanceByDates();
        $searchcotisation = $this->createForm(BilancotisationexceptionnType::class, $cotisationexceptions);
        if ($searchcotisation->handleRequest($request)->isSubmitted()) {
            $criteria = $searchcotisation->getData();

            $criteres = array();

            $criteres["communaute"] = $communaute->getId();

            if ($criteria["dateDebut"]) {
                $dateDebut = ($criteria["dateDebut"]);
                //  $dateDebut = DateTime::createFromFormat('Y-m-d',$dateDebuts);
            }

            if ($criteria["dateFin"]) {
                $dateFin = $criteria["dateFin"];

                // $dateFin = DateTime::createFromFormat('Y-m-d',$dateFins);
            }


            $limit = 1000;
            $cotisationexceptions = $cotisationexceptionRepository->rechercheCotisationExceptionn($criteres, $dateDebut, $dateFin, $limit);
        }

        return $this->render('bilan/national/cotisationexception.html.twig', [
                    'form_recherche' => $searchcotisation->createView(),
                    'cotisationexceptions' => $cotisationexceptions,
        ]);
    }

    #[Route('/bilancotisation', name: 'app_bilan_cotisation', methods: ['GET', 'POST'])]
    public function rechercheCotisation(Request $request, CotisationRepository $cotisationRepository,
            DateTime $dateDebut = null, DateTime $dateFin = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $eglise = $this->getUser()->getEglise();

        $cotisations = [];

//        $difference = $cotisationRepository->getSeanceByDates();
        $searchcotisation = $this->createForm(BilancotisationType::class, $cotisations);
        if ($searchcotisation->handleRequest($request)->isSubmitted()) {
            $criteria = $searchcotisation->getData();

            $criteres = array();

            $criteres["eglise"] = $eglise->getId();

            if ($criteria["dateDebut"]) {
                $dateDebut = ($criteria["dateDebut"]);
                //  $dateDebut = DateTime::createFromFormat('Y-m-d',$dateDebuts);
            }

            if ($criteria["dateFin"]) {
                $dateFin = $criteria["dateFin"];

                // $dateFin = DateTime::createFromFormat('Y-m-d',$dateFins);
            }


            $limit = 1000;
            $cotisations = $cotisationRepository->rechercheCotisation($criteres, $dateDebut, $dateFin, $limit);
        }

        return $this->render('bilan/finance/cotisation1.html.twig', [
                    'form_recherche' => $searchcotisation->createView(),
                    'cotisations' => $cotisations,
        ]);
    }

    #[Route('/bilancotisationn', name: 'app_bilan_cotisationn', methods: ['GET', 'POST'])]
    public function rechercheCotisationn(Request $request, CotisationRepository $cotisationRepository,
            DateTime $dateDebut = null, DateTime $dateFin = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $communaute = $this->getUser()->getEglise()->getCommunaute();
        $user = $this->getUser();

        $cotisations = [];

//        $difference = $cotisationRepository->getSeanceByDates();
        $searchcotisation = $this->createForm(BilancotisationnType::class, $cotisations);
        if ($searchcotisation->handleRequest($request)->isSubmitted()) {
            $criteria = $searchcotisation->getData();

            $criteres = array();

            $criteres["communaute"] = $communaute->getId();

            if ($criteria["dateDebut"]) {
                $dateDebut = ($criteria["dateDebut"]);
                //  $dateDebut = DateTime::createFromFormat('Y-m-d',$dateDebuts);
            }

            if ($criteria["dateFin"]) {
                $dateFin = $criteria["dateFin"];

                // $dateFin = DateTime::createFromFormat('Y-m-d',$dateFins);
            }


            $limit = 1000;
            $cotisations = $cotisationRepository->rechercheCotisationn($criteres, $dateDebut, $dateFin, $limit);
        }

        return $this->render('bilan/national/cotisation1.html.twig', [
                    'form_recherche' => $searchcotisation->createView(),
                    'cotisations' => $cotisations,
        ]);
    }

    #[Route('/bilanoffrande', name: 'app_bilan_offrande', methods: ['GET', 'POST'])]
    public function rechercheOffrande(Request $request, OffrandeRepository $offrandeRepository,
            DateTime $dateDebut = null, DateTime $dateFin = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $eglise = $this->getUser()->getEglise();

        $offrandes = [];

//        $difference = $cotisationRepository->getSeanceByDates();
        $searchcotisation = $this->createForm(BilanoffrandeType::class, $offrandes);
        if ($searchcotisation->handleRequest($request)->isSubmitted()) {
            $criteria = $searchcotisation->getData();

            $criteres = array();

            $criteres["eglise"] = $eglise->getId();

            if ($criteria["dateDebut"]) {
                $dateDebut = ($criteria["dateDebut"]);
                //  $dateDebut = DateTime::createFromFormat('Y-m-d',$dateDebuts);
            }

            if ($criteria["dateFin"]) {
                $dateFin = $criteria["dateFin"];

                // $dateFin = DateTime::createFromFormat('Y-m-d',$dateFins);
            }


            $limit = 1000;
            $offrandes = $offrandeRepository->rechercheOffrande($criteres, $dateDebut, $dateFin, $limit);
        }

        return $this->render('bilan/finance/offrande.html.twig', [
                    'form_recherche' => $searchcotisation->createView(),
                    'offrandes' => $offrandes,
        ]);
    }

    #[Route('/bilanoffranden', name: 'app_bilan_offranden', methods: ['GET', 'POST'])]
    public function rechercheOffranden(Request $request, OffrandeRepository $offrandeRepository,
            DateTime $dateDebut = null, DateTime $dateFin = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $communaute = $this->getUser()->getEglise()->getCommunaute();
        $user = $this->getUser();

        $offrandes = [];

//        $difference = $cotisationRepository->getSeanceByDates();
        $searchcotisation = $this->createForm(BilanoffrandenType::class, $offrandes);
        if ($searchcotisation->handleRequest($request)->isSubmitted()) {
            $criteria = $searchcotisation->getData();

            $criteres = array();

            $criteres["communaute"] = $communaute->getId();

            if ($criteria["dateDebut"]) {
                $dateDebut = ($criteria["dateDebut"]);
                //  $dateDebut = DateTime::createFromFormat('Y-m-d',$dateDebuts);
            }

            if ($criteria["dateFin"]) {
                $dateFin = $criteria["dateFin"];

                // $dateFin = DateTime::createFromFormat('Y-m-d',$dateFins);
            }


            $limit = 1000;
            $offrandes = $offrandeRepository->rechercheOffranden($criteres, $dateDebut, $dateFin, $limit);
        }

        return $this->render('bilan/national/offrande.html.twig', [
                    'form_recherche' => $searchcotisation->createView(),
                    'offrandes' => $offrandes,
        ]);
    }

    #[Route('/bilandon', name: 'app_bilan_don', methods: ['GET', 'POST'])]
    public function rechercheDon(Request $request, DonRepository $donRepository,
            DateTime $dateDebut = null, DateTime $dateFin = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $eglise = $this->getUser()->getEglise();

        $dons = [];

//        $difference = $cotisationRepository->getSeanceByDates();
        $searchcotisation = $this->createForm(BilandonType::class, $dons);
        if ($searchcotisation->handleRequest($request)->isSubmitted()) {
            $criteria = $searchcotisation->getData();

            $criteres = array();

            $criteres["eglise"] = $eglise->getId();

            if ($criteria["dateDebut"]) {
                $dateDebut = ($criteria["dateDebut"]);
                //  $dateDebut = DateTime::createFromFormat('Y-m-d',$dateDebuts);
            }

            if ($criteria["dateFin"]) {
                $dateFin = $criteria["dateFin"];

                // $dateFin = DateTime::createFromFormat('Y-m-d',$dateFins);
            }

            if ($criteria["nature"]) {
                $criteres["nature"] = $criteria["nature"];
            }

            $limit = 1000;
            $dons = $donRepository->rechercheDon($criteres, $dateDebut, $dateFin, $limit);
        }

        return $this->render('bilan/finance/don.html.twig', [
                    'form_recherche' => $searchcotisation->createView(),
                    'dons' => $dons,
        ]);
    }

    #[Route('/bilandonn', name: 'app_bilan_donn', methods: ['GET', 'POST'])]
    public function rechercheDonn(Request $request, DonRepository $donRepository,
            DateTime $dateDebut = null, DateTime $dateFin = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $communaute = $this->getUser()->getEglise()->getCommunaute();
        $user = $this->getUser();

        $dons = [];

//        $difference = $cotisationRepository->getSeanceByDates();
        $searchcotisation = $this->createForm(BilandonnType::class, $dons);
        if ($searchcotisation->handleRequest($request)->isSubmitted()) {
            $criteria = $searchcotisation->getData();

            $criteres = array();

            $criteres["communaute"] = $communaute->getId();
            if ($criteria["dateDebut"]) {
                $dateDebut = ($criteria["dateDebut"]);
                //  $dateDebut = DateTime::createFromFormat('Y-m-d',$dateDebuts);
            }

            if ($criteria["dateFin"]) {
                $dateFin = $criteria["dateFin"];

                // $dateFin = DateTime::createFromFormat('Y-m-d',$dateFins);
            }

            if ($criteria["nature"]) {
                $criteres["nature"] = $criteria["nature"];
            }

            $limit = 1000;
            $dons = $donRepository->rechercheDonn($criteres, $dateDebut, $dateFin, $limit);
        }

        return $this->render('bilan/national/don.html.twig', [
                    'form_recherche' => $searchcotisation->createView(),
                    'dons' => $dons,
        ]);
    }

    #[Route('/bilanoperation', name: 'app_bilan_operation', methods: ['GET', 'POST'])]
    public function rechercheOperation(Request $request, OperationRepository $operationRepository,
            DateTime $dateDebut = null, DateTime $dateFin = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $eglise = $this->getUser()->getEglise();

        $operations = [];

//        $difference = $cotisationRepository->getSeanceByDates();
        $searchcotisation = $this->createForm(BilanoperationType::class, $operations);
        if ($searchcotisation->handleRequest($request)->isSubmitted()) {
            $criteria = $searchcotisation->getData();

            $criteres = array();

            $criteres["eglise"] = $eglise->getId();

            if ($criteria["dateDebut"]) {
                $dateDebut = ($criteria["dateDebut"]);
                //  $dateDebut = DateTime::createFromFormat('Y-m-d',$dateDebuts);
            }

            if ($criteria["dateFin"]) {
                $dateFin = $criteria["dateFin"];

                // $dateFin = DateTime::createFromFormat('Y-m-d',$dateFins);
            }


            if ($criteria["objet"]) {
                $criteres["objet"] = $criteria["objet"];
            }


            $limit = 1000;
            $operations = $operationRepository->rechercheOperation($criteres, $dateDebut, $dateFin, $limit);
        }

        return $this->render('bilan/finance/operation.html.twig', [
                    'form_recherche' => $searchcotisation->createView(),
                    'operations' => $operations,
        ]);
    }

    #[Route('/bilanoperationn', name: 'app_bilan_operationn', methods: ['GET', 'POST'])]
    public function rechercheOperationn(Request $request, OperationRepository $operationRepository,
            DateTime $dateDebut = null, DateTime $dateFin = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $communaute = $this->getUser()->getEglise()->getCommunaute();
        $user = $this->getUser();

        $operations = [];

//        $difference = $cotisationRepository->getSeanceByDates();
        $searchcotisation = $this->createForm(BilanoperationnType::class, $operations);
        if ($searchcotisation->handleRequest($request)->isSubmitted()) {
            $criteria = $searchcotisation->getData();

            $criteres = array();

            $criteres["communaute"] = $communaute->getId();

            if ($criteria["dateDebut"]) {
                $dateDebut = ($criteria["dateDebut"]);
                //  $dateDebut = DateTime::createFromFormat('Y-m-d',$dateDebuts);
            }

            if ($criteria["dateFin"]) {
                $dateFin = $criteria["dateFin"];

                // $dateFin = DateTime::createFromFormat('Y-m-d',$dateFins);
            }


            if ($criteria["objet"]) {
                $criteres["objet"] = $criteria["objet"];
            }


            $limit = 1000;
            $operations = $operationRepository->rechercheOperationn($criteres, $dateDebut, $dateFin, $limit);
        }

        return $this->render('bilan/national/operation.html.twig', [
                    'form_recherche' => $searchcotisation->createView(),
                    'operations' => $operations,
        ]);
    }

    #[Route('/bilanactiongrace', name: 'app_bilan_actiongrace', methods: ['GET', 'POST'])]
    public function rechercheActiongrace(Request $request, ActiongraceRepository $actiongraceRepository, FideleRepository $fideleRepository,
            DateTime $dateDebut = null, DateTime $dateFin = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        $eglise = $this->getUser()->getEglise();
        $actiongraces = [];
        $fidele = $fideleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);

        $searchcotisation = $this->createForm(BilanactiongraceType::class, $actiongraces, ['fidele' => $fidele]);
        if ($searchcotisation->handleRequest($request)->isSubmitted()) {
            $criteria = $searchcotisation->getData();

            $criteres = array();

            $criteres["eglise"] = $eglise->getId();

            if ($criteria["dateDebut"]) {
                $dateDebut = ($criteria["dateDebut"]);
                //  $dateDebut = DateTime::createFromFormat('Y-m-d',$dateDebuts);
            }


            if ($criteria["fidele"]) {
                $criteres["fidele"] = $criteria["fidele"];
            }

            if ($criteria["dateFin"]) {
                $dateFin = ($criteria["dateFin"]);
                //  $dateDebut = DateTime::createFromFormat('Y-m-d',$dateDebuts);
            }



            $limit = 1000;
            $actiongraces = $actiongraceRepository->rechercheActiongrace($criteres, $dateDebut, $dateFin, $limit);
        }

        return $this->render('bilan/finance/actiongrace.html.twig', [
                    'form_recherche' => $searchcotisation->createView(),
                    'actiongraces' => $actiongraces,
        ]);
    }

    #[Route('/bilanactiongracen', name: 'app_bilan_actiongracen', methods: ['GET', 'POST'])]
    public function rechercheActiongracen(Request $request, ActiongraceRepository $actiongraceRepository, DateTime $dateDebut = null, DateTime $dateFin = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $communaute = $this->getUser()->getEglise()->getCommunaute();
        $user = $this->getUser();
        $actiongraces = [];

        $searchcotisation = $this->createForm(BilanactiongracenType::class, $actiongraces);
        if ($searchcotisation->handleRequest($request)->isSubmitted()) {
            $criteria = $searchcotisation->getData();

            $criteres = array();

            $criteres["communaute"] = $communaute->getId();
            if ($criteria["dateDebut"]) {
                $dateDebut = ($criteria["dateDebut"]);
                //  $dateDebut = DateTime::createFromFormat('Y-m-d',$dateDebuts);
            }




            if ($criteria["dateFin"]) {
                $dateFin = ($criteria["dateFin"]);
                //  $dateDebut = DateTime::createFromFormat('Y-m-d',$dateDebuts);
            }



            $limit = 1000;
            $actiongraces = $actiongraceRepository->rechercheActiongracen($criteres, $dateDebut, $dateFin, $limit);
        }

        return $this->render('bilan/national/actiongrace.html.twig', [
                    'form_recherche' => $searchcotisation->createView(),
                    'actiongraces' => $actiongraces,
        ]);
    }

    #[Route('/bilandime', name: 'app_bilan_dime', methods: ['GET', 'POST'])]
    public function rechercheDime(Request $request, DimeRepository $dimeRepository, FideleRepository $fideleRepository,
            DateTime $dateDebut = null, DateTime $dateFin = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $eglise = $this->getUser()->getEglise();

        $dimes = [];

        $fidele = $fideleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);

        $searchcotisation = $this->createForm(BilandimeType::class, $dimes, ['fidele' => $fidele]);
        if ($searchcotisation->handleRequest($request)->isSubmitted()) {
            $criteria = $searchcotisation->getData();

            $criteres = array();

            $criteres["eglise"] = $eglise->getId();

            if ($criteria["dateDebut"]) {
                $dateDebut = ($criteria["dateDebut"]);
                //  $dateDebut = DateTime::createFromFormat('Y-m-d',$dateDebuts);
            }


            if ($criteria["fidele"]) {
                $criteres["fidele"] = $criteria["fidele"];
            }

            if ($criteria["dateFin"]) {
                $dateFin = $criteria["dateFin"];

                // $dateFin = DateTime::createFromFormat('Y-m-d',$dateFins);
            }


            $limit = 1000;
            $dimes = $dimeRepository->rechercheDime($criteres, $dateDebut, $dateFin, $limit);
        }

        return $this->render('bilan/finance/dime.html.twig', [
                    'form_recherche' => $searchcotisation->createView(),
                    'dimes' => $dimes,
        ]);
    }

    #[Route('/bilandimen', name: 'app_bilan_dimen', methods: ['GET', 'POST'])]
    public function rechercheDimen(Request $request, DimeRepository $dimeRepository,
            DateTime $dateDebut = null, DateTime $dateFin = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $communaute = $this->getUser()->getEglise()->getCommunaute();
        $user = $this->getUser();

        $dimes = [];

        $searchcotisation = $this->createForm(BilandimenType::class, $dimes);
        if ($searchcotisation->handleRequest($request)->isSubmitted()) {
            $criteria = $searchcotisation->getData();

            $criteres = array();

            $criteres["communaute"] = $communaute->getId();
            if ($criteria["dateDebut"]) {
                $dateDebut = ($criteria["dateDebut"]);
            }


            if ($criteria["dateFin"]) {
                $dateFin = $criteria["dateFin"];
            }


            $limit = 1000;
            $dimes = $dimeRepository->rechercheDimen($criteres, $dateDebut, $dateFin, $limit);
        }

        return $this->render('bilan/national/dime.html.twig', [
                    'form_recherche' => $searchcotisation->createView(),
                    'dimes' => $dimes,
        ]);
    }

    #[Route('/bilandimeglobale', name: 'app_bilan_dimeglobale', methods: ['GET', 'POST'])]
    public function rechercheDimeGlobale(Request $request, DimeglobaleRepository $dimeglobaleRepository,
            DateTime $dateDebut = null, DateTime $dateFin = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $eglise = $this->getUser()->getEglise();

        $dimeglobales = [];

//        $difference = $cotisationRepository->getSeanceByDates();
        $searchcotisation = $this->createForm(BilandimeglobaleType::class, $dimeglobales);
        if ($searchcotisation->handleRequest($request)->isSubmitted()) {
            $criteria = $searchcotisation->getData();

            $criteres = array();

            $criteres["eglise"] = $eglise->getId();

            if ($criteria["dateDebut"]) {
                $dateDebut = ($criteria["dateDebut"]);
                //  $dateDebut = DateTime::createFromFormat('Y-m-d',$dateDebuts);
            }

            if ($criteria["dateFin"]) {
                $dateFin = $criteria["dateFin"];

                // $dateFin = DateTime::createFromFormat('Y-m-d',$dateFins);
            }


            $limit = 1000;
            $dimeglobales = $dimeglobaleRepository->rechercheDimeGlobale($criteres, $dateDebut, $dateFin, $limit);
        }

        return $this->render('bilan/finance/dimeglobale.html.twig', [
                    'form_recherche' => $searchcotisation->createView(),
                    'dimeglobales' => $dimeglobales,
        ]);
    }

    #[Route('/bilandimeglobalen', name: 'app_bilan_dimeglobalen', methods: ['GET', 'POST'])]
    public function rechercheDimeGlobalen(Request $request, DimeglobaleRepository $dimeglobaleRepository,
            DateTime $dateDebut = null, DateTime $dateFin = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $communaute = $this->getUser()->getEglise()->getCommunaute();
        $user = $this->getUser();

        $dimeglobales = [];

//        $difference = $cotisationRepository->getSeanceByDates();
        $searchcotisation = $this->createForm(BilandimeglobalenType::class, $dimeglobales);
        if ($searchcotisation->handleRequest($request)->isSubmitted()) {
            $criteria = $searchcotisation->getData();

            $criteres = array();

            $criteres["communaute"] = $communaute->getId();
            if ($criteria["dateDebut"]) {
                $dateDebut = ($criteria["dateDebut"]);
                //  $dateDebut = DateTime::createFromFormat('Y-m-d',$dateDebuts);
            }

            if ($criteria["dateFin"]) {
                $dateFin = $criteria["dateFin"];

                // $dateFin = DateTime::createFromFormat('Y-m-d',$dateFins);
            }


            $limit = 1000;
            $dimeglobales = $dimeglobaleRepository->rechercheDimeGlobalen($criteres, $dateDebut, $dateFin, $limit);
        }

        return $this->render('bilan/national/dimeglobale.html.twig', [
                    'form_recherche' => $searchcotisation->createView(),
                    'dimeglobales' => $dimeglobales,
        ]);
    }

    #[Route('/bilanevangelisation', name: 'app_bilan_evangelisation', methods: ['GET', 'POST'])]
    public function rechercheEvangelisation(Request $request, EvangelisationRepository $evangelisationRepository,
            DateTime $dateDebut = null, DateTime $dateFin = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $eglise = $this->getUser()->getEglise();

        $evangelisations = [];

//        $difference = $cotisationRepository->getSeanceByDates();
        $searchcotisation = $this->createForm(BilanevangelisationType::class, $evangelisations);
        if ($searchcotisation->handleRequest($request)->isSubmitted()) {
            $criteria = $searchcotisation->getData();

            $criteres = array();

            $criteres["eglise"] = $eglise->getId();

            if ($criteria["dateDebut"]) {
                $dateDebut = ($criteria["dateDebut"]);
                //  $dateDebut = DateTime::createFromFormat('Y-m-d',$dateDebuts);
            }

            if ($criteria["dateFin"]) {
                $dateFin = $criteria["dateFin"];

                // $dateFin = DateTime::createFromFormat('Y-m-d',$dateFins);
            }


            $limit = 1000;
            $evangelisations = $evangelisationRepository->rechercheEvangelisation($criteres, $dateDebut, $dateFin, $limit);
        }

        return $this->render('bilan/activite/activite_evangelisation.html.twig', [
                    'form_recherche' => $searchcotisation->createView(),
                    'evangelisations' => $evangelisations,
        ]);
    }

    #[Route('/bilanevangelisationn', name: 'app_bilan_evangelisationn', methods: ['GET', 'POST'])]
    public function rechercheEvangelisationn(Request $request, EvangelisationRepository $evangelisationRepository,
            DateTime $dateDebut = null, DateTime $dateFin = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $communaute = $this->getUser()->getEglise()->getCommunaute();
        $user = $this->getUser();

        $evangelisations = [];

//        $difference = $cotisationRepository->getSeanceByDates();
        $searchcotisation = $this->createForm(BilanevangelisationnType::class, $evangelisations);
        if ($searchcotisation->handleRequest($request)->isSubmitted()) {
            $criteria = $searchcotisation->getData();

            $criteres = array();

            $criteres["communaute"] = $communaute->getId();

            if ($criteria["dateDebut"]) {
                $dateDebut = ($criteria["dateDebut"]);
                //  $dateDebut = DateTime::createFromFormat('Y-m-d',$dateDebuts);
            }

            if ($criteria["dateFin"]) {
                $dateFin = $criteria["dateFin"];

                // $dateFin = DateTime::createFromFormat('Y-m-d',$dateFins);
            }


            $limit = 1000;
            $evangelisations = $evangelisationRepository->rechercheEvangelisationn($criteres, $dateDebut, $dateFin, $limit);
        }

        return $this->render('bilan/national/activite_evangelisation.html.twig', [
                    'form_recherche' => $searchcotisation->createView(),
                    'evangelisations' => $evangelisations,
        ]);
    }

    #[Route('/bilanmariage', name: 'app_bilan_mariage', methods: ['GET', 'POST'])]
    public function rechercheMariage(Request $request, MariageRepository $mariageRepository,
            DateTime $dateDebut = null, DateTime $dateFin = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $eglise = $this->getUser()->getEglise();

        $mariages = [];

//        $difference = $cotisationRepository->getSeanceByDates();
        $searchcotisation = $this->createForm(BilanemariageType::class, $mariages);
        if ($searchcotisation->handleRequest($request)->isSubmitted()) {
            $criteria = $searchcotisation->getData();

            $criteres = array();

            $criteres["eglise"] = $eglise->getId();

            if ($criteria["dateDebut"]) {
                $dateDebut = ($criteria["dateDebut"]);
                //  $dateDebut = DateTime::createFromFormat('Y-m-d',$dateDebuts);
            }

            if ($criteria["dateFin"]) {
                $dateFin = $criteria["dateFin"];

                // $dateFin = DateTime::createFromFormat('Y-m-d',$dateFins);
            }

            if ($criteria["regime"]) {
                $criteres["regime"] = $criteria["regime"];
            }

            $limit = 1000;
            $mariages = $mariageRepository->rechercheMariage($criteres, $dateDebut, $dateFin, $limit);
        }

        return $this->render('bilan/activite/activite_mariage.html.twig', [
                    'form_recherche' => $searchcotisation->createView(),
                    'mariages' => $mariages,
        ]);
    }

    #[Route('/bilanmariagen', name: 'app_bilan_mariagen', methods: ['GET', 'POST'])]
    public function rechercheMariagen(Request $request, MariageRepository $mariageRepository,
            DateTime $dateDebut = null, DateTime $dateFin = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $communaute = $this->getUser()->getEglise()->getCommunaute();
        $user = $this->getUser();

        $mariages = [];

//        $difference = $cotisationRepository->getSeanceByDates();
        $searchcotisation = $this->createForm(BilanmariagenType::class, $mariages);
        if ($searchcotisation->handleRequest($request)->isSubmitted()) {
            $criteria = $searchcotisation->getData();

            $criteres = array();

            $criteres["communaute"] = $communaute->getId();

            if ($criteria["dateDebut"]) {
                $dateDebut = ($criteria["dateDebut"]);
                //  $dateDebut = DateTime::createFromFormat('Y-m-d',$dateDebuts);
            }

            if ($criteria["dateFin"]) {
                $dateFin = $criteria["dateFin"];

                // $dateFin = DateTime::createFromFormat('Y-m-d',$dateFins);
            }

            if ($criteria["regime"]) {
                $criteres["regime"] = $criteria["regime"];
            }

            $limit = 1000;
            $mariages = $mariageRepository->rechercheMariagen($criteres, $dateDebut, $dateFin, $limit);
        }

        return $this->render('bilan/national/activite_mariage.html.twig', [
                    'form_recherche' => $searchcotisation->createView(),
                    'mariages' => $mariages,
        ]);
    }

    #[Route('/bilannaissance', name: 'app_bilan_naissance', methods: ['GET', 'POST'])]
    public function rechercheNaissance(Request $request, NaissanceRepository $naissanceRepository,
            DateTime $dateDebut = null, DateTime $dateFin = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $eglise = $this->getUser()->getEglise();

        $naissances = [];

//        $difference = $cotisationRepository->getSeanceByDates();
        $searchcotisation = $this->createForm(BilannaissanceType::class, $naissances);
        if ($searchcotisation->handleRequest($request)->isSubmitted()) {
            $criteria = $searchcotisation->getData();

            $criteres = array();

            $criteres["eglise"] = $eglise->getId();

            if ($criteria["dateDebut"]) {
                $dateDebut = ($criteria["dateDebut"]);
                //  $dateDebut = DateTime::createFromFormat('Y-m-d',$dateDebuts);
            }

            if ($criteria["dateFin"]) {
                $dateFin = $criteria["dateFin"];

                // $dateFin = DateTime::createFromFormat('Y-m-d',$dateFins);
            }

            if ($criteria["sexenaiss"]) {
                $criteres["sexenaiss"] = $criteria["sexenaiss"];
            }
            $limit = 1000;
            $naissances = $naissanceRepository->rechercheNaissance($criteres, $dateDebut, $dateFin, $limit);
        }

        return $this->render('bilan/annuel/presentation.html.twig', [
                    'form_recherche' => $searchcotisation->createView(),
                    'naissances' => $naissances,
        ]);
    }

    #[Route('/bilannaissancen', name: 'app_bilan_naissancen', methods: ['GET', 'POST'])]
    public function rechercheNaissancen(Request $request, NaissanceRepository $naissanceRepository,
            DateTime $dateDebut = null, DateTime $dateFin = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $communaute = $this->getUser()->getEglise()->getCommunaute();
        $user = $this->getUser();

        $naissances = [];

//        $difference = $cotisationRepository->getSeanceByDates();
        $searchcotisation = $this->createForm(BilannaissancenType::class, $naissances);
        if ($searchcotisation->handleRequest($request)->isSubmitted()) {
            $criteria = $searchcotisation->getData();

            $criteres = array();

            $criteres["communaute"] = $communaute->getId();
            if ($criteria["dateDebut"]) {
                $dateDebut = ($criteria["dateDebut"]);
                //  $dateDebut = DateTime::createFromFormat('Y-m-d',$dateDebuts);
            }

            if ($criteria["dateFin"]) {
                $dateFin = $criteria["dateFin"];

                // $dateFin = DateTime::createFromFormat('Y-m-d',$dateFins);
            }

            if ($criteria["sexenaiss"]) {
                $criteres["sexenaiss"] = $criteria["sexenaiss"];
            }
            $limit = 1000;
            $naissances = $naissanceRepository->rechercheNaissancen($criteres, $dateDebut, $dateFin, $limit);
        }

        return $this->render('bilan/national/presentation.html.twig', [
                    'form_recherche' => $searchcotisation->createView(),
                    'naissances' => $naissances,
        ]);
    }

    #[Route('/bilanfinance', name: 'app_bilan_finance', methods: ['GET', 'POST'])]
    public function rechercheBilan(Request $request, OffrandeRepository $offrandeRepository,
            DimeglobaleRepository $dimeglobaleRepository, DimeRepository $dimeRepository,
            ActiongraceRepository $actiongraceRepository, DonRepository $donRepository, OperationRepository $operationRepository,
            DateTime $dateDebut = null, DateTime $dateFin = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $eglise = $this->getUser()->getEglise();

        $offrandes = [];
        $actiongraces = [];
        $dimes = [];
        $dimeglobales = [];
        $dons = [];
        $operations = [];

//        $difference = $cotisationRepository->getSeanceByDates();
        $searchcotisation = $this->createForm(BilanType::class, $offrandes, $dimes, $actiongraces, $actiongraces, $dons, $dimeglobales, $operations);
        if ($searchcotisation->handleRequest($request)->isSubmitted()) {
            $criteria = $searchcotisation->getData();

            $criteres = array();

            $criteres["eglise"] = $eglise->getId();

            if ($criteria["dateDebut"]) {
                $dateDebut = ($criteria["dateDebut"]);
                //  $dateDebut = DateTime::createFromFormat('Y-m-d',$dateDebuts);
            }

            if ($criteria["dateFin"]) {
                $dateFin = $criteria["dateFin"];

                // $dateFin = DateTime::createFromFormat('Y-m-d',$dateFins);
            }


            $limit = 1000;
            $offrandes = $offrandeRepository->rechercheOffrande($criteres, $dateDebut, $dateFin, $limit);
            $operations = $operationRepository->rechercheOperation($criteres, $dateDebut, $dateFin, $limit);

            $dimes = $dimeRepository->rechercheDime($criteres, $dateDebut, $dateFin, $limit);
            $actiongraces = $actiongraceRepository->rechercheActiongrace($criteres, $dateDebut, $dateFin, $limit);
            $dimeglobales = $dimeglobaleRepository->rechercheDimeGlobale($criteres, $dateDebut, $dateFin, $limit);
            $dons = $donRepository->rechercheDon($criteres, $dateDebut, $dateFin, $limit);
        }

        return $this->render('bilan/finance/finance.html.twig', [
                    'form_recherche' => $searchcotisation->createView(),
                    'offrandes' => $offrandes,
                    'dimeglobales' => $dimeglobales,
                    'dimes' => $dimes,
                    'actiongraces' => $actiongraces,
                    'dons' => $dons,
                    'operations' => $operations,
        ]);
    }

//     #[Route('/bilanculte', name: 'app_bilan_culte', methods: ['GET', 'POST'])]
//     public function rechercheCulte(Request $request, CulteRepository $culteRepository,
//             DateTime $dateDebut = null, DateTime $dateFin = null): Response {
//         $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
//         $user = $this->getUser();

//         $eglise = $this->getUser()->getEglise();

//         $cultes = [];

// //        $difference = $cotisationRepository->getSeanceByDates();
//         $searchcotisation = $this->createForm(BilanculteType::class, $cultes);
//         if ($searchcotisation->handleRequest($request)->isSubmitted()) {
//             $criteria = $searchcotisation->getData();

//             $criteres = array();

//             $criteres["eglise"] = $eglise->getId();

//             if ($criteria["dateDebut"]) {
//                 $dateDebut = ($criteria["dateDebut"]);
//                 //  $dateDebut = DateTime::createFromFormat('Y-m-d',$dateDebuts);
//             }

//             if ($criteria["dateFin"]) {
//                 $dateFin = $criteria["dateFin"];
//             }
//             if ($criteria["typeculte"]) {
//                 $criteres["typeculte"] = $criteria["typeculte"];
//             }


//             $limit = 1000;
//             $cultes = $culteRepository->rechercheCulte($criteres, $dateDebut, $dateFin, $limit);
//         }

//         return $this->render('bilan/annuel/culte.html.twig', [
//                     'form_recherche' => $searchcotisation->createView(),
//                     'cultes' => $cultes,
//         ]);
//     }
//Nouveau bilan cultes
#[Route('/bilanculte', name: 'app_bilan_culte', methods: ['GET', 'POST'])]
public function rechercheCulte(
    Request $request, 
    CulteRepository $culteRepository
): Response {
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    
    $user = $this->getUser();
    $eglise = $user->getEglise();
    
    if (!$eglise) {
        $this->addFlash('error', 'Aucune église associée à votre compte.');
        return $this->redirectToRoute('app_accueil');
    }
    
    // Créer le DTO et le formulaire
    $searchDTO = new BilanCulteDTO();
    $form = $this->createForm(BilanculteType::class, $searchDTO);
    $form->handleRequest($request);
    
    $bilanDTO = null;
    
    // Vérifier si le formulaire est soumis (même sans données)
    if ($form->isSubmitted() && $form->isValid()) {
        // Construire les critères de recherche
        $searchCriteria = ['eglise' => $eglise->getId()];
        
        // Ajouter les filtres optionnels - passer les objets directement
        if ($searchDTO->getTypeculte()) {
            $searchCriteria['typeculte'] = $searchDTO->getTypeculte()->getId();
        }
        
        if ($searchDTO->getMessager()) {
            $searchCriteria['messager'] = $searchDTO->getMessager()->getId();
        }
        
        if ($searchDTO->getDirigeant()) {
            $searchCriteria['dirigeant'] = $searchDTO->getDirigeant()->getId();
        }
        
        $dateDebut = $searchDTO->getDateDebut();
        $dateFin = $searchDTO->getDateFin();
        
        // Validation des dates
        if ($dateDebut && $dateFin && $dateDebut > $dateFin) {
            $this->addFlash('warning', 'La date de début doit être antérieure à la date de fin.');
            $cultes = [];
        } else {
            $limit = 1000;
            $cultes = $culteRepository->rechercheCulte($searchCriteria, $dateDebut, $dateFin, $limit);
        }
        
        $bilanDTO = new BilanCulteDTO($cultes ?? []);
        
        if (! $bilanDTO) {
            $this->addFlash('info', 'Aucune donnée trouvée pour les critères sélectionnés.');
        }
    }
    
    return $this->render('bilan/annuel/culte.html.twig', [
        'form_recherche' => $form->createView(),
        'bilan' => $bilanDTO,
    ]);
}
//Fin bilan cultes

    #[Route('/bilanculten', name: 'app_bilan_culten', methods: ['GET', 'POST'])]
    public function rechercheCulten(Request $request,  RegionRepository $regionRepository, CulteRepository $culteRepository,
            DateTime $dateDebut = null, DateTime $dateFin = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $communaute = $this->getUser()->getEglise()->getCommunaute();
        $user = $this->getUser();

        $cultes = [];

        $region = $regionRepository->findBy(["deletedAt" => NULL]);

        $searchcotisation = $this->createForm(BilancultenType::class, $cultes, ['region' => $region]);
        if ($searchcotisation->handleRequest($request)->isSubmitted()) {
            $criteria = $searchcotisation->getData();

            $criteres = array();

             $criteres["communaute"] = $communaute->getId();

            if ($criteria["region"]) {
                $criteres["region"] = $criteria["region"];
            }
            
            if ($criteria["dateDebut"]) {
                $dateDebut = ($criteria["dateDebut"]);
                //  $dateDebut = DateTime::createFromFormat('Y-m-d',$dateDebuts);
            }

            if ($criteria["dateFin"]) {
                $dateFin = $criteria["dateFin"];

                // $dateFin = DateTime::createFromFormat('Y-m-d',$dateFins);
            }
            if ($criteria["typeculte"]) {
                $criteres["typeculte"] = $criteria["typeculte"];
            }


            $limit = 1000;
            $cultes = $culteRepository->rechercheCulten($criteres, $dateDebut, $dateFin, $limit);
        }

        return $this->render('bilan/national/culte.html.twig', [
                    'form_recherche' => $searchcotisation->createView(),
                    'cultes' => $cultes,
        ]);
    }

    #[Route('/bilanprogramme', name: 'app_bilan_programme', methods: ['GET', 'POST'])]
    public function rechercheProgramme(Request $request, ProgrammeRepository $programmeRepository,
            DateTime $dateDebut = null, DateTime $dateFin = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $eglise = $this->getUser()->getEglise();

        $programmes = [];

//        $difference = $cotisationRepository->getSeanceByDates();
        $searchcotisation = $this->createForm(BilanprogrammeType::class, $programmes);
        if ($searchcotisation->handleRequest($request)->isSubmitted()) {
            $criteria = $searchcotisation->getData();

            $criteres = array();

            $criteres["eglise"] = $eglise->getId();

            if ($criteria["dateDebut"]) {
                $dateDebut = ($criteria["dateDebut"]);
                //  $dateDebut = DateTime::createFromFormat('Y-m-d',$dateDebuts);
            }

            if ($criteria["dateFin"]) {
                $dateFin = $criteria["dateFin"];

                // $dateFin = DateTime::createFromFormat('Y-m-d',$dateFins);
            }


            $limit = 1000;
            $programmes = $programmeRepository->rechercheProgramme($criteres, $dateDebut, $dateFin, $limit);
        }

        return $this->render('bilan/annuel/programme.html.twig', [
                    'form_recherche' => $searchcotisation->createView(),
                    'programmes' => $programmes,
        ]);
    }

    #[Route('/bilanprogrammen', name: 'app_bilan_programmen', methods: ['GET', 'POST'])]
    public function rechercheProgrammen(Request $request, ProgrammeRepository $programmeRepository,
            DateTime $dateDebut = null, DateTime $dateFin = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $communaute = $this->getUser()->getEglise()->getCommunaute();
        $user = $this->getUser();

        $programmes = [];

//        $difference = $cotisationRepository->getSeanceByDates();
        $searchcotisation = $this->createForm(BilanprogrammenType::class, $programmes);
        if ($searchcotisation->handleRequest($request)->isSubmitted()) {
            $criteria = $searchcotisation->getData();

            $criteres = array();

            $criteres["communaute"] = $communaute->getId();

            if ($criteria["dateDebut"]) {
                $dateDebut = ($criteria["dateDebut"]);
                //  $dateDebut = DateTime::createFromFormat('Y-m-d',$dateDebuts);
            }

            if ($criteria["dateFin"]) {
                $dateFin = $criteria["dateFin"];

                // $dateFin = DateTime::createFromFormat('Y-m-d',$dateFins);
            }


            $limit = 1000;
            $programmes = $programmeRepository->rechercheProgrammen($criteres, $dateDebut, $dateFin, $limit);
        }

        return $this->render('bilan/national/programme.html.twig', [
                    'form_recherche' => $searchcotisation->createView(),
                    'programmes' => $programmes,
        ]);
    }

    #[Route('/bilanvisite', name: 'app_bilan_visite', methods: ['GET', 'POST'])]
    public function rechercheVisite(Request $request, VisiteRepository $visiteRepository, FideleRepository $fideleRepository,
            DateTime $dateDebut = null, DateTime $dateFin = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $eglise = $this->getUser()->getEglise();

        $visites = [];
        $fidele = $fideleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);

//        $difference = $cotisationRepository->getSeanceByDates();
        $searchcotisation = $this->createForm(BilanvisiteType::class, $visites, ['receptionpar' => $fidele]);
        if ($searchcotisation->handleRequest($request)->isSubmitted()) {
            $criteria = $searchcotisation->getData();

            $criteres = array();

            $criteres["eglise"] = $eglise->getId();

            if ($criteria["dateDebut"]) {
                $dateDebut = ($criteria["dateDebut"]);
                //  $dateDebut = DateTime::createFromFormat('Y-m-d',$dateDebuts);
            }

            if ($criteria["dateFin"]) {
                $dateFin = $criteria["dateFin"];

                // $dateFin = DateTime::createFromFormat('Y-m-d',$dateFins);
            }
            if ($criteria["sexe"]) {
                $criteres["sexe"] = $criteria["sexe"];
            }
            if ($criteria["receptionpar"]) {
                $criteres["receptionpar"] = $criteria["receptionpar"];
            }

            $limit = 1000;
            $visites = $visiteRepository->rechercheVisite($criteres, $dateDebut, $dateFin, $limit);
        }

        return $this->render('bilan/annuel/visite.html.twig', [
                    'form_recherche' => $searchcotisation->createView(),
                    'visites' => $visites,
        ]);
    }

    #[Route('/bilanvisiten', name: 'app_bilan_visiten', methods: ['GET', 'POST'])]
    public function rechercheVisiten(Request $request, VisiteRepository $visiteRepository, DateTime $dateDebut = null, DateTime $dateFin = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $communaute = $this->getUser()->getEglise()->getCommunaute();
        $user = $this->getUser();

        $visites = [];

//        $difference = $cotisationRepository->getSeanceByDates();
        $searchcotisation = $this->createForm(BilanvisitenType::class, $visites);
        if ($searchcotisation->handleRequest($request)->isSubmitted()) {
            $criteria = $searchcotisation->getData();

            $criteres = array();

            $criteres["communaute"] = $communaute->getId();
            if ($criteria["dateDebut"]) {
                $dateDebut = ($criteria["dateDebut"]);
                //  $dateDebut = DateTime::createFromFormat('Y-m-d',$dateDebuts);
            }

            if ($criteria["dateFin"]) {
                $dateFin = $criteria["dateFin"];

                // $dateFin = DateTime::createFromFormat('Y-m-d',$dateFins);
            }
        

            $limit = 1000;
            $visites = $visiteRepository->rechercheVisiten($criteres, $dateDebut, $dateFin, $limit);
        }

        return $this->render('bilan/national/visite.html.twig', [
                    'form_recherche' => $searchcotisation->createView(),
                    'visites' => $visites,
        ]);
    }

    #[Route('/bilanpatrimoine', name: 'app_bilan_patrimoine', methods: ['GET', 'POST'])]
    public function recherchePatrimoine(Request $request, PatrimoineRepository $patrimoineRepository,
            DateTime $dateDebut = null, DateTime $dateFin = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $eglise = $this->getUser()->getEglise();

        $patrimoines = [];

//        $difference = $cotisationRepository->getSeanceByDates();
        $searchcotisation = $this->createForm(BilanpatrimoineType::class, $patrimoines);
        if ($searchcotisation->handleRequest($request)->isSubmitted()) {
            $criteria = $searchcotisation->getData();

            $criteres = array();

            $criteres["eglise"] = $eglise->getId();

            if ($criteria["dateDebut"]) {
                $dateDebut = ($criteria["dateDebut"]);
                //  $dateDebut = DateTime::createFromFormat('Y-m-d',$dateDebuts);
            }

            if ($criteria["dateFin"]) {
                $dateFin = $criteria["dateFin"];

                // $dateFin = DateTime::createFromFormat('Y-m-d',$dateFins);
            }


            $limit = 1000;
            $patrimoines = $patrimoineRepository->recherchePatrimoine($criteres, $dateDebut, $dateFin, $limit);
        }

        return $this->render('bilan/annuel/patrimoine.html.twig', [
                    'form_recherche' => $searchcotisation->createView(),
                    'patrimoines' => $patrimoines,
        ]);
    }

    #[Route('/bilanpatrimoinen', name: 'app_bilan_patrimoinen', methods: ['GET', 'POST'])]
    public function recherchePatrimoinen(Request $request, PatrimoineRepository $patrimoineRepository,
            DateTime $dateDebut = null, DateTime $dateFin = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $communaute = $this->getUser()->getEglise()->getCommunaute();
        $user = $this->getUser();

        $patrimoines = [];

//        $difference = $cotisationRepository->getSeanceByDates();
        $searchcotisation = $this->createForm(BilanpatrimoinenType::class, $patrimoines);
        if ($searchcotisation->handleRequest($request)->isSubmitted()) {
            $criteria = $searchcotisation->getData();

            $criteres = array();

            $criteres["communaute"] = $communaute->getId();

            if ($criteria["dateDebut"]) {
                $dateDebut = ($criteria["dateDebut"]);
                //  $dateDebut = DateTime::createFromFormat('Y-m-d',$dateDebuts);
            }

            if ($criteria["dateFin"]) {
                $dateFin = $criteria["dateFin"];

                // $dateFin = DateTime::createFromFormat('Y-m-d',$dateFins);
            }


            $limit = 1000;
            $patrimoines = $patrimoineRepository->recherchePatrimoinen($criteres, $dateDebut, $dateFin, $limit);
        }

        return $this->render('bilan/national/patrimoine.html.twig', [
                    'form_recherche' => $searchcotisation->createView(),
                    'patrimoines' => $patrimoines,
        ]);
    }

    #[Route('/bilaninvite', name: 'app_bilan_invite', methods: ['GET', 'POST'])]
    public function rechercheInvite(Request $request, InviteRepository $inviteRepository, CulteRepository $culteRepository, FideleRepository $fideleRepository
            , DateTime $dateDebut = null, DateTime $dateFin = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $eglise = $this->getUser()->getEglise();

        $invites = [];

        $fideles = $fideleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);

        $cultes = $culteRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $searchcotisation = $this->createForm(BilaninviteType::class, $invites, ['fidele' => $fideles, 'culte' => $cultes]);
        if ($searchcotisation->handleRequest($request)->isSubmitted()) {
            $criteria = $searchcotisation->getData();

            $criteres = array();

            $criteres["eglise"] = $eglise->getId();

            if ($criteria["culte"]) {
                $criteres["culte"] = $criteria["culte"];
            }


            if ($criteria["fidele"]) {
                $criteres["fidele"] = $criteria["fidele"];
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
            $invites = $inviteRepository->rechercheInvite($criteres, $dateDebut, $dateFin, $limit);
        }

        return $this->render('bilan/annuel/invite.html.twig', [
                    'form_recherche' => $searchcotisation->createView(),
                    'invites' => $invites,
        ]);
    }

    #[Route('/bilaninviten', name: 'app_bilan_inviten', methods: ['GET', 'POST'])]
    public function rechercheInviten(Request $request, InviteRepository $inviteRepository, DateTime $dateDebut = null, DateTime $dateFin = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $communaute = $this->getUser()->getEglise()->getCommunaute();
        $user = $this->getUser();

        $invites = [];

        $searchcotisation = $this->createForm(BilaninvitenType::class, $invites,);
        if ($searchcotisation->handleRequest($request)->isSubmitted()) {
            $criteria = $searchcotisation->getData();

            $criteres = array();

            $criteres["communaute"] = $communaute->getId();

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
            $invites = $inviteRepository->rechercheInviten($criteres, $dateDebut, $dateFin, $limit);
        }

        return $this->render('bilan/national/invite.html.twig', [
                    'form_recherche' => $searchcotisation->createView(),
                    'invites' => $invites,
        ]);
    }

    #[Route('/bilancene', name: 'app_bilan_cene', methods: ['GET', 'POST'])]
    public function rechercheCene(Request $request, SceneRepository $ceneRepository,
            DateTime $dateDebut = null, DateTime $dateFin = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $eglise = $this->getUser()->getEglise();

        $cenes = [];

//        $difference = $cotisationRepository->getSeanceByDates();
        $searchcotisation = $this->createForm(BilanceneType::class, $cenes);
        if ($searchcotisation->handleRequest($request)->isSubmitted()) {
            $criteria = $searchcotisation->getData();

            $criteres = array();

            $criteres["eglise"] = $eglise->getId();

            if ($criteria["dateDebut"]) {
                $dateDebut = ($criteria["dateDebut"]);
                //  $dateDebut = DateTime::createFromFormat('Y-m-d',$dateDebuts);
            }

            if ($criteria["dateFin"]) {
                $dateFin = $criteria["dateFin"];

                // $dateFin = DateTime::createFromFormat('Y-m-d',$dateFins);
            }


            $limit = 1000;
            $cenes = $ceneRepository->rechercheScene($criteres, $dateDebut, $dateFin, $limit);
        }

        return $this->render('bilan/annuel/cene.html.twig', [
                    'form_recherche' => $searchcotisation->createView(),
                    'cenes' => $cenes,
        ]);
    }

    #[Route('/bilancenen', name: 'app_bilan_cenen', methods: ['GET', 'POST'])]
    public function rechercheCenen(Request $request, SceneRepository $ceneRepository,
            DateTime $dateDebut = null, DateTime $dateFin = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        $communaute = $this->getUser()->getEglise()->getCommunaute();
        $user = $this->getUser();

        $cenes = [];

//        $difference = $cotisationRepository->getSeanceByDates();
        $searchcotisation = $this->createForm(BilancenenType::class, $cenes);
        if ($searchcotisation->handleRequest($request)->isSubmitted()) {
            $criteria = $searchcotisation->getData();

            $criteres = array();

            $criteres["communaute"] = $communaute->getId();
            if ($criteria["dateDebut"]) {
                $dateDebut = ($criteria["dateDebut"]);
                //  $dateDebut = DateTime::createFromFormat('Y-m-d',$dateDebuts);
            }

            if ($criteria["dateFin"]) {
                $dateFin = $criteria["dateFin"];

                // $dateFin = DateTime::createFromFormat('Y-m-d',$dateFins);
            }


            $limit = 1000;
            $cenes = $ceneRepository->rechercheScenen($criteres, $dateDebut, $dateFin, $limit);
        }

        return $this->render('bilan/national/cene.html.twig', [
                    'form_recherche' => $searchcotisation->createView(),
                    'cenes' => $cenes,
        ]);
    }

    #[Route('/bilandeces', name: 'app_bilan_deces', methods: ['GET', 'POST'])]
    public function rechercheDeces(Request $request, DecesRepository $decesRepository,
            DateTime $dateDebut = null, DateTime $dateFin = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $eglise = $this->getUser()->getEglise();

        $decess = [];

//        $difference = $cotisationRepository->getSeanceByDates();
        $searchcotisation = $this->createForm(BilandecesType::class, $decess);
        if ($searchcotisation->handleRequest($request)->isSubmitted()) {
            $criteria = $searchcotisation->getData();

            $criteres = array();

            $criteres["eglise"] = $eglise->getId();

            if ($criteria["dateDebut"]) {
                $dateDebut = ($criteria["dateDebut"]);
                //  $dateDebut = DateTime::createFromFormat('Y-m-d',$dateDebuts);
            }

            if ($criteria["dateFin"]) {
                $dateFin = $criteria["dateFin"];

                // $dateFin = DateTime::createFromFormat('Y-m-d',$dateFins);
            }


            $limit = 1000;
            $decess = $decesRepository->rechercheDeces($criteres, $dateDebut, $dateFin, $limit);
        }

        return $this->render('bilan/annuel/necrologie.html.twig', [
                    'form_recherche' => $searchcotisation->createView(),
                    'decess' => $decess,
        ]);
    }

    #[Route('/bilandecesn', name: 'app_bilan_decesn', methods: ['GET', 'POST'])]
    public function rechercheDecesn(Request $request, DecesRepository $decesRepository,
            DateTime $dateDebut = null, DateTime $dateFin = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $communaute = $this->getUser()->getEglise()->getCommunaute();
        $user = $this->getUser();

        $decess = [];

//        $difference = $cotisationRepository->getSeanceByDates();
        $searchcotisation = $this->createForm(BilandecesnType::class, $decess);
        if ($searchcotisation->handleRequest($request)->isSubmitted()) {
            $criteria = $searchcotisation->getData();

            $criteres = array();

            $criteres["communaute"] = $communaute->getId();

            if ($criteria["dateDebut"]) {
                $dateDebut = ($criteria["dateDebut"]);
                //  $dateDebut = DateTime::createFromFormat('Y-m-d',$dateDebuts);
            }

            if ($criteria["dateFin"]) {
                $dateFin = $criteria["dateFin"];

                // $dateFin = DateTime::createFromFormat('Y-m-d',$dateFins);
            }


            $limit = 1000;
            $decess = $decesRepository->rechercheDecesn($criteres, $dateDebut, $dateFin, $limit);
        }

        return $this->render('bilan/national/necrologie.html.twig', [
                    'form_recherche' => $searchcotisation->createView(),
                    'decess' => $decess,
        ]);
    }

    #[Route('/bilanrecommandation', name: 'app_bilan_recommandation', methods: ['GET', 'POST'])]
    public function rechercheRecommandation(Request $request, RecommandationRepository $recommandationRepository,
            DateTime $dateDebut = null, DateTime $dateFin = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $eglise = $this->getUser()->getEglise();

        $recommandations = [];

//        $difference = $cotisationRepository->getSeanceByDates();
        $searchcotisation = $this->createForm(BilanrecommandationType::class, $recommandations);
        if ($searchcotisation->handleRequest($request)->isSubmitted()) {
            $criteria = $searchcotisation->getData();

            $criteres = array();

            $criteres["eglise"] = $eglise->getId();

            if ($criteria["fidele"]) {
                $criteres["fidele"] = $criteria["fidele"];
            }

            if ($criteria["dateDebut"]) {
                $dateDebut = ($criteria["dateDebut"]);
            }

            if ($criteria["dateFin"]) {
                $dateFin = $criteria["dateFin"];
            }


            $limit = 1000;
            $recommandations = $recommandationRepository->rechercheRecommandation($criteres, $dateDebut, $dateFin, $limit);
        }

        return $this->render('bilan/annuel/recommandation.html.twig', [
                    'form_recherche' => $searchcotisation->createView(),
                    'recommandations' => $recommandations,
        ]);
    }

    #[Route('/bilanrecommandationn', name: 'app_bilan_recommandationn', methods: ['GET', 'POST'])]
    public function rechercheRecommandationn(Request $request, RecommandationRepository $recommandationRepository,
            DateTime $dateDebut = null, DateTime $dateFin = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $communaute = $this->getUser()->getEglise()->getCommunaute();
        $user = $this->getUser();

        $recommandations = [];

//        $difference = $cotisationRepository->getSeanceByDates();
        $searchcotisation = $this->createForm(BilanrecommandationnType::class, $recommandations);
        if ($searchcotisation->handleRequest($request)->isSubmitted()) {
            $criteria = $searchcotisation->getData();

            $criteres = array();

            $criteres["communaute"] = $communaute->getId();

            if ($criteria["dateDebut"]) {
                $dateDebut = ($criteria["dateDebut"]);
            }

            if ($criteria["dateFin"]) {
                $dateFin = $criteria["dateFin"];
            }


            $limit = 1000;
            $recommandations = $recommandationRepository->rechercheRecommandationn($criteres, $dateDebut, $dateFin, $limit);
        }

        return $this->render('bilan/national/recommandation.html.twig', [
                    'form_recherche' => $searchcotisation->createView(),
                    'recommandations' => $recommandations,
        ]);
    }

    #[Route('/bilandiscipline', name: 'app_bilan_discipline', methods: ['GET', 'POST'])]
    public function rechercheDiscipline(Request $request, DisciplineRepository $disciplineRepository,
            DateTime $dateDebut1 = null, DateTime $dateFin = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $eglise = $this->getUser()->getEglise();

        $disciplines = [];

//        $difference = $cotisationRepository->getSeanceByDates();
        $searchcotisation = $this->createForm(BilandisciplineType::class, $disciplines);
        if ($searchcotisation->handleRequest($request)->isSubmitted()) {
            $criteria = $searchcotisation->getData();

            $criteres = array();

            $criteres["eglise"] = $eglise->getId();

            if ($criteria["fidele"]) {
                $criteres["fidele"] = $criteria["fidele"];
            }

            if ($criteria["dateDebut1"]) {
                $dateDebut1 = ($criteria["dateDebut1"]);
                //  $dateDebut = DateTime::createFromFormat('Y-m-d',$dateDebuts);
            }

            if ($criteria["dateFin"]) {
                $dateFin = $criteria["dateFin"];

                // $dateFin = DateTime::createFromFormat('Y-m-d',$dateFins);
            }


            $limit = 1000;
            $disciplines = $disciplineRepository->rechercheDiscipline($criteres, $dateDebut1, $dateFin, $limit);
        }

        return $this->render('bilan/annuel/discipline.html.twig', [
                    'form_recherche' => $searchcotisation->createView(),
                    'disciplines' => $disciplines,
        ]);
    }

    #[Route('/bilandisciplinen', name: 'app_bilan_disciplinen', methods: ['GET', 'POST'])]
    public function rechercheDisciplinen(Request $request, DisciplineRepository $disciplineRepository,
            DateTime $dateDebut1 = null, DateTime $dateFin = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $communaute = $this->getUser()->getEglise()->getCommunaute();
        $user = $this->getUser();

        $disciplines = [];

//        $difference = $cotisationRepository->getSeanceByDates();
        $searchcotisation = $this->createForm(BilandisciplinenType::class, $disciplines);
        if ($searchcotisation->handleRequest($request)->isSubmitted()) {
            $criteria = $searchcotisation->getData();

            $criteres = array();

            $criteres["communaute"] = $communaute->getId();

            if ($criteria["dateDebut1"]) {
                $dateDebut1 = ($criteria["dateDebut1"]);
                //  $dateDebut = DateTime::createFromFormat('Y-m-d',$dateDebuts);
            }

            if ($criteria["dateFin"]) {
                $dateFin = $criteria["dateFin"];

                // $dateFin = DateTime::createFromFormat('Y-m-d',$dateFins);
            }


            $limit = 1000;
            $disciplines = $disciplineRepository->rechercheDisciplinen($criteres, $dateDebut1, $dateFin, $limit);
        }

        return $this->render('bilan/national/discipline.html.twig', [
                    'form_recherche' => $searchcotisation->createView(),
                    'disciplines' => $disciplines,
        ]);
    }

    #[Route('/bilanconge', name: 'app_bilan_conge', methods: ['GET', 'POST'])]
    public function rechercheConge(Request $request, CongeRepository $congeRepository,
            DateTime $dateDebut = null, DateTime $dateFin = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $eglise = $this->getUser()->getEglise();

        $conges = [];

//        $difference = $cotisationRepository->getSeanceByDates();
        $searchcotisation = $this->createForm(BilancongeType::class, $conges);
        if ($searchcotisation->handleRequest($request)->isSubmitted()) {
            $criteria = $searchcotisation->getData();

            $criteres = array();

            $criteres["eglise"] = $eglise->getId();

            if ($criteria["dateDebut"]) {
                $dateDebut = ($criteria["dateDebut"]);
                //  $dateDebut = DateTime::createFromFormat('Y-m-d',$dateDebuts);
            }

            if ($criteria["dateFin"]) {
                $dateFin = $criteria["dateFin"];

                // $dateFin = DateTime::createFromFormat('Y-m-d',$dateFins);
            }


            $limit = 1000;
            $conges = $congeRepository->rechercheConge($criteres, $dateDebut, $dateFin, $limit);
        }

        return $this->render('bilan/annuel/conge.html.twig', [
                    'form_recherche' => $searchcotisation->createView(),
                    'conges' => $conges,
        ]);
    }

    #[Route('/bilancongen', name: 'app_bilan_congen', methods: ['GET', 'POST'])]
    public function rechercheCongen(Request $request, CongeRepository $congeRepository,
            DateTime $dateDebut = null, DateTime $dateFin = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $eglise = $this->getUser()->getEglise();

        $conges = [];

//        $difference = $cotisationRepository->getSeanceByDates();
        $searchcotisation = $this->createForm(BilancongenType::class, $conges);
        if ($searchcotisation->handleRequest($request)->isSubmitted()) {
            $criteria = $searchcotisation->getData();

            $criteres = array();

            $criteres["eglise"] = $eglise->getId();

            if ($criteria["dateDebut"]) {
                $dateDebut = ($criteria["dateDebut"]);
                //  $dateDebut = DateTime::createFromFormat('Y-m-d',$dateDebuts);
            }

            if ($criteria["dateFin"]) {
                $dateFin = $criteria["dateFin"];

                // $dateFin = DateTime::createFromFormat('Y-m-d',$dateFins);
            }


            $limit = 1000;
            $conges = $congeRepository->rechercheCongen($criteres, $dateDebut, $dateFin, $limit);
        }

        return $this->render('bilan/national/conge.html.twig', [
                    'form_recherche' => $searchcotisation->createView(),
                    'conges' => $conges,
        ]);
    }

    #[Route('/bilannommination', name: 'app_bilan_nommination', methods: ['GET', 'POST'])]
    public function rechercheNommination(Request $request, NomminationRepository $nomminationRepository,
            DateTime $dateDebut = null, DateTime $dateFin = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $eglise = $this->getUser()->getEglise();

        $nomminations = [];

//        $difference = $cotisationRepository->getSeanceByDates();
        $searchcotisation = $this->createForm(BilannomminationType::class, $nomminations);
        if ($searchcotisation->handleRequest($request)->isSubmitted()) {
            $criteria = $searchcotisation->getData();

            $criteres = array();

            $criteres["eglise"] = $eglise->getId();

            if ($criteria["dateDebut"]) {
                $dateDebut = ($criteria["dateDebut"]);
                //  $dateDebut = DateTime::createFromFormat('Y-m-d',$dateDebuts);
            }

            if ($criteria["dateFin"]) {
                $dateFin = $criteria["dateFin"];

                // $dateFin = DateTime::createFromFormat('Y-m-d',$dateFins);
            }
            if ($criteria["fidele"]) {
                $criteres["fidele"] = $criteria["fidele"];
            }

            $limit = 1000;
            $nomminations = $nomminationRepository->rechercheNommination($criteres, $dateDebut, $dateFin, $limit);
        }

        return $this->render('bilan/annuel/nommination.html.twig', [
                    'form_recherche' => $searchcotisation->createView(),
                    'nomminations' => $nomminations,
        ]);
    }

    #[Route('/bilannomminationn', name: 'app_bilan_nomminationn', methods: ['GET', 'POST'])]
    public function rechercheNomminationn(Request $request, NomminationRepository $nomminationRepository,
            DateTime $dateDebut = null, DateTime $dateFin = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $communaute = $this->getUser()->getEglise()->getCommunaute();
        $user = $this->getUser();

        $nomminations = [];

//        $difference = $cotisationRepository->getSeanceByDates();
        $searchcotisation = $this->createForm(BilannomminationnType::class, $nomminations);
        if ($searchcotisation->handleRequest($request)->isSubmitted()) {
            $criteria = $searchcotisation->getData();

            $criteres = array();

            $criteres["communaute"] = $communaute->getId();

            if ($criteria["dateDebut"]) {
                $dateDebut = ($criteria["dateDebut"]);
                //  $dateDebut = DateTime::createFromFormat('Y-m-d',$dateDebuts);
            }

            if ($criteria["dateFin"]) {
                $dateFin = $criteria["dateFin"];

                // $dateFin = DateTime::createFromFormat('Y-m-d',$dateFins);
            }
        

            $limit = 1000;
            $nomminations = $nomminationRepository->rechercheNomminationn($criteres, $dateDebut, $dateFin, $limit);
        }

        return $this->render('bilan/national/nommination.html.twig', [
                    'form_recherche' => $searchcotisation->createView(),
                    'nomminations' => $nomminations,
        ]);
    }

    #[Route('/bilanbapteme', name: 'app_bilan_bapteme', methods: ['GET', 'POST'])]
    public function rechercheBapteme(Request $request, BaptemeRepository $baptemeRepository,
            DateTime $dateDebut = null, DateTime $dateFin = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $eglise = $this->getUser()->getEglise();

        $baptemes = [];

//        $difference = $cotisationRepository->getSeanceByDates();
        $searchcotisation = $this->createForm(BilanbaptemeType::class, $baptemes);
        if ($searchcotisation->handleRequest($request)->isSubmitted()) {
            $criteria = $searchcotisation->getData();

            $criteres = array();

            $criteres["eglise"] = $eglise->getId();

            if ($criteria["dateDebut"]) {
                $dateDebut = ($criteria["dateDebut"]);
                //  $dateDebut = DateTime::createFromFormat('Y-m-d',$dateDebuts);
            }

            if ($criteria["dateFin"]) {
                $dateFin = $criteria["dateFin"];

                // $dateFin = DateTime::createFromFormat('Y-m-d',$dateFins);
            }


            $limit = 1000;
            $baptemes = $baptemeRepository->rechercheBapteme($criteres, $dateDebut, $dateFin, $limit);
        }

        return $this->render('bilan/annuel/bapteme.html.twig', [
                    'form_recherche' => $searchcotisation->createView(),
                    'baptemes' => $baptemes,
        ]);
    }

    #[Route('/bilanbaptemen', name: 'app_bilan_baptemen', methods: ['GET', 'POST'])]
    public function rechercheBaptemen(Request $request, BaptemeRepository $baptemeRepository,
            DateTime $dateDebut = null, DateTime $dateFin = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $communaute = $this->getUser()->getEglise()->getCommunaute();
        $user = $this->getUser();

        $baptemes = [];

//        $difference = $cotisationRepository->getSeanceByDates();
        $searchcotisation = $this->createForm(BilanbaptemenType::class, $baptemes);
        if ($searchcotisation->handleRequest($request)->isSubmitted()) {
            $criteria = $searchcotisation->getData();

            $criteres = array();

            $criteres["communaute"] = $communaute->getId();

            if ($criteria["dateDebut"]) {
                $dateDebut = ($criteria["dateDebut"]);
                //  $dateDebut = DateTime::createFromFormat('Y-m-d',$dateDebuts);
            }

            if ($criteria["dateFin"]) {
                $dateFin = $criteria["dateFin"];

                // $dateFin = DateTime::createFromFormat('Y-m-d',$dateFins);
            }


            $limit = 1000;
            $baptemes = $baptemeRepository->rechercheBaptemen($criteres, $dateDebut, $dateFin, $limit);
        }

        return $this->render('bilan/national/bapteme.html.twig', [
                    'form_recherche' => $searchcotisation->createView(),
                    'baptemes' => $baptemes,
        ]);
    }

    #[Route('/bilanpastorale', name: 'app_bilan_pastorale', methods: ['GET', 'POST'])]
    public function recherchePastorale(Request $request, PastoraleRepository $pastoraleRepository,
            DateTime $dateDebut = null, DateTime $dateFin = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $eglise = $this->getUser()->getEglise();

        $pastorales = [];

//        $difference = $cotisationRepository->getSeanceByDates();
        $searchcotisation = $this->createForm(BilanpastoraleType::class, $pastorales);
        if ($searchcotisation->handleRequest($request)->isSubmitted()) {
            $criteria = $searchcotisation->getData();

            $criteres = array();

            $criteres["eglise"] = $eglise->getId();

            if ($criteria["dateDebut"]) {
                $dateDebut = ($criteria["dateDebut"]);
                //  $dateDebut = DateTime::createFromFormat('Y-m-d',$dateDebuts);
            }

            if ($criteria["dateFin"]) {
                $dateFin = $criteria["dateFin"];

                // $dateFin = DateTime::createFromFormat('Y-m-d',$dateFins);
            }


            $limit = 1000;
            $pastorales = $pastoraleRepository->recherchePastorale($criteres, $dateDebut, $dateFin, $limit);
        }

        return $this->render('bilan/annuel/pastorale.html.twig', [
                    'form_recherche' => $searchcotisation->createView(),
                    'pastorales' => $pastorales,
        ]);
    }

    #[Route('/bilanpastoralen', name: 'app_bilan_pastoralen', methods: ['GET', 'POST'])]
    public function recherchePastoralen(Request $request, PastoraleRepository $pastoraleRepository,
            DateTime $dateDebut = null, DateTime $dateFin = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $communaute = $this->getUser()->getEglise()->getCommunaute();
        $user = $this->getUser();

        $pastorales = [];

//        $difference = $cotisationRepository->getSeanceByDates();
        $searchcotisation = $this->createForm(BilanpastoralenType::class, $pastorales);
        if ($searchcotisation->handleRequest($request)->isSubmitted()) {
            $criteria = $searchcotisation->getData();

            $criteres = array();

            $criteres["communaute"] = $communaute->getId();

            if ($criteria["dateDebut"]) {
                $dateDebut = ($criteria["dateDebut"]);
                //  $dateDebut = DateTime::createFromFormat('Y-m-d',$dateDebuts);
            }

            if ($criteria["dateFin"]) {
                $dateFin = $criteria["dateFin"];

                // $dateFin = DateTime::createFromFormat('Y-m-d',$dateFins);
            }


            $limit = 1000;
            $pastorales = $pastoraleRepository->recherchePastoralen($criteres, $dateDebut, $dateFin, $limit);
        }

        return $this->render('bilan/national/pastorale.html.twig', [
                    'form_recherche' => $searchcotisation->createView(),
                    'pastorales' => $pastorales,
        ]);
    }

    #[Route('/bilansociale', name: 'app_bilan_sociale', methods: ['GET', 'POST'])]
    public function rechercheSociale(Request $request, ActivitesocialeRepository $activitesocialeRepository,
            DateTime $dateDebut = null, DateTime $dateFin = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $eglise = $this->getUser()->getEglise();

        $activitesociales = [];

//        $difference = $cotisationRepository->getSeanceByDates();
        $searchcotisation = $this->createForm(BilansocialeType::class, $activitesociales);
        if ($searchcotisation->handleRequest($request)->isSubmitted()) {
            $criteria = $searchcotisation->getData();

            $criteres = array();

            $criteres["eglise"] = $eglise->getId();

            if ($criteria["dateDebut"]) {
                $dateDebut = ($criteria["dateDebut"]);
                //  $dateDebut = DateTime::createFromFormat('Y-m-d',$dateDebuts);
            }

            if ($criteria["dateFin"]) {
                $dateFin = $criteria["dateFin"];

                // $dateFin = DateTime::createFromFormat('Y-m-d',$dateFins);
            }


            $limit = 1000;
            $activitesociales = $activitesocialeRepository->rechercheActivitesociale($criteres, $dateDebut, $dateFin, $limit);
        }

        return $this->render('bilan/annuel/sociale.html.twig', [
                    'form_recherche' => $searchcotisation->createView(),
                    'sociales' => $activitesociales,
        ]);
    }

    #[Route('/bilansocialen', name: 'app_bilan_socialen', methods: ['GET', 'POST'])]
    public function rechercheSocialen(Request $request, ActivitesocialeRepository $activitesocialeRepository,
            DateTime $dateDebut = null, DateTime $dateFin = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $communaute = $this->getUser()->getEglise()->getCommunaute();
        $user = $this->getUser();

        $activitesociales = [];

//        $difference = $cotisationRepository->getSeanceByDates();
        $searchcotisation = $this->createForm(BilansocialenType::class, $activitesociales);
        if ($searchcotisation->handleRequest($request)->isSubmitted()) {
            $criteria = $searchcotisation->getData();

            $criteres = array();

            $criteres["communaute"] = $communaute->getId();

            if ($criteria["dateDebut"]) {
                $dateDebut = ($criteria["dateDebut"]);
                //  $dateDebut = DateTime::createFromFormat('Y-m-d',$dateDebuts);
            }

            if ($criteria["dateFin"]) {
                $dateFin = $criteria["dateFin"];

                // $dateFin = DateTime::createFromFormat('Y-m-d',$dateFins);
            }


            $limit = 1000;
            $activitesociales = $activitesocialeRepository->rechercheActivitesocialen($criteres, $dateDebut, $dateFin, $limit);
        }

        return $this->render('bilan/national/sociale.html.twig', [
                    'form_recherche' => $searchcotisation->createView(),
                    'sociales' => $activitesociales,
        ]);
    }

    #[Route('/bilancultecodim', name: 'app_bilan_cultecodim', methods: ['GET', 'POST'])]
    public function rechercheCultecodim(Request $request, CultecodimRepository $cultecodimRepository, ClassecodimRepository $classecodimRepository,
            DateTime $dateDebut = null, DateTime $dateFin = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $eglise = $this->getUser()->getEglise();

        $cultecodims = [];
        $difference = $cultecodimRepository->findCultecodimsByDates();
        $classecodim = $classecodimRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
//        $difference = $cotisationRepository->getSeanceByDates();
        $searchcotisation = $this->createForm(BilanecodimType::class, $cultecodims, ['classecodim' => $classecodim]);
        if ($searchcotisation->handleRequest($request)->isSubmitted()) {
            $criteria = $searchcotisation->getData();

            $criteres = array();

            $criteres["eglise"] = $eglise->getId();

            if ($criteria["dateDebut"]) {
                $dateDebut = ($criteria["dateDebut"]);
                //  $dateDebut = DateTime::createFromFormat('Y-m-d',$dateDebuts);
            }

            if ($criteria["dateFin"]) {
                $dateFin = $criteria["dateFin"];

                // $dateFin = DateTime::createFromFormat('Y-m-d',$dateFins);
            }

            if ($criteria["classecodim"]) {
                $criteres["classecodim"] = $criteria["classecodim"];
            }
            $limit = 1000;
            $cultecodims = $cultecodimRepository->rechercheEcodim($criteres, $dateDebut, $dateFin, $limit);
        }

        return $this->render('bilan/annuel/bilanecodim.html.twig', [
                    'form_recherche' => $searchcotisation->createView(),
                    'cultecodims' => $cultecodims,
                    'differences' => $difference,
        ]);
    }

    #[Route('/bilancultecodimn', name: 'app_bilan_cultecodimn', methods: ['GET', 'POST'])]
    public function rechercheCultecodimn(Request $request, CultecodimRepository $cultecodimRepository, ClassecodimRepository $classecodimRepository,
            DateTime $dateDebut = null, DateTime $dateFin = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $communaute = $this->getUser()->getEglise()->getCommunaute();
        $user = $this->getUser();

        $cultecodims = [];
        $difference = $cultecodimRepository->findCultecodimsByDates();
        //$classecodim = $classecodimRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
//        $difference = $cotisationRepository->getSeanceByDates();
        $searchcotisation = $this->createForm(BilanculteecodimnType::class, $cultecodims);
        if ($searchcotisation->handleRequest($request)->isSubmitted()) {
            $criteria = $searchcotisation->getData();

            $criteres = array();

            $criteres["communaute"] = $communaute->getId();

            if ($criteria["dateDebut"]) {
                $dateDebut = ($criteria["dateDebut"]);
                //  $dateDebut = DateTime::createFromFormat('Y-m-d',$dateDebuts);
            }

            if ($criteria["dateFin"]) {
                $dateFin = $criteria["dateFin"];

                // $dateFin = DateTime::createFromFormat('Y-m-d',$dateFins);
            }


            $limit = 1000;
            $cultecodims = $cultecodimRepository->rechercheEcodimn($criteres, $dateDebut, $dateFin, $limit);
        }

        return $this->render('bilan/national/bilanecodim.html.twig', [
                    'form_recherche' => $searchcotisation->createView(),
                    'cultecodims' => $cultecodims,
                    'differences' => $difference,
        ]);
    }

    #[Route('/bilanvisite2', name: 'app_bilan_visite2', methods: ['GET', 'POST'])]
    public function rechercheVisite2(Request $request, Visite2Repository $visite2Repository, FideleRepository $fideleRepository,
            DateTime $dateDebut = null, DateTime $dateFin = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        $eglise = $this->getUser()->getEglise();
        $visite2s = [];
        $fidele = $fideleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);

        $searchcotisation = $this->createForm(Bilanvisite2Type::class, $visite2s, ['fidele' => $fidele]);
        if ($searchcotisation->handleRequest($request)->isSubmitted()) {
            $criteria = $searchcotisation->getData();

            $criteres = array();

            $criteres["eglise"] = $eglise->getId();

            if ($criteria["dateDebut"]) {
                $dateDebut = ($criteria["dateDebut"]);
                //  $dateDebut = DateTime::createFromFormat('Y-m-d',$dateDebuts);
            }


            if ($criteria["fidele"]) {
                $criteres["fidele"] = $criteria["fidele"];
            }

            if ($criteria["dateFin"]) {
                $dateFin = ($criteria["dateFin"]);
                //  $dateDebut = DateTime::createFromFormat('Y-m-d',$dateDebuts);
            }



            $limit = 1000;
            $visite2s = $visite2Repository->rechercheVisite2($criteres, $dateDebut, $dateFin, $limit);
        }

        return $this->render('bilan/annuel/visite2.html.twig', [
                    'form_recherche' => $searchcotisation->createView(),
                    'visite2s' => $visite2s,
        ]);
    }

    #[Route('/bilanvisite2n', name: 'app_bilan_visite2n', methods: ['GET', 'POST'])]
    public function rechercheVisite2n(Request $request, Visite2Repository $visite2Repository, FideleRepository $fideleRepository,
            DateTime $dateDebut = null, DateTime $dateFin = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $communaute = $this->getUser()->getEglise()->getCommunaute();
        $user = $this->getUser();
        $eglise = $this->getUser()->getEglise();
        $visite2s = [];

        $searchcotisation = $this->createForm(Bilanvisite2nType::class, $visite2s);
        if ($searchcotisation->handleRequest($request)->isSubmitted()) {
            $criteria = $searchcotisation->getData();

            $criteres = array();

            $criteres["communaute"] = $communaute->getId();

            if ($criteria["dateDebut"]) {
                $dateDebut = ($criteria["dateDebut"]);
                //  $dateDebut = DateTime::createFromFormat('Y-m-d',$dateDebuts);
            }



            if ($criteria["dateFin"]) {
                $dateFin = ($criteria["dateFin"]);
                //  $dateDebut = DateTime::createFromFormat('Y-m-d',$dateDebuts);
            }



            $limit = 1000;
            $visite2s = $visite2Repository->rechercheVisite2n($criteres, $dateDebut, $dateFin, $limit);
        }

        return $this->render('bilan/national/visite2.html.twig', [
                    'form_recherche' => $searchcotisation->createView(),
                    'visite2s' => $visite2s,
        ]);
    }

    #[Route('/bilanglobal', name: 'app_bilan_global', methods: ['GET', 'POST'])]
    public function rechercheGlobal(Request $request, ActivitesocialeRepository $activitesocialeRepository,
            NaissanceRepository $naissanceRepository, EvangelisationRepository $evangelisationRepository,
            VisiteRepository $visiteRepository,
            PatrimoineRepository $patrimoineRepository, CulteRepository $culteRepository, ProgrammeRepository $programmeRepository,
            InviteRepository $inviteRepository,
            SceneRepository $ceneRepository, DecesRepository $decesRepository, RecommandationRepository $recommandationRepository,
            DisciplineRepository $disciplineRepository,
            CongeRepository $congeRepository,
            NomminationRepository $nomminationRepository,
            BaptemeRepository $baptemeRepository, PastoraleRepository $pastoraleRepository, Visite2Repository $visite2Repository,
            CultecodimRepository $cultecodimRepository, ClassecodimRepository $classecodimRepository,
            DateTime $dateDebut = null, DateTime $dateFin = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $eglise = $this->getUser()->getEglise();

        $activitesociales = [];
        $naissances = [];
        $visites = [];
        $patrimoines = [];
        $cultes = [];
        $programmes = [];
        $invites = [];
        $cenes = [];
        $decess = [];
        $recommandations = [];
        $disciplines = [];
        $conges = [];
        $nomminations = [];
        $baptemes = [];
        $pastorales = [];
        $visite2s = [];
        $evangelisations = [];
        $cultecodims = [];

//        $difference = $cotisationRepository->getSeanceByDates();
        $searchcotisation = $this->createForm(BilanglobalType::class, $activitesociales, $naissances, $visites,
                $patrimoines, $cultes, $programmes, $invites, $cenes, $decess, $recommandations, $disciplines,
                $conges, $nomminations, $baptemes, $pastorales, $evangelisations, $visite2s, $cultecodims);
        if ($searchcotisation->handleRequest($request)->isSubmitted()) {
            $criteria = $searchcotisation->getData();

            $criteres = array();

            $criteres["eglise"] = $eglise->getId();

            if ($criteria["dateDebut"]) {
                $dateDebut = ($criteria["dateDebut"]);
            }

            if ($criteria["dateFin"]) {
                $dateFin = $criteria["dateFin"];
                // $dateFin = DateTime::createFromFormat('Y-m-d',$dateFins);
            }

            $limit = 1000;
            $evangelisations = $evangelisationRepository->rechercheEvangelisation($criteres, $dateDebut, $dateFin, $limit);
            $naissances = $naissanceRepository->rechercheNaissance($criteres, $dateDebut, $dateFin, $limit);
            $visites = $visiteRepository->rechercheVisite($criteres, $dateDebut, $dateFin, $limit);
            $patrimoines = $patrimoineRepository->recherchePatrimoine($criteres, $dateDebut, $dateFin, $limit);
            $cultes = $culteRepository->rechercheCulte($criteres, $dateDebut, $dateFin, $limit);
            $programmes = $programmeRepository->rechercheProgramme($criteres, $dateDebut, $dateFin, $limit);
            $invites = $inviteRepository->rechercheInvite($criteres, $dateDebut, $dateFin, $limit);
            $cenes = $ceneRepository->rechercheScene($criteres, $dateDebut, $dateFin, $limit);
            $decess = $decesRepository->rechercheDeces($criteres, $dateDebut, $dateFin, $limit);
            $recommandations = $recommandationRepository->rechercheRecommandation($criteres, $dateDebut, $dateFin, $limit);
            $disciplines = $disciplineRepository->rechercheDiscipline($criteres, $dateDebut, $dateFin, $limit);
            $conges = $congeRepository->rechercheConge($criteres, $dateDebut, $dateFin, $limit);
            $nomminations = $nomminationRepository->rechercheNommination($criteres, $dateDebut, $dateFin, $limit);
            $baptemes = $baptemeRepository->rechercheBapteme($criteres, $dateDebut, $dateFin, $limit);
            $pastorales = $pastoraleRepository->recherchePastorale($criteres, $dateDebut, $dateFin, $limit);
            $activitesociales = $activitesocialeRepository->rechercheActivitesociale($criteres, $dateDebut, $dateFin, $limit);
            $visite2s = $visite2Repository->rechercheVisite2($criteres, $dateDebut, $dateFin, $limit);
            $cultecodims = $cultecodimRepository->rechercheEcodim($criteres, $dateDebut, $dateFin, $limit);
        }

        return $this->render('bilan/annuel/global.html.twig', [
                    'form_recherche' => $searchcotisation->createView(),
                    'sociales' => $activitesociales,
                    'pastorales' => $pastorales,
                    'baptemes' => $baptemes,
                    'nomminations' => $nomminations,
                    'conges' => $conges,
                    'disciplines' => $disciplines,
                    'recommandations' => $recommandations,
                    'decess' => $decess,
                    'cenes' => $cenes,
                    'invites' => $invites,
                    'programmes' => $programmes,
                    'cultes' => $cultes,
                    'patrimoines' => $patrimoines,
                    'visites' => $visites,
                    'naissances' => $naissances,
                    'evangelisations' => $evangelisations,
                    'visite2s' => $visite2s,
                    'cultecodims' => $cultecodims,
        ]);
    }

    #[Route('/bilanglobalactivite', name: 'app_bilan_activite', methods: ['GET', 'POST'])]
    public function rechercheActivite(Request $request,
            SeancecelluleRepository $celluleRepository,
            SeancegroupeRepository $groupeRepository,
            SeancedepartementRepository $departementRepository, SeancefamilleRepository $familleRepository,
            SeancezoneRepository $zoneRepository,
            DateTime $dateDebut = null, DateTime $dateFin = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $eglise = $this->getUser()->getEglise();

        $cellules = [];
        $groupes = [];
        $departements = [];
        $familles = [];
        $zones = [];
        $differencec = $celluleRepository->getSeanceByDates();
        $differenceg = $groupeRepository->getSeanceByDates();
        $differenced = $departementRepository->getSeanceByDates();
        $differencef = $familleRepository->getSeanceByDates();
        $differencez = $zoneRepository->getSeanceByDates();
        $searchcotisation = $this->createForm(BilanglobalType::class, $cellules, $groupes,
                $departements, $familles, $zones);
        if ($searchcotisation->handleRequest($request)->isSubmitted()) {
            $criteria = $searchcotisation->getData();

            $criteres = array();

            $criteres["eglise"] = $eglise->getId();

            if ($criteria["dateDebut"]) {
                $dateDebut = ($criteria["dateDebut"]);
            }

            if ($criteria["dateFin"]) {
                $dateFin = $criteria["dateFin"];
                // $dateFin = DateTime::createFromFormat('Y-m-d',$dateFins);
            }

            $limit = 1000;
            $cellules = $celluleRepository->rechercheCellule($criteres, $dateDebut, $dateFin, $limit);
            $groupes = $groupeRepository->rechercheGroupe($criteres, $dateDebut, $dateFin, $limit);
            $departements = $departementRepository->rechercheDepartement($criteres, $dateDebut, $dateFin, $limit);
            $familles = $familleRepository->rechercheFamille($criteres, $dateDebut, $dateFin, $limit);
            $zones = $zoneRepository->rechercheZone($criteres, $dateDebut, $dateFin, $limit);
        }

        return $this->render('bilan/activite/activite.html.twig', [
                    'form_recherche' => $searchcotisation->createView(),
                    'seancezones' => $zones,
                    'seancefamilles' => $familles,
                    'seancedepartements' => $departements,
                    'seancegroupes' => $groupes,
                    'seancecellules' => $cellules,
                    'differencecs' => $differencec,
                    'differenceds' => $differenced,
                    'differencefs' => $differencef,
                    'differencezs' => $differencez,
                    'differencegs' => $differenceg,
        ]);
    }

}
