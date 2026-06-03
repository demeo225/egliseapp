<?php

namespace App\Controller;

use App\Entity\Presencezone;
use App\Entity\Seancezone;
use App\Entity\Soldezone;
use App\Form\SeancezoneType;
use App\Repository\FideleRepository;
use App\Repository\PresencezoneRepository;
use App\Repository\SeancezoneRepository;
use App\Repository\SoldezoneRepository;
use App\Repository\ZoneRepository;
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
 
#[Route('/seancezone')]
class SeancezoneController extends AbstractController {

    use ClientIp;

    #[Route('/', name: 'app_seancezone_index', methods: ['GET'])]
    public function index(SeancezoneRepository $seancezoneRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_ZONE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $seancezone = $seancezoneRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $difference = $seancezoneRepository->getSeanceByDates();
        return $this->render('seancezone/index.html.twig', [
                    'seancezones' => $seancezone,
                    'differences' => $difference,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_seancezone_edit', methods: ['GET', 'POST'])]
    #[Route('/new', name: 'app_seancezone_new', methods: ['GET', 'POST'])]
    public function new(EntityManagerInterface $entityManager, Request $request, FileUploader $fileUploader, SeancezoneRepository $seancezoneRepository, ZoneRepository $zoneRepository, FideleRepository $fideleRepository, SoldezoneRepository $soldeRepo, ?Seancezone $seancezone = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_ZONE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }

        $type = $seancezone === null ? 'new' : 'edit';
        $seancezone = $seancezone === null ? new Seancezone() : $seancezone;
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
                 $cellule = $zoneRepository->findOneByUser($user);
         if (!$cellule) {
            $this->addFlash('warning', 'Vous ne disposez pas de zone / secteur à gérer.');
            return $this->redirectToRoute('app_seancezone_index');
        }
        $zone = $zoneRepository->findOneByUser($user);
        $fidele = $fideleRepository->findBy(['eglise' => $eglise, 'zone' => $zone, "deletedAt" => NULL, "etatfidele" => 1]);
        $form = $this->createForm(SeancezoneType::class, $seancezone, ['fidele' => $fidele],);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
           //            Insertion rapport
            $brochureFile = $form->get('photo')->getData();
            if ($brochureFile) {
                $brochureFileName = $fileUploader->upload($brochureFile);
                $seancezone->setPhoto($brochureFileName);
            }
            //Adresse ip de l'utilisateur
            if ($type === 'new') {
                $seancezone->setCreatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setEglise($user->getEglise())
                        ->setZone($user->getZone())
                        ->setCreatedBy($user)
                ;
                
                                            $offrande = $form['offrande']->getData();

                $zone2 = $zoneRepository->findOneZone($zone);
                $dql = $soldeRepo->findBy(['zone' => $zone]);
                if ($dql) {
                    $id = $dql[0]->getId();
                    $activite = $soldeRepo->findOneBySoldeZone($id);
                    $mont = $activite->getMontant();
                    $j = 0;
                    $j = $mont + $offrande;
                    $activite->setMontant($j);
                } else {

                    $montant = new Soldezone();
                    $montant->setMontant($offrande);
                    $montant->setZone($zone2);
                    $entityManager->persist($montant);
                }
            } else {
                $seancezone->setUpdatedFromIp($this->GetIp()) // remplacement de la function par le trait
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

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($seancezone);
            $entityManager->flush();
            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'app_seancezone_new' : 'app_seancezone_index';
            if ($nextAction) {
                $this->addFlash('success', 'Enregistrement avec succès.');
            }

            return $this->redirectToRoute($nextAction);
//            return $this->redirectToRoute('app_seancezone_index', [], Response::HTTP_SEE_OTHER);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('seancezone/new.html.twig', [
                    'seancezone' => $seancezone,
                    'zone' => $zone,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    #[Route('/listeparticipantzone', name: 'app_seancezone_listeparticipant', methods: ['GET'])]
    public function indexPresence(PresencezoneRepository $presenceRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_ZONE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $presencezone = $presenceRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $difference = $presenceRepository->getPresenceByDates();
        return $this->render('seancezone/listeparticipant.html.twig', [
                    'presencezones' => $presencezone,
                    'differences' => $difference,
        ]);
    }

//     
   
//Fin liste
    #[Route('/presencezone', name: 'app_seancezone_presence', methods: ['POST', 'GET'])]
    public function presenceZone(FideleRepository $fideleRepository, Request $request, PresencezoneRepository $presencezoneRepository, ZoneRepository $zoneRepo, SeancezoneRepository $seancezoneRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        if (!$this->isGranted('ROLE_RESPONSABLE_FAMILLE')) {
            throw $this->createAccessDeniedException('Accès refusé, vous n\'avez pas les droits d\'accès ici!');
        }
        
        $user = $this->getUser();
        $eglise = $user->getEglise();
        
        // Récupérer la zone de l'utilisateur
        $zone = $zoneRepo->findOneByUser($user);
        
        if (!$zone) {
            $this->addFlash('warning', 'Vous ne disposez pas de zone à gérer.');
            return $this->redirectToRoute('seancezone_listeparticipant');
        }
        
        if ($request->isMethod('POST')) {
            $seancezoneId = $request->request->get('seancezone');
            $tabpost = $request->request->get('tab');
            
            // Vérifications
            if (!$seancezoneId) {
                $this->addFlash('error', 'Veuillez sélectionner une séance.');
                return $this->redirectToRoute('app_seancezone_presence');
            }
            
            if (empty($tabpost)) {
                $this->addFlash('error', 'Veuillez sélectionner au moins un fidèle.');
                return $this->redirectToRoute('app_seancezone_presence');
            }
            
            $idseancezone = $seancezoneRepository->find($seancezoneId);
            
            if (!$idseancezone) {
                $this->addFlash('error', 'Séance non trouvée.');
                return $this->redirectToRoute('app_seancezone_presence');
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
                $presenceExistante = $presencezoneRepository->findOneBy([
                    'fidele' => $idfidele, 
                    'seancezone' => $idseancezone
                ]);
                
                if ($presenceExistante) {
                    $presencesDejaExistantes[] = $idfidele->getNomfidele();
                } else {
                    $presencezone = new Presencezone();
                    $presencezone->setFidele($idfidele);
                    $presencezone->setZone($zone);
                    $presencezone->setSeancezone($idseancezone);
                    $presencezone->setEglise($eglise);
                    $presencezone->setCreatedBy($user);
                    $presencezone->setCreateAt(new \DateTimeImmutable());
                    
                    $em->persist($presencezone);
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
            
            return $this->redirectToRoute('app_seancezone_listeparticipant');
            
        } else {
            // Méthode GET
            $fideles = $fideleRepository->findBy([
                'zone' => $zone, 
            // "deleteAt" => NULL, 
                "etatfidele" => 1
            ]);
            
            $seances = $seancezoneRepository->findBy([
                'zone' => $zone, 
            // "deleteAt" => NULL
            ], ['datesuper' => 'DESC']);
            
            return $this->render('seancezone/presence.html.twig', [
                'fideles' => $fideles,
                'zone' => $zone,
                'seancezones' => $seances
            ]);
        }
    }

 #[Route('/get-presences-by-seancefammile', name: 'seancezone_get_presences', methods: ['POST', 'GET'])]
        public function getPresencesBySeance(Request $request, PresencezoneRepository $presencezoneRepository, SeancezoneRepository $seancezoneRepository): JsonResponse
        {
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
            
            // Vérifiez le rôle approprié (RESPONSABLE_FAMILLE ou RESPONSABLE_CELLULE ?)
            if (!$this->isGranted('ROLE_RESPONSABLE_FAMILLE') && !$this->isGranted('ROLE_RESPONSABLE_CELLULE')) {
                return $this->json(['error' => 'Accès refusé'], 403);
            }
            
            $seanceId = $request->request->get('seanceId');
            
            if (!$seanceId) {
                return $this->json(['error' => 'Séance non spécifiée'], 400);
            }
            
            $seance = $seancezoneRepository->find($seanceId);
            
            if (!$seance) {
                return $this->json(['error' => 'Séance non trouvée'], 404);
            }
            
            // Récupérer toutes les présences pour cette séance
            $presences = $presencezoneRepository->findBy(['seancezone' => $seance]);
            
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

    #[Route('/{id}', name: 'app_seancezone_show', methods: ['GET'])]
    public function show(Seancezone $seancezone): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_ZONE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        return $this->render('seancezone/show.html.twig', [
                    'seancezone' => $seancezone,
        ]);
    }

    
        /**
     * @Route("/search/invitezones/{id}", name="seancezone_search_invitezones", requirements={"id"="\d+"}, methods={"POST"})
     *
     * @return Response
     */
    public function seancezoneSearchEnfants(SerializerInterface $serializer, Seancezone $seancezone): Response {
        if (!$this->isGranted('ROLE_RESPONSABLE_ZONE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($seancezone) {
            $invitezones = (array) json_decode($serializer->serialize($seancezone->getInvitezones()->toArray(), 'json', ['groups' => ['public']]));
        } else {
            $invitezones = [];
        }

        return new Response($this->renderView('seancezone/listefidele.html.twig', [
                    'invitezones' => $invitezones
        ]));
    }

    
        /**
         * Liste des présents pour une séance
         */
        #[Route('/presents', name: 'seancezone_presents', methods: ['POST'])]
        public function getPresents(Request $request, FideleRepository $fideleRepository, SeancezoneRepository $seancezoneRepository): Response
        {
            $seanceId = $request->request->get('seance_id');
            $zoneId = $request->request->get('zone_id');
            
            // Récupérer la séance
            $seance = $seancezoneRepository->find($seanceId);
            
            // Récupérer les présents (les Presencezone pour cette séance)
            $presents = [];
            if ($seance) {
                foreach ($seance->getPresencezones() as $presence) {
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
            
            return $this->render('seancezone/_presents_modal.html.twig', [
                'presents' => $presents,
                'seance' => $seance,
                'total' => count($presents)
            ]);
        }

        /**
         * Liste des absents pour une séance
         */
        #[Route('/absents', name: 'seancezone_absents', methods: ['POST'])]
        public function getAbsents(Request $request, FideleRepository $fideleRepository, SeancezoneRepository $seancezoneRepository): Response
        {
            $seanceId = $request->request->get('seance_id');
            $zoneId = $request->request->get('zone_id');
            
            // Récupérer la séance
            $seance = $seancezoneRepository->find($seanceId);
            
            // Récupérer les IDs des présents
            $presentIds = [];
            if ($seance) {
                foreach ($seance->getPresencezones() as $presence) {
                    $fidele = $presence->getFidele();
                    if ($fidele) {
                        $presentIds[] = $fidele->getId();
                    }
                }
            }
            
            // Récupérer tous les membres de la zone
            $membresCellule = $fideleRepository->findBy([
                'zone' => $zoneId,
                'deletedAt' => null
            ]);
            
            // Filtrer les absents (membres qui ne sont pas dans la liste des présents)
            $absents = [];
            foreach ($membresCellule as $membre) {
                if (!in_array($membre->getId(), $presentIds)) {
                    $absents[] = [
                        'id' => $membre->getId(),
                        'nom' => $membre->getNomfidele(),
                        'contact' => $membre->getContact1()
                    ];
                }
            }
            
            return $this->render('seancezone/_absents_modal.html.twig', [
                'absents' => $absents,
                'seance' => $seance,
                'total' => count($absents),
                'totalMembres' => count($membresCellule)
            ]);
        }

//Fin liste

    #[Route('/{id}', name: 'app_seancezone_delete', methods: ['POST'])]
    public function delete(Request $request, Seancezone $seancezone): Response {


        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->denyAccessUnlessGranted('seancezone_delete', $seancezone);

        if ($this->isCsrfTokenValid('delete' . $seancezone->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();

            $seancezone->setDeletedFromIp($this->GetIp());
            $seancezone->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $seancezone->setDeletedBy($user);
            $entityManager->flush();
                if ($request) {
            $this->addFlash('danger', 'Suppression avec succès.');
        }
        }

        return $this->redirectToRoute('app_seancezone_index');
    }

    #[Route('{id}/presencezone', name: 'presencezone_delete', methods: ['POST'])]
    public function deletePresence(Request $request, Presencezone $presencezone, Seancezone $seancezone): Response {
        $this->denyAccessUnlessGranted('seancezone_delete', $seancezone);

        if ($this->isCsrfTokenValid('delete' . $presencezone->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();

            $presencezone->setDeletedFromIp($this->GetIp());
            $presencezone->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $presencezone->setDeletedBy($user);
            $entityManager->flush();
        }

        if ($request) {
            $this->addFlash('suppressionzone', 'Suppression avec succès.');
        }

        return $this->redirectToRoute('app_seancezone_listeparticipant', [], Response::HTTP_SEE_OTHER);
    }

}
