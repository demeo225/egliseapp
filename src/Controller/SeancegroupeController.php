<?php

namespace App\Controller;

use App\Entity\Presencegroupe;
use App\Entity\Seancegroupe;
use App\Entity\Soldegroupe;
use App\Form\SeancegroupeType;
use App\Repository\FideleRepository;
use App\Repository\GroupeRepository;
use App\Repository\PresencegroupeRepository;
use App\Repository\SeancegroupeRepository;
use App\Repository\SoldegroupeRepository;
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
 
#[Route('/seancegroupe')]
class SeancegroupeController extends AbstractController {

    use ClientIp;
 
    #[Route('/', name: 'seancegroupe_index', methods: ['GET'])]
    public function index(SeancegroupeRepository $seancegroupeRepository, Request $request): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_GROUPE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $seancegroupe = $seancegroupeRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $difference = $seancegroupeRepository->getSeanceByDates();
        return $this->render('seancegroupe/index.html.twig', [
                    'seancegroupes' => $seancegroupe,
                    'differences' => $difference,
        ]);
    }

        #[Route('/new', name: 'seancegroupe_new', methods: ['GET', 'POST'])]
        #[Route('/{id}/edit', name: 'seancegroupe_edit', methods: ['GET', 'POST'])]
        public function form(
            EntityManagerInterface $entityManager,
            Request $request,
            FileUploader $fileUploader,
            GroupeRepository $groupeRepository,
            FideleRepository $fideleRepository,
            SoldegroupeRepository $soldeRepo,
            ?Seancegroupe $seancegroupe = null
        ): Response {
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

            if (!$this->isGranted('ROLE_RESPONSABLE_GROUPE')) {
                throw $this->createAccessDeniedException('Accès refusé.');
            }

            $isEdit = $seancegroupe !== null;
            $seancegroupe ??= new Seancegroupe();

            $user   = $this->getUser();
            $eglise = $user->getEglise();

            $groupe = $groupeRepository->findOneByUser($user);

            if (!$groupe) {
                $this->addFlash('warning', 'Vous ne disposez pas de sous-groupe à gérer.');
                return $this->redirectToRoute('seancegroupe_index');
            }

            $fideles = $fideleRepository->findFidelesByGroupe($groupe->getId());

            $form = $this->createForm(SeancegroupeType::class, $seancegroupe, [
                'groupes' => [$groupe],   // ✅ clé correcte
                'fideles' => $fideles,    // ✅ clé correcte
            ]);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
 $groupe = $groupeRepository->findOneByUser($user);
                // Upload photo
                $seancegroupe->setGroupe($groupe);
                if ($photo = $form->get('photo')->getData()) {
                    $seancegroupe->setPhoto($fileUploader->upload($photo));
                }

                if (!$isEdit) {
                    $seancegroupe
                        ->setCreatedBy($user)
                        ->setEglise($eglise)
                        ->setCreatedFromIp($this->GetIp());

                    // Mise à jour solde groupe
                    $offrande = $form->get('offrande')->getData();
                    $solde = $soldeRepo->findOneBy(['groupe' => $groupe]);

                    if ($solde) {
                        $solde->setMontant($solde->getMontant() + $offrande);
                    } else {
                        $solde = (new Soldegroupe())
                            ->setGroupe($groupe)
                            ->setMontant($offrande);
                        $entityManager->persist($solde);
                    }
                } else {
                    $seancegroupe
                        ->setUpdatedBy($user)
                        ->setUpdatedFromIp($this->GetIp());
                }

                // Validation date
                if ($form->get('datesuper')->getData() > new \DateTime()) {
                    $this->addFlash('warning', 'Date erronée.');
                    return $this->redirectToRoute('seancegroupe_new');
                }

                // Validation heure
                if ($form->get('heurefin')->getData() < $form->get('heuredebut')->getData()) {
                    $this->addFlash('warning', 'Heure de fin incorrecte.');
                    return $this->redirectToRoute('seancegroupe_new');
                }

                $entityManager->persist($seancegroupe);
                $entityManager->flush();

                $route = $form->get('saveAndAdd')->isClicked()
                    ? 'seancegroupe_new'
                    : 'seancegroupe_index';

                $this->addFlash('success', 'Enregistrement réussi.');
                return $this->redirectToRoute($route);
            }

            return $this->render('seancegroupe/new.html.twig', [
                'seancegroupe' => $seancegroupe,
                'form' => $form->createView(),
            ]);
        }
 

    #[Route('/listeparticipantgroupe', name: 'seancegroupe_listeparticipant', methods: ['GET'])]
    public function indexpresence(PresencegroupeRepository $presenceRepository, Request $request): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_GROUPE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $presencegroupe = $presenceRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $difference = $presenceRepository->getPresenceByDates();
        return $this->render('seancegroupe/listeparticipant.html.twig', [
                    'presencegroupes' => $presencegroupe,
                    'differences' => $difference,
        ]);
    }

            /**Present et absent */
        
            /**
             * @Route("/search/invitegroupes/{id}", name="seancegroupe_search_invitegroupes", requirements={"id"="\d+"}, methods={"POST"})
             *
             * @return Response
             */
            public function seancegroupeSearchEnfants(SerializerInterface $serializer, Seancegroupe $seancegroupe): Response {
                if (!$this->isGranted('ROLE_RESPONSABLE_GROUPE')) {
                    throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
                }
                if ($seancegroupe) {
                    $invitegroupes = (array) json_decode($serializer->serialize($seancegroupe->getInvitegroupes()->toArray(), 'json', ['groups' => ['public']]));
                } else {
                    $invitegroupes = [];
                }

                return new Response($this->renderView('seancegroupe/listefidele.html.twig', [
                            'invitegroupes' => $invitegroupes
                ]));
            }

            
            
                /**
                 * Liste des présents pour une séance
                 */
                #[Route('/presents', name: 'seancegroupe_presents', methods: ['POST'])]
                public function getPresents(Request $request, FideleRepository $fideleRepository, SeancegroupeRepository $seancegroupeRepository): Response
                {
                    $seanceId = $request->request->get('seance_id');
                    $groupeId = $request->request->get('groupe_id');
                    
                    // Récupérer la séance
                    $seance = $seancegroupeRepository->find($seanceId);
                    
                    // Récupérer les présents (les Presencegroupe pour cette séance)
                    $presents = [];
                    if ($seance) {
                        foreach ($seance->getPresencegroupes() as $presence) {
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
                    
                    return $this->render('seancegroupe/_presents_modal.html.twig', [
                        'presents' => $presents,
                        'seance' => $seance,
                        'total' => count($presents)
                    ]);
                }


        #[Route('/absents', name: 'seancegroupe_absents', methods: ['POST'])]
        public function getAbsents(
            Request $request, 
            FideleRepository $fideleRepository, 
            SeancegroupeRepository $seancegroupeRepository
        ): Response {
            $seanceId = $request->request->get('seance_id');
            $groupeId = $request->request->get('groupe_id');
            
            // Récupérer la séance
            $seance = $seancegroupeRepository->find($seanceId);
            
            if (!$seance) {
                return $this->render('seancegroupe/_absents_modal.html.twig', [
                    'error' => 'Séance non trouvée',
                    'absents' => [],
                    'total' => 0,
                    'totalMembres' => 0
                ]);
            }
            
            // Récupérer les IDs des présents (Presencegroupe)
            $presentIds = [];
            if ($seance) {
                foreach ($seance->getPresencegroupes() as $presence) {
                    $fidele = $presence->getFidele();
                    if ($fidele) {
                        $presentIds[] = $fidele->getId();
                    }
                }
            }
            
            // Récupérer tous les membres du groupe (en utilisant votre méthode existante)
            $membresGroupe = $fideleRepository->findFidelesByGroupe($groupeId);
            
            // Filtrer les absents (membres qui ne sont pas dans la liste des présents)
            $absents = [];
            foreach ($membresGroupe as $membre) {
                if (!in_array($membre->getId(), $presentIds)) {
                    $absents[] = [
                        'id' => $membre->getId(),
                        'nom' => $membre->getNomfidele(),
                      
                        'contact' => $membre->getContact1() ?? $membre->getContactwhatssapt()
                    ];
                }
            }
            
            // Statistiques supplémentaires
            $totalMembres = count($membresGroupe);
            $totalPresents = count($presentIds);
            $tauxPresence = $totalMembres > 0 ? round(($totalPresents / $totalMembres) * 100, 2) : 0;
            $tauxAbsence = $totalMembres > 0 ? round((count($absents) / $totalMembres) * 100, 2) : 0;
            
            return $this->render('seancegroupe/_absents_modal.html.twig', [
                'absents' => $absents,
                'seance' => $seance,
                'total' => count($absents),
                'totalMembres' => $totalMembres,
                'totalPresents' => $totalPresents,
                'tauxPresence' => $tauxPresence,
                'tauxAbsence' => $tauxAbsence
            ]);
        }
 
//    
   #[Route('/presencegroupe', name: 'seancegroupe_presence', methods: ['POST', 'GET'])]
        public function presenceGroupe(FideleRepository $fideleRepository, Request $request, PresencegroupeRepository $presencegroupeRepository, GroupeRepository $groupeRepo, SeancegroupeRepository $seancegroupeRepository): Response
        {
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
            
            if (!$this->isGranted('ROLE_RESPONSABLE_GROUPE')) {
                throw $this->createAccessDeniedException('Accès refusé, vous n\'avez pas les droits d\'accès ici!');
            }
            
            $user = $this->getUser();
            $eglise = $user->getEglise();
            
            // Récupérer le groupe de l'utilisateur
            $groupe = $groupeRepo->findOneByUser($user);
            
            if (!$groupe) {
                $this->addFlash('warning', 'Vous ne disposez pas de groupe à gérer.');
                return $this->redirectToRoute('seancegroupe_listeparticipant');
            }
            
            if ($request->isMethod('POST')) {
                $seancegroupeId = $request->request->get('seancegroupe');
                $tabpost = $request->request->get('tab');
                
                // Vérifications
                if (!$seancegroupeId) {
                    $this->addFlash('error', 'Veuillez sélectionner une séance.');
                    return $this->redirectToRoute('seancegroupe_presence');
                }
                
                if (empty($tabpost)) {
                    $this->addFlash('error', 'Veuillez sélectionner au moins un fidèle.');
                    return $this->redirectToRoute('seancegroupe_presence');
                }
                
                $idseancegroupe = $seancegroupeRepository->find($seancegroupeId);
                
                if (!$idseancegroupe) {
                    $this->addFlash('error', 'Séance non trouvée.');
                    return $this->redirectToRoute('seancegroupe_presence');
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
                    $presenceExistante = $presencegroupeRepository->findOneBy([
                        'fidele' => $idfidele, 
                        'seancegroupe' => $idseancegroupe
                    ]);
                    
                    if ($presenceExistante) {
                        $presencesDejaExistantes[] = $idfidele->getNomfidele();
                    } else {
                        $presencegroupe = new Presencegroupe();
                        $presencegroupe->setFidele($idfidele);
                        $presencegroupe->setGroupe($groupe);
                        $presencegroupe->setSeancegroupe($idseancegroupe);
                        $presencegroupe->setEglise($eglise);
                        $presencegroupe->setCreatedBy($user);
                        $presencegroupe->setCreateAt(new \DateTimeImmutable());
                        
                        $em->persist($presencegroupe);
                        $presencesEnregistrees[] = $idfidele->getNomfidele();
                    }
                }
                
                if (!empty($presencesEnregistrees)) {
                    $em->flush();
                    $this->addFlash('success', count($presencesEnregistrees) . ' présence(s) enregistrée(s) : ' . implode(', ', $presencesEnregistrees));
                }
                
                if (!empty($presencesDejaExistantes)) {
                    $this->addFlash('warning', count($presencesDejaExistantes) . ' fidèle(s) avaient déjà une présence : ' . implode(', ', $presencesDejaExistantes));
                }
                
                return $this->redirectToRoute('seancegroupe_listeparticipant');
                
            } else {
                // Méthode GET
                $fideles = $fideleRepository->findFidelesByGroupe($groupe->getId());
                
                $seances = $seancegroupeRepository->findBy([
                    'groupe' => $groupe, 
                    "deletedAt" => NULL
                ], ['datesuper' => 'DESC']);
                
                return $this->render('seancegroupe/presence.html.twig', [
                    'fideles' => $fideles,
                    'groupe' => $groupe,
                    'seancegroupes' => $seances
                ]);
            }
        }

            
        #[Route('/get-presences-by-seance-groupe', name: 'seancegroupe_get_presences', methods: ['POST'])]
        public function getPresencesBySeanceGroupe(Request $request, PresencegroupeRepository $presencegroupeRepository, SeancegroupeRepository $seancegroupeRepository): JsonResponse
        {
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
            
            if (!$this->isGranted('ROLE_RESPONSABLE_GROUPE')) {
                return $this->json(['error' => 'Accès refusé'], 403);
            }
            
            $seanceId = $request->request->get('seanceId');
            
            if (!$seanceId) {
                return $this->json(['error' => 'Séance non spécifiée'], 400);
            }
            
            $seance = $seancegroupeRepository->find($seanceId);
            
            if (!$seance) {
                return $this->json(['error' => 'Séance non trouvée'], 404);
            }
            
            $presences = $presencegroupeRepository->findBy(['seancegroupe' => $seance]);
            
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

    #[Route('/{id}', name: 'seancegroupe_delete', methods: ['POST'])]
    public function delete(Request $request, Seancegroupe $seancegroupe): Response {

        $this->denyAccessUnlessGranted('seancegroupe_delete', $seancegroupe);

        if ($this->isCsrfTokenValid('delete' . $seancegroupe->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();

            $seancegroupe->setDeletedFromIp($this->GetIp());
            $seancegroupe->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $seancegroupe->setDeletedBy($user);
            $this->addFlash('danger', 'Suppression avec succès.');
            $entityManager->flush();
        }

        return $this->redirectToRoute('seancegroupe_index');
    }

    #[Route('/{id}/show', name: 'seancegroupe_show', methods: ['GET'])]
    public function show(Seancegroupe $seancegroupe): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_GROUPE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        return $this->render('seancegroupe/show.html.twig', [
                    'seancegroupe' => $seancegroupe,
        ]);
    }

    #[Route('{id}/presencegroupe', name: 'presencegroupe_delete', methods: ['POST'])]
    public function deletePresence(Request $request, Presencegroupe $presencegroupe, PresencegroupeRepository $presencegroupeRepository): Response {
        if ($this->isCsrfTokenValid('delete' . $presencegroupe->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();

            $presencegroupe->setDeletedFromIp($this->GetIp());
            $presencegroupe->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $presencegroupe->setDeletedBy($user);
              $this->addFlash('danger', 'Suppression avec succès.');
            $entityManager->flush();
        }

        if ($request) {
            $this->addFlash('suppseancegroupe', 'Suppression avec succès.');
        }

        return $this->redirectToRoute('seancegroupe_listeparticipant', [], Response::HTTP_SEE_OTHER);
    }

} 
