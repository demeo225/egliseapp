<?php

namespace App\Controller;

//use Symfony\Component\Validator\Constraints\DateTime;


use App\Entity\Presencefamille;
use App\Entity\Seancefamille;
use App\Entity\Soldefamille;
use App\Form\SeancefamilleType;
use App\Repository\FamilleRepository;
use App\Repository\FideleRepository;
use App\Repository\PresencefamilleRepository;
use App\Repository\SeancefamilleRepository;
use App\Repository\SoldefamilleRepository;
use App\Service\FileUploader;
use App\Traits\ClientIp;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
 
#[Route('/seancefamille')]
class SeancefamilleController extends AbstractController {

    use ClientIp;

    #[Route('/', name: 'seancefamille_index', methods: ['GET'])]
    public function index(SeancefamilleRepository $seancefamilleRepository, Request $request): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_FAMILLE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $roles = $user->getRoles();
        $seancefamille = $seancefamilleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $difference = $seancefamilleRepository->getSeanceByDates();
        return $this->render('seancefamille/index.html.twig', [
                    'seancefamilles' => $seancefamille,
                    'differences' => $difference,
        ]);
    }

    #[Route('/{id}/edit', name: 'seancefamille_edit', methods: ['GET', 'POST'])]
    #[Route('/new', name: 'seancefamille_new', methods: ['GET', 'POST'])]
    public function new(EntityManagerInterface $entityManager, Request $request, FileUploader $fileUploader, SoldefamilleRepository $soldeRepo, FamilleRepository $familleRepository, FideleRepository $fideleRepository, ?Seancefamille $seancefamille = null): Response {
        if (!$this->isGranted('ROLE_RESPONSABLE_FAMILLE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $type = $seancefamille === null ? 'new' : 'edit';
        $seancefamille = $seancefamille === null ? new Seancefamille() : $seancefamille;
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();

                  //Recuperer le groupe et les membres
            $famille = $familleRepository->findOneByUser($user);
         if (!$famille) {
            $this->addFlash('warning', 'Vous ne disposez pas de Famille à gérer.');
            return $this->redirectToRoute('seancefamille_index');
        }

      //  $famille = $familleRepository->findBy(['eglise' => $eglise, "user" => $user, "deletedAt" => NULL]);
        $fidele = $fideleRepository->findBy(['famille' => $famille, "deletedAt" => NULL, "etatfidele" => 1]);
        $form = $this->createForm(SeancefamilleType::class, $seancefamille, ['fidele' => $fidele],);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
             $user = $this->getUser();
           //            Insertion rapport
            $brochureFile = $form->get('photo')->getData();
            if ($brochureFile) {
                $brochureFileName = $fileUploader->upload($brochureFile);
                $seancefamille->setPhoto($brochureFileName);
            }

            //Adresse ip de l'utilisateur
            if ($type === 'new') {
                $seancefamille->setCreatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setEglise($user->getEglise())
                        ->setCreatedBy($user)
                ;
                $offrande = $form['offrande']->getData();

                $famille2 = $familleRepository->findOneFamille($famille);
                $dql = $soldeRepo->findBy(['famille' => $famille]);
                if ($dql) {
                    $id = $dql[0]->getId();
                    $activite = $soldeRepo->findOneBySoldeFamille($id);
                    $mont = $activite->getMontant();
                    $j = 0;
                    $j = $mont + $offrande;
                    $activite->setMontant($j);
                } else {

                    $montant = new Soldefamille();
                    $montant->setMontant($offrande);
                    $montant->setFamille($famille2);
                    $entityManager->persist($montant);
                }
            } else {
                $seancefamille->setUpdatedFromIp($this->GetIp()) // remplacement de la function par le trait
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
            $seancefamille->setFamille($user->getFamille());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($seancefamille);
            $entityManager->flush();
            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'seancefamille_new' : 'seancefamille_index';
            if ($nextAction) {
                $this->addFlash('success', 'Enregistrement avec succès.');
            }

            return $this->redirectToRoute($nextAction);
//            return $this->redirectToRoute('seancefamille_index', [], Response::HTTP_SEE_OTHER);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('seancefamille/new.html.twig', [
                    'seancefamille' => $seancefamille,
                    'famille' => $user->getFamille(),
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    
 
        #[Route('/listeparticipantfamille', name: 'seancefamille_listeparticipant', methods: ['GET'])]
        public function indexpresence(
            PresencefamilleRepository $presenceRepository, 
            FideleRepository $fideleRepository,
            FamilleRepository $familleRepository,  // Ajout du repository
            Request $request
        ): Response {
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
            
            $user = $this->getUser();
            $eglise = $user->getEglise();
            
            // Construction de la requête selon le rôle
            $qb = $presenceRepository->createQueryBuilder('p')
                ->leftJoin('p.seancefamille', 's')
                ->leftJoin('p.famille', 'c')
                ->leftJoin('c.zone', 'z')
                ->where('p.eglise = :eglise')
                ->andWhere('p.deletedAt IS NULL')
                ->setParameter('eglise', $eglise);
            
            // Filtres selon les rôles
            if ($this->isGranted('ROLE_ADMIN') || $this->isGranted('ROLE_PASTEUR') || $this->isGranted('ROLE_SECRETAIRE')) {
                // Ces rôles voient toutes les présences
            } 
            elseif ($this->isGranted('ROLE_RESPONSABLE_ZONE')) {
                $zone = $user->getZone();
                if ($zone) {
                    $qb->andWhere('z.id = :zoneId')->setParameter('zoneId', $zone->getId());
                } else {
                    $this->addFlash('warning', 'Aucune zone associée à votre compte.');
                    return $this->redirectToRoute('dashboard');
                }
            }
            elseif ($this->isGranted('ROLE_RESPONSABLE_FAMILLE')) {
                $famille = $user->getFamille();
                if ($famille) {
                    $qb->andWhere('c.id = :familleId')->setParameter('familleId', $famille->getId());
                } else {
                    $this->addFlash('warning', 'Aucune famille associée à votre compte.');
                    return $this->redirectToRoute('dashboard');
                }
            }
            else {
                $this->addFlash('error', 'Vous n\'avez pas les droits pour voir les présences.');
                return $this->redirectToRoute('dashboard');
            }
            
            $presences = $qb->orderBy('s.datesuper', 'DESC')->getQuery()->getResult();
            
            // Récupérer toutes les familles pour connaître leurs membres
            $familles = $this->getFamillesAccessibles($user, $eglise, $familleRepository);
            
            // Compter les membres par famille
            $membresParFamille = [];
            foreach ($familles as $famille) {
                $membresParFamille[$famille->getId()] = [
                    'famille' => $famille,
                    'nb_membres' => $fideleRepository->count(['famille' => $famille, 'deletedAt' => NULL]),
                    'nb_presences' => 0
                ];
            }
            
            // Grouper par date et par famille avec calcul des taux
            $presencesParDateEtFamille = [];
            $totalGeneral = 0;
            
            foreach ($presences as $presence) {
                if ($presence->getSeancefamille() && $presence->getSeancefamille()->getDatesuper()) {
                    $dateKey = $presence->getSeancefamille()->getDatesuper()->format('Y-m-d');
                    $familleId = $presence->getFamille()->getId();
                    $familleNom = $presence->getFamille()->getNom();
                    
                    if (!isset($presencesParDateEtFamille[$dateKey])) {
                        $presencesParDateEtFamille[$dateKey] = [
                            'date' => $presence->getSeancefamille()->getDatesuper(),
                            'familles' => [],
                            'total_jour' => 0,
                            'total_membres_jour' => 0
                        ];
                    }
                    if (!isset($presencesParDateEtFamille[$dateKey]['familles'][$familleId])) {
                        $nbMembres = $membresParFamille[$familleId]['nb_membres'] ?? 0;
                        $presencesParDateEtFamille[$dateKey]['familles'][$familleId] = [
                            'famille_id' => $familleId,
                            'famille_nom' => $familleNom,
                            'presences' => [],
                            'total_presences' => 0,
                            'nb_membres' => $nbMembres,
                            'taux_presence' => 0
                        ];
                        $presencesParDateEtFamille[$dateKey]['total_membres_jour'] += $nbMembres;
                    }
                    $presencesParDateEtFamille[$dateKey]['familles'][$familleId]['presences'][] = $presence;
                    $presencesParDateEtFamille[$dateKey]['familles'][$familleId]['total_presences']++;
                    $presencesParDateEtFamille[$dateKey]['total_jour']++;
                    $totalGeneral++;
                }
            }
            
            // Calculer les taux de présence pour chaque famille et chaque date
            foreach ($presencesParDateEtFamille as $dateKey => &$dateData) {
                foreach ($dateData['familles'] as &$familleData) {
                    if ($familleData['nb_membres'] > 0) {
                        $familleData['taux_presence'] = round(($familleData['total_presences'] / $familleData['nb_membres']) * 100, 2);
                    } else {
                        $familleData['taux_presence'] = 0;
                    }
                }
                // Calculer le taux global de la journée
                if ($dateData['total_membres_jour'] > 0) {
                    $dateData['taux_global_jour'] = round(($dateData['total_jour'] / $dateData['total_membres_jour']) * 100, 2);
                } else {
                    $dateData['taux_global_jour'] = 0;
                }
            }
            
            // Calculer le taux global général
            $totalMembresGeneral = array_sum(array_column($membresParFamille, 'nb_membres'));
            $tauxGeneral = $totalMembresGeneral > 0 ? round(($totalGeneral / $totalMembresGeneral) * 100, 2) : 0;
            
            return $this->render('seancefamille/listeparticipant.html.twig', [
                'presencesParDateEtFamille' => $presencesParDateEtFamille,
                'totalGeneral' => $totalGeneral,
                'totalMembresGeneral' => $totalMembresGeneral,
                'tauxGeneral' => $tauxGeneral,
            ]);
        }

        /**
         * Récupère les familles accessibles selon le rôle de l'utilisateur
         */
        private function getFamillesAccessibles($user, $eglise, FamilleRepository $familleRepository): array
        {
            if ($this->isGranted('ROLE_ADMIN') || $this->isGranted('ROLE_PASTEUR') || $this->isGranted('ROLE_SECRETAIRE')) {
                // Ces rôles voient toutes les familles
                return $familleRepository->findBy(['eglise' => $eglise, 'deletedAt' => NULL]);
            } 
            elseif ($this->isGranted('ROLE_RESPONSABLE_ZONE')) {
                $zone = $user->getZone();
                if ($zone) {
                    return $zone->getFamilles()->toArray();
                }
            }
            elseif ($this->isGranted('ROLE_RESPONSABLE_FAMILLE')) {
                $famille = $user->getFamille();
                if ($famille) {
                    return [$famille];
                }
            }
            return [];
        }


/**
 * Récupère les dates distinctes des séances
 */
private function getDistinctDates($presencefamilles): array
{
    $dates = [];
    foreach ($presencefamilles as $presence) {
        if ($presence->getSeancefamille() && $presence->getSeancefamille()->getDatesuper()) {
            $date = $presence->getSeancefamille()->getDatesuper();
            $dateKey = $date->format('Y-m-d');
            if (!isset($dates[$dateKey])) {
                $dates[$dateKey] = $date;
            }
        }
    }
    return $dates;
}


    #[Route('/{id}/show', name: 'seancefamille_show', methods: ['GET'])]
    public function show(Seancefamille $seancefamille): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
//        if (!$this->isGranted('ROLE_RESPONSABLE_FAMILLE')) {
//            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
//        }
        $this->denyAccessUnlessGranted('seancefamille_index', $seancefamille);
        return $this->render('seancefamille/show.html.twig', [
                    'seancefamille' => $seancefamille,
        ]);
            }
      
           

    /**
     * @Route("/search/invitefamilles/{id}", name="seancefamille_search_invitefamilles", requirements={"id"="\d+"}, methods={"POST"})
     *
     * @return Response
     */
    public function seancefamilleSearchEnfants(SerializerInterface $serializer, Seancefamille $seancefamille): Response {
        if (!$this->isGranted('ROLE_RESPONSABLE_FAMILLE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($seancefamille) {
            $invitefamilles = (array) json_decode($serializer->serialize($seancefamille->getInvitefamilles()->toArray(), 'json', ['groups' => ['public']]));
        } else {
            $invitefamilles = [];
        }

        return new Response($this->renderView('seancefamille/listefidele.html.twig', [
                    'invitefamilles' => $invitefamilles
        ]));
    }

    
        /**
         * Liste des présents pour une séance
         */
        #[Route('/presents', name: 'seancefamille_presents', methods: ['POST'])]
        public function getPresents(Request $request, FideleRepository $fideleRepository, SeancefamilleRepository $seancefamilleRepository): Response
        {
            $seanceId = $request->request->get('seance_id');
            $familleId = $request->request->get('famille_id');
            
            // Récupérer la séance
            $seance = $seancefamilleRepository->find($seanceId);
            
            // Récupérer les présents (les Presencefamille pour cette séance)
            $presents = [];
            if ($seance) {
                foreach ($seance->getPresencefamilles() as $presence) {
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
            
            return $this->render('seancefamille/_presents_modal.html.twig', [
                'presents' => $presents,
                'seance' => $seance,
                'total' => count($presents)
            ]);
        }

        /**
         * Liste des absents pour une séance
         */
        #[Route('/absents', name: 'seancefamille_absents', methods: ['POST'])]
        public function getAbsents(Request $request, FideleRepository $fideleRepository, SeancefamilleRepository $seancefamilleRepository): Response
        {
            $seanceId = $request->request->get('seance_id');
            $familleId = $request->request->get('famille_id');
            
            // Récupérer la séance
            $seance = $seancefamilleRepository->find($seanceId);
            
            // Récupérer les IDs des présents
            $presentIds = [];
            if ($seance) {
                foreach ($seance->getPresencefamilles() as $presence) {
                    $fidele = $presence->getFidele();
                    if ($fidele) {
                        $presentIds[] = $fidele->getId();
                    }
                }
            }
            
            // Récupérer tous les membres de la famille
            $membresFamille = $fideleRepository->findBy([
                'famille' => $familleId,
                'deletedAt' => null
            ]);
            
            // Filtrer les absents (membres qui ne sont pas dans la liste des présents)
            $absents = [];
            foreach ($membresFamille as $membre) {
                if (!in_array($membre->getId(), $presentIds)) {
                    $absents[] = [
                        'id' => $membre->getId(),
                        'nom' => $membre->getNomfidele(),
                        'contact' => $membre->getContact1()
                    ];
                }
            }
            
            return $this->render('seancefamille/_absents_modal.html.twig', [
                'absents' => $absents,
                'seance' => $seance,
                'total' => count($absents),
                'totalMembres' => count($membresFamille)
            ]);
        }

//Fin liste
    #[Route('/presencefamille', name: 'seancefamille_presence', methods: ['POST', 'GET'])]
    public function presenceFamille(FideleRepository $fideleRepository, Request $request, PresencefamilleRepository $presencefamilleRepository, FamilleRepository $familleRepo, SeancefamilleRepository $seancefamilleRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        if (!$this->isGranted('ROLE_RESPONSABLE_FAMILLE')) {
            throw $this->createAccessDeniedException('Accès refusé, vous n\'avez pas les droits d\'accès ici!');
        }
        
        $user = $this->getUser();
        $eglise = $user->getEglise();
        
        // Récupérer la famille de l'utilisateur
        $famille = $familleRepo->findOneByUser($user);
        
        if (!$famille) {
            $this->addFlash('warning', 'Vous ne disposez pas de famille à gérer.');
            return $this->redirectToRoute('seancefamille_listeparticipant');
        }
        
        if ($request->isMethod('POST')) {
            $seancefamilleId = $request->request->get('seancefamille');
            $tabpost = $request->request->get('tab');
            
            // Vérifications
            if (!$seancefamilleId) {
                $this->addFlash('error', 'Veuillez sélectionner une séance.');
                return $this->redirectToRoute('seancefamille_presence');
            }
            
            if (empty($tabpost)) {
                $this->addFlash('error', 'Veuillez sélectionner au moins un fidèle.');
                return $this->redirectToRoute('seancefamille_presence');
            }
            
            $idseancefamille = $seancefamilleRepository->find($seancefamilleId);
            
            if (!$idseancefamille) {
                $this->addFlash('error', 'Séance non trouvée.');
                return $this->redirectToRoute('seancefamille_presence');
            }
            
            $em = $this->getDoctrine()->getManager();
            $presencesEnregistrees = [];
            $presencesDejaExistantes = [];
            
            foreach ($tabpost as $fideleId) {
                $idfidele = $fideleRepository->find($fideleId);
                
                if (!$idfidele) {
                    continue;
                }
                
                // Vérifier si le fidèle a déjà une présence pour cette séance
                $presenceExistante = $presencefamilleRepository->findOneBy([
                    'fidele' => $idfidele, 
                    'seancefamille' => $idseancefamille
                ]);
                
                if ($presenceExistante) {
                    $presencesDejaExistantes[] = $idfidele->getNomfidele();
                } else {
                    $presencefamille = new Presencefamille();
                    $presencefamille->setFidele($idfidele);
                    $presencefamille->setFamille($famille);
                    $presencefamille->setSeancefamille($idseancefamille);
                    $presencefamille->setEglise($eglise);
                    $presencefamille->setCreatedBy($user);
                    $presencefamille->setCreateAt(new \DateTimeImmutable());
                    
                    $em->persist($presencefamille);
                    $presencesEnregistrees[] = $idfidele->getNomfidele();
                }
            }
            
            // Flush uniquement s'il y a des enregistrements
            if (!empty($presencesEnregistrees)) {
                $em->flush();
                $this->addFlash('success', count($presencesEnregistrees) . ' présence(s) enregistrée(s) : ' . implode(', ', $presencesEnregistrees));
            }
            
            if (!empty($presencesDejaExistantes)) {
                $this->addFlash('warning', count($presencesDejaExistantes) . ' fidèle(s) avaient déjà une présence : ' . implode(', ', $presencesDejaExistantes));
            }
            
            return $this->redirectToRoute('seancefamille_listeparticipant');
            
        } else {
            // Méthode GET
            $fideles = $fideleRepository->findBy([
                'famille' => $famille, 
            // "deleteAt" => NULL, 
                "etatfidele" => 1
            ]);
            
            $seances = $seancefamilleRepository->findBy([
                'famille' => $famille, 
            // "deleteAt" => NULL
            ], ['datesuper' => 'DESC']);
            
            return $this->render('seancefamille/presence.html.twig', [
                'fideles' => $fideles,
                'famille' => $famille,
                'seancefamilles' => $seances
            ]);
        }
    }

 #[Route('/get-presences-by-seancefammile', name: 'seancefamille_get_presences', methods: ['POST', 'GET'])]
        public function getPresencesBySeance(Request $request, PresencefamilleRepository $presencefamilleRepository, SeancefamilleRepository $seancefamilleRepository): JsonResponse
        {
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
            
            // Vérifiez le rôle approprié (RESPONSABLE_FAMILLE ou RESPONSABLE_FAMILLE ?)
            if (!$this->isGranted('ROLE_RESPONSABLE_FAMILLE') && !$this->isGranted('ROLE_RESPONSABLE_FAMILLE')) {
                return $this->json(['error' => 'Accès refusé'], 403);
            }
            
            $seanceId = $request->request->get('seanceId');
            
            if (!$seanceId) {
                return $this->json(['error' => 'Séance non spécifiée'], 400);
            }
            
            $seance = $seancefamilleRepository->find($seanceId);
            
            if (!$seance) {
                return $this->json(['error' => 'Séance non trouvée'], 404);
            }
            
            // Récupérer toutes les présences pour cette séance
            $presences = $presencefamilleRepository->findBy(['seancefamille' => $seance]);
            
            // Extraire les IDs des fidèles présents
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

    #[Route('/{id}', name: 'seancefamille_delete', methods: ['POST'])]
    public function delete(Request $request, Seancefamille $seancefamille): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $this->denyAccessUnlessGranted('seancefamille_delete', $seancefamille);

        if ($this->isCsrfTokenValid('delete' . $seancefamille->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();

            $seancefamille->setDeletedFromIp($this->GetIp());
            $seancefamille->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $seancefamille->setDeletedBy($user);
             $this->addFlash('danger', 'Suppression effectuée avec succès.');
            $entityManager->flush();
        }

        return $this->redirectToRoute('seancefamille');
    }

    #[Route('{id}/presencefamille', name: 'presencefamille_delete', methods: ['POST'])]
    public function deletePresence(Request $request, Presencefamille $presencefamille, PresencefamilleRepository $presencefamilleRepository): Response {
        if ($this->isCsrfTokenValid('delete' . $presencefamille->getId(), $request->request->get('_token'))) {
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
            $entityManager = $this->getDoctrine()->getManager();

            $presencefamille->setDeletedFromIp($this->GetIp());
            $presencefamille->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $presencefamille->setDeletedBy($user);
            
            $entityManager->flush();
        }

        if ($request) {
            $this->addFlash('danger', 'Suppression avec succès.');
        }

        return $this->redirectToRoute('seancefamille_listeparticipant', [], Response::HTTP_SEE_OTHER);
    }

}
