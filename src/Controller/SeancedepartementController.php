<?php

namespace App\Controller;
 

use App\Entity\Departement;
use App\Entity\Fidele;
use App\Entity\Presencedepartement;
use App\Entity\Seancedepartement;
use App\Entity\Soldedepartement;
use App\Form\SeancedepartementType;
use App\Repository\DepartementRepository;
use App\Repository\FideleRepository;
use App\Repository\PresencedepartementRepository;
use App\Repository\SeancedepartementRepository;
use App\Repository\SoldedepartementRepository;
use App\Service\FileUploader;
use App\Traits\ClientIp;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/seancedepartement')]
class SeancedepartementController extends AbstractController {
use ClientIp;
    
    #[Route('/', name: 'seancedepartement_index', methods: ['GET'])]
    public function index(SeancedepartementRepository $seancedepartementRepository, Request $request): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_DEPARTEMENT')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $seancedepartement = $seancedepartementRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $difference = $seancedepartementRepository->getSeanceByDates();
        return $this->render('seancedepartement/index.html.twig', [
                    'seancedepartements' => $seancedepartement,
                    'differences' => $difference,
        ]);
    } 
    
    #[Route('/{id}/edit', name: 'seancedepartement_edit', methods: ['GET', 'POST'])]
    #[Route('/new', name: 'seancedepartement_new', methods: ['GET', 'POST'])]
    public function new(EntityManagerInterface $entityManager,Request $request, FileUploader $fileUploader, DepartementRepository $departementRepository, SoldedepartementRepository $soldeRepo, FideleRepository $fideleRepository, ?Seancedepartement $seancedepartement = null): Response {
        if (!$this->isGranted('ROLE_RESPONSABLE_DEPARTEMENT')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
                $type = $seancedepartement === null ? 'new' : 'edit';
                $seancedepartement = $seancedepartement === null ? new Seancedepartement() : $seancedepartement;
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException();
        }

        $eglise = $user->getEglise();

        if (!$eglise) {
            $this->addFlash('warning', 'Aucune église associée à votre compte.');
            
            return $this->redirectToRoute('seancedepartement_index');
        } 

        // Récupérer le département dirigé par l'utilisateur
        $departement = $departementRepository->findOneByUser($user);

        if (!$departement) {
            $this->addFlash('warning', 'Vous ne disposez pas de direction à gérer.');

            return $this->redirectToRoute('seancedepartement_index');
        }

        // Récupérer les fidèles du département
        $fidele = $fideleRepository->findFidelesByDepartement($departement->getId());

        // Création du formulaire
        $form = $this->createForm(
            SeancedepartementType::class,
            $seancedepartement,
            [
                'fidele' => $fidele,
              
            ]
        );

        $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {

                // $ideglise = $this->getUser()->getEglise()->getId();
                //  $seancedepartement->setIdeglise($ideglise);

                    $seancedepartement->setDepartement($departement);

                            //            Insertion rapport
                    $brochureFile = $form->get('photo')->getData();
                    if ($brochureFile) {
                        $brochureFileName = $fileUploader->upload($brochureFile);
                        $seancedepartement->setPhoto($brochureFileName);
                    }

                    if ($type === 'new') {
                        $seancedepartement->setCreatedFromIp($this->GetIp()) // remplacement de la function par le trait
                                ->setEglise($user->getEglise())
                                ->setCreatedBy($user)
                        ;
                                    $offrande = $form['offrande']->getData();

                        $departement2 = $departementRepository->findOneDepartement($departement);
                        $dql = $soldeRepo->findBy(['departement' => $departement]);
                        if ($dql) {
                            $id = $dql[0]->getId();
                            $activite = $soldeRepo->findOneBySoldeDepartement($id);
                            $mont = $activite->getMontant();
                            $j = 0;
                            $j = $mont + $offrande;
                            $activite->setMontant($j);
                        } else {

                            $montant = new Soldedepartement();
                            $montant->setMontant($offrande);
                            $montant->setDepartement($departement2);
                            $entityManager->persist($montant);
                        }
                    } else {
                        $seancedepartement->setUpdatedFromIp($this->GetIp()) // remplacement de la function par le trait
                                ->setUpdatedBy($user)
                        ;
                    }

                    //La date de la séance ne peut pas être superieure à la date du jour
                    $naiss = $form['datesuper']->getData();

                    $aujourdhui = new DateTime("now");

                    if ($aujourdhui < $naiss) {
                        $this->addFlash('warning', 'Date éronnée.');
                        return $this->redirect('new');
                    }


                    //Heure de debut ne pas être superieure à heure de fin
                    $debut = $form['heuredebut']->getData();
                    $fin = $form['heurefin']->getData();

                    if ($fin < $debut) {
                        $this->addFlash('warning', 'Heure debut ne pas être superieure à Heure fin.');
                        return $this->redirect('new');
                    }

                    $seancedepartement->setDepartement($departement);
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($seancedepartement);
                    $entityManager->flush();
                    $nextAction = $form->get('saveAndAdd')->isClicked() ? 'seancedepartement_new' : 'seancedepartement_index';
                    if ($nextAction) {
                        $this->addFlash('success', 'Enregistrement avec succès.');
                    }

                    return $this->redirectToRoute($nextAction);
        //            return $this->redirectToRoute('seancedepartement_index', [], Response::HTTP_SEE_OTHER);
                }
                $response = new Response(null, $form->isSubmitted() ? 422 : 200);
                return $this->render('seancedepartement/new.html.twig', [
                    'seancedepartement' => $seancedepartement,
                    'departement' => $departement,
                    'form' => $form->createView(),
                            'response' => $response,
                                ], $response);
            }

    
        /**
     * @Route("/search/invitedepartements/{id}", name="seancedepartement_search_invitedepartements", requirements={"id"="\d+"}, methods={"POST"})
     *
     * @return Response
     */
    public function seancedepartementSearchEnfants(SerializerInterface $serializer, Seancedepartement $seancedepartement): Response {
        if (!$this->isGranted('ROLE_RESPONSABLE_DEPARTEMENT')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($seancedepartement) {
            $invitedepartements = (array) json_decode($serializer->serialize($seancedepartement->getInvitedepartements()->toArray(), 'json', ['groups' => ['public']]));
        } else {
            $invitedepartements = [];
        }

        return new Response($this->renderView('seancedepartement/listefidele.html.twig', [
                    'invitedepartements' => $invitedepartements
        ]));
    }

    
        /**
         * Liste des présents pour une séance
         */
        #[Route('/presents', name: 'seancedepartement_presents', methods: ['POST'])]
        public function getPresents(Request $request, FideleRepository $fideleRepository, SeancedepartementRepository $seancedepartementRepository): Response
        {
            $seanceId = $request->request->get('seance_id');
            $departementId = $request->request->get('departement_id');
            
            // Récupérer la séance
            $seance = $seancedepartementRepository->find($seanceId);
            
            // Récupérer les présents (les Presencedepartement pour cette séance)
            $presents = [];
            if ($seance) {
                foreach ($seance->getPresencedepartements() as $presence) {
                    $fidele = $presence->getFidele();
                    if ($fidele) {
                        $presents[] = [
                            'id' => $fidele->getId(),
                            'nom' => $fidele->getNomfidele(),
                            'contact' => $fidele->getContact1()
                        ];
                    }
                }
            }
            
            return $this->render('seancedepartement/_presents_modal.html.twig', [
                'presents' => $presents,
                'seance' => $seance,
                'total' => count($presents)
            ]);
        }

        /**
         * Liste des absents pour une séance
         */
         #[Route('/absents', name: 'seancedepartement_absents', methods: ['POST'])]
            public function getAbsents(
                Request $request, 
                FideleRepository $fideleRepository, 
                SeancedepartementRepository $seancedepartementRepository
            ): Response {
                $seanceId = $request->request->get('seance_id');
                $departementId = $request->request->get('departement_id');
                
                // Récupérer la séance
                $seance = $seancedepartementRepository->find($seanceId);
                
                if (!$seance) {
                    return $this->render('seancedepartement/_absents_modal.html.twig', [
                        'error' => 'Séance non trouvée',
                        'absents' => [],
                        'total' => 0,
                        'totalMembres' => 0
                    ]);
                }
                
                // Récupérer les IDs des présents (Presencedepartement)
                $presentIds = [];
                if ($seance) {
                    foreach ($seance->getPresencedepartements() as $presence) {
                        $fidele = $presence->getFidele();
                        if ($fidele) {
                            $presentIds[] = $fidele->getId();
                        }
                    }
                }
                
                // Récupérer tous les membres du département (en utilisant votre méthode existante)
                $membresDepartement = $fideleRepository->findFidelesByDepartement($departementId);
                
                // Filtrer les absents (membres qui ne sont pas dans la liste des présents)
                $absents = [];
                foreach ($membresDepartement as $membre) {
                    if (!in_array($membre->getId(), $presentIds)) {
                        $absents[] = [
                            'id' => $membre->getId(),
                            'nom' => $membre->getNomfidele(),
                            'contact' => $membre->getContact1() ?? $membre->getContactwhatssap()
                        ];
                    }
                }
                
                // Statistiques supplémentaires
                $totalMembres = count($membresDepartement);
                $totalPresents = count($presentIds);
                $tauxPresence = $totalMembres > 0 ? round(($totalPresents / $totalMembres) * 100, 2) : 0;
                $tauxAbsence = $totalMembres > 0 ? round((count($absents) / $totalMembres) * 100, 2) : 0;
                
                return $this->render('seancedepartement/_absents_modal.html.twig', [
                    'absents' => $absents,
                    'seance' => $seance,
                    'total' => count($absents),
                    'totalMembres' => $totalMembres,
                    'totalPresents' => $totalPresents,
                    'tauxPresence' => $tauxPresence,
                    'tauxAbsence' => $tauxAbsence
                ]);
            }

        //Fin liste
            #[Route('/listeparticipantdepartement', name: 'seancedepartement_listeparticipant', methods: ['GET'])]
        public function indexpresence(
            PresencedepartementRepository $presenceRepository,
            FideleRepository $fideleRepository,
            DepartementRepository $departementRepository,
            Request $request
        ): Response {
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
            
            $user = $this->getUser();
            $eglise = $user->getEglise();
            
            // Construction de la requête selon le rôle
            $qb = $presenceRepository->createQueryBuilder('p')
                ->leftJoin('p.seancedepartement', 's')
                ->leftJoin('p.departement', 'd')
                ->where('p.eglise = :eglise')
                ->andWhere('p.deletedAt IS NULL')
                ->setParameter('eglise', $eglise);
            
            // Filtres selon les rôles
            if ($this->isGranted('ROLE_ADMIN') || $this->isGranted('ROLE_PASTEUR') || $this->isGranted('ROLE_SECRETAIRE')) {
                // Ces rôles voient toutes les présences
            } 
            elseif ($this->isGranted('ROLE_RESPONSABLE_DEPARTEMENT')) {
                $departement = $user->getDepartement();
                if ($departement) {
                    $qb->andWhere('d.id = :departementId')
                    ->setParameter('departementId', $departement->getId());
                }
            }
            else {
                $this->addFlash('error', 'Vous n\'avez pas les droits pour voir les présences.');
                return $this->redirectToRoute('dashboard');
            }
            
            $presences = $qb->orderBy('s.datesuper', 'DESC')->getQuery()->getResult();
            
            // Récupérer les départements accessibles
            $departements = $this->getDepartementsAccessibles($user, $eglise, $departementRepository);
            
            // Compter les membres par département
            $membresParDepartement = [];
            foreach ($departements as $departement) {
                $membresParDepartement[$departement->getId()] = [
                    'departement' => $departement,
                    'nb_membres' => count($fideleRepository->findFidelesByDepartement($departement->getId())),
                    'nb_presences' => 0
                ];
            }
            
            // Grouper par date et par département
            $presencesParDateEtDepartement = [];
            $totalGeneral = 0;
            
            foreach ($presences as $presence) {
                if ($presence->getSeancedepartement() && $presence->getSeancedepartement()->getDatesuper()) {
                    $dateKey = $presence->getSeancedepartement()->getDatesuper()->format('Y-m-d');
                    $departementId = $presence->getDepartement()->getId();
                    $departementNom = $presence->getDepartement()->getNom();
                    
                    if (!isset($presencesParDateEtDepartement[$dateKey])) {
                        $presencesParDateEtDepartement[$dateKey] = [
                            'date' => $presence->getSeancedepartement()->getDatesuper(),
                            'departements' => [],
                            'total_jour' => 0,
                            'total_membres_jour' => 0
                        ];
                    }
                    if (!isset($presencesParDateEtDepartement[$dateKey]['departements'][$departementId])) {
                        $nbMembres = $membresParDepartement[$departementId]['nb_membres'] ?? 0;
                        $presencesParDateEtDepartement[$dateKey]['departements'][$departementId] = [
                            'departement_id' => $departementId,
                            'departement_nom' => $departementNom,
                            'presences' => [],
                            'total_presences' => 0,
                            'nb_membres' => $nbMembres,
                            'taux_presence' => 0
                        ];
                        $presencesParDateEtDepartement[$dateKey]['total_membres_jour'] += $nbMembres;
                    }
                    $presencesParDateEtDepartement[$dateKey]['departements'][$departementId]['presences'][] = $presence;
                    $presencesParDateEtDepartement[$dateKey]['departements'][$departementId]['total_presences']++;
                    $presencesParDateEtDepartement[$dateKey]['total_jour']++;
                    $totalGeneral++;
                }
            }
            
            // Calculer les taux
            foreach ($presencesParDateEtDepartement as $dateKey => &$dateData) {
                foreach ($dateData['departements'] as &$departementData) {
                    $departementData['taux_presence'] = $departementData['nb_membres'] > 0 
                        ? round(($departementData['total_presences'] / $departementData['nb_membres']) * 100, 2) 
                        : 0;
                }
                $dateData['taux_global_jour'] = $dateData['total_membres_jour'] > 0 
                    ? round(($dateData['total_jour'] / $dateData['total_membres_jour']) * 100, 2) 
                    : 0;
            }
            
            $totalMembresGeneral = array_sum(array_column($membresParDepartement, 'nb_membres'));
            $tauxGeneral = $totalMembresGeneral > 0 ? round(($totalGeneral / $totalMembresGeneral) * 100, 2) : 0;
            
            return $this->render('seancedepartement/listeparticipant.html.twig', [
                'presencesParDateEtDepartement' => $presencesParDateEtDepartement,
                'totalGeneral' => $totalGeneral,
                'totalMembresGeneral' => $totalMembresGeneral,
                'tauxGeneral' => $tauxGeneral,
            ]);
        }

        private function getDepartementsAccessibles($user, $eglise, DepartementRepository $departementRepository): array
        {
            if ($this->isGranted('ROLE_ADMIN') || $this->isGranted('ROLE_PASTEUR') || $this->isGranted('ROLE_SECRETAIRE')) {
                return $departementRepository->findBy(['eglise' => $eglise, 'deletedAt' => NULL]);
            } 
            elseif ($this->isGranted('ROLE_RESPONSABLE_DEPARTEMENT')) {
                $departement = $user->getDepartement();
                if ($departement) {
                    return [$departement];
                }
            }
            return [];
        }
 


        #[Route('/presencedepartement', name: 'seancedepartement_presence', methods: ['POST', 'GET'])]
        public function presenceDepartement(
            FideleRepository $fideleRepository, 
            Request $request, 
            DepartementRepository $departementRepo, 
            SeancedepartementRepository $seancedepartementRepository,
            PresencedepartementRepository $presencedepartementRepository
        ): Response {
            
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
            if (!$this->isGranted('ROLE_RESPONSABLE_DEPARTEMENT')) {
                throw $this->createAccessDeniedException('Accès refusé, vous n\'avez pas les droits d\'accès ici!');
            }
            
            $eglise = $this->getUser()->getEglise();
            $user = $this->getUser();
            
            // Récupérer le département de l'utilisateur
            $departement = $departementRepo->findOneByUser($user);
            
            if (!$departement) {
                $this->addFlash('warning', 'Vous ne disposez pas de département à gérer.');
                return $this->redirectToRoute('seancedepartement_listeparticipant');
            }
            
            if ($request->isMethod('POST')) {
                // Récupérer les données du formulaire
                $seancedepartementId = $request->request->get('seancedepartement');
                $tabpost = $request->request->get('tab');
                
                if (empty($tabpost)) {
                    $this->addFlash('warning', 'Veuillez sélectionner au moins un fidèle.');
                    return $this->redirectToRoute('seancedepartement_presence');
                }
                
                // Récupérer la séance
                $seancedepartement = $seancedepartementRepository->find($seancedepartementId);
                
                if (!$seancedepartement) {
                    $this->addFlash('error', 'Séance non trouvée.');
                    return $this->redirectToRoute('seancedepartement_presence');
                }
                
                $em = $this->getDoctrine()->getManager();
                $existingFideles = [];
                $savedFideles = [];
                $duplicateFideles = [];
                
                foreach ($tabpost as $value) {
                    $idfidele = $fideleRepository->find($value);
                    
                    // Vérifier si ce fidèle existe déjà pour cette séance
                    $existingPresence = $presencedepartementRepository->findOneBy([
                        'fidele' => $idfidele,
                        'seancedepartement' => $seancedepartement,
                        'deletedAt' => NULL
                    ]);
                    
                    if ($existingPresence) {
                        // Mémoriser les doublons
                        $duplicateFideles[] = $idfidele->getNomfidele() . ' ' . $idfidele->getContact1();
                        $existingFideles[] = $idfidele->getId();
                    } else {
                        // Créer une nouvelle présence
                        $presencedepartement = new Presencedepartement();
                        $presencedepartement->setFidele($idfidele);
                        $presencedepartement->setDepartement($departement);
                        $presencedepartement->setSeancedepartement($seancedepartement);
                        $presencedepartement->setEglise($eglise);
                        $presencedepartement->setCreatedBy($this->getUser());
                    //  $presencedepartement->setCreatedAt(new \DateTime());
                        $presencedepartement->setCreatedFromIp($this->getIp());
                        
                        $em->persist($presencedepartement);
                        $savedFideles[] = $idfidele->getNomfidele() . ' ' . $idfidele->getContact1();
                    }
                }
                
                // Exécuter toutes les insertions
                if (!empty($savedFideles)) {
                    $em->flush();
                    $this->addFlash('success', sprintf(
                        '%d fidèle(s) enregistré(s) avec succès : %s',
                        count($savedFideles),
                        implode(', ', $savedFideles)
                    ));
                }
                
                // Afficher les doublons
                if (!empty($duplicateFideles)) {
                    $this->addFlash('warning', sprintf(
                        'Les fidèles suivants étaient déjà présents et n\'ont pas été ajoutés : %s',
                        implode(', ', $duplicateFideles)
                    ));
                }
                
                return $this->redirectToRoute('seancedepartement_listeparticipant');
                
            } else {
                // Méthode GET - Afficher le formulaire
                $idDepart = $departement->getId();
                $fideles = $fideleRepository->findFidelesByDepartement($idDepart);
                $seancedepartements = $seancedepartementRepository->findBy([
                    'eglise' => $eglise, 
                    'departement' => $departement, 
                    "deletedAt" => NULL
                ]);
                
                return $this->render('seancedepartement/presence.html.twig', [
                    'fideles' => $fideles,
                    'departement' => $departement,
                    'seancedepartements' => $seancedepartements
                ]);
            }
        }


#[Route('/get-presences-by-seance-departement', name: 'seancedepartement_get_presences', methods: ['POST'])]
public function getPresencesBySeanceDepartement(Request $request, PresencedepartementRepository $presencedepartementRepository, SeancedepartementRepository $seancedepartementRepository): JsonResponse
{
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    
    if (!$this->isGranted('ROLE_RESPONSABLE_DEPARTEMENT')) {
        return $this->json(['error' => 'Accès refusé'], 403);
    }
    
    $seanceId = $request->request->get('seanceId');
    
    if (!$seanceId) {
        return $this->json(['error' => 'Séance non spécifiée'], 400);
    }
    
    $seance = $seancedepartementRepository->find($seanceId);
    
    if (!$seance) {
        return $this->json(['error' => 'Séance non trouvée'], 404);
    }
    
    $presences = $presencedepartementRepository->findBy(['seancedepartement' => $seance]);
    
    $presencesIds = [];
    foreach ($presences as $presence) {
        $presencesIds[] = $presence->getFidele()->getId();
    }
    
    return $this->json([
        'success' => true,
        'presences' => $presencesIds,
        'seanceId' => $seanceId,
        'seanceDate' => $seance->getDatesuper()->format('d-m-Y'),
        'seanceTheme' => $seance->getTheme()
    ]);
}

    #[Route('/{id}/show', name: 'seancedepartement_show', methods: ['GET'])]
    public function show(Seancedepartement $seancedepartement): Response {
                $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        return $this->render('seancedepartement/show.html.twig', [
                    'seancedepartement' => $seancedepartement,
        ]);
    }

    #[Route('/{id}', name: 'seancedepartement_delete', methods: ['POST'])]
    public function delete(Request $request, Seancedepartement $seancedepartement): Response {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->denyAccessUnlessGranted('seancedepartement_delete', $seancedepartement);

        if ($this->isCsrfTokenValid('delete' . $seancedepartement->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();

  
            $seancedepartement->setDeletedFromIp($this->GetIp());
            $seancedepartement->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $seancedepartement->setDeletedBy($user);
             $this->addFlash('danger', 'Suppression effectuée avec succès.');
            $entityManager->flush();
        }

        return $this->redirectToRoute('seancedepartement_index');
    }

    #[Route('{id}/presencedepartement', name: 'presencedepartement_delete', methods: ['POST'])]
    public function deletePresence(Request $request, Presencedepartement $presencedepartement, PresencedepartementRepository $presencedepartementRepository): Response {
               $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if ($this->isCsrfTokenValid('delete' . $presencedepartement->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();

            $presencedepartement->setDeletedFromIp($this->GetIp());
            $presencedepartement->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $presencedepartement->setDeletedBy($user);
            $entityManager->flush();
        }

        if ($request) {
            $this->addFlash('suppseancedep', 'Suppression avec succès.');
        }

        return $this->redirectToRoute('seancedepartement_listeparticipant', [], Response::HTTP_SEE_OTHER);
    }

//    #[Route('/activite/fideles-par-departement', name: 'activite_fideles_par_departement')]
// public function fidelesParDepartement(
//     Request $request,
//     FideleRepository $fideleRepo,
//     FormFactoryInterface $formFactory
// ): Response {
//     $departementId = $request->query->get('departement');
//     $departement = $departementId ? $this->getDoctrine()->getRepository(Departement::class)->find($departementId) : null;

//     // Nouveau formulaire pour juste ce champ
//     $form = $formFactory->create(ActiviteType::class);

//     $form->add('fidele', EntityType::class, [
//         'class' => Fidele::class,
//         'choice_label' => 'nom',
//         'placeholder' => 'Sélectionner un fidele',
//         'query_builder' => function (FideleRepository $repo) use ($departement) {
//             return $repo->createQueryBuilder('m')
//                 ->join('m.sousGroupes', 'sg')
//                 ->join('sg.groupe', 'g')
//                 ->join('g.departement', 'd')
//                 ->where('d = :departement')
//                 ->setParameter('departement', $departement);
//         },
//     ]);

//     return $this->render('activite/_fidele_field.html.twig', [
//         'form' => $form->get('fidele'),
//     ]);
// }


}
