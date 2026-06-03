<?php

namespace App\Controller;

use App\Entity\Presencecellule;
use App\Entity\User;
use App\Entity\Seancecellule;
use App\Entity\Solecellule;
use App\Form\SeancecelluleType;
use App\Repository\CelluleRepository;
use App\Repository\FideleRepository;
use App\Repository\PresencecelluleRepository;
use App\Repository\SeancecelluleRepository;
use App\Repository\SolecelluleRepository;
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

#[Route('/seancecellule')]
class SeancecelluleController extends AbstractController {

    use ClientIp;

    #[Route('/', name: 'seancecellule_index', methods: ['GET'])]
    public function index(SeancecelluleRepository $seancecelluleRepository, SolecelluleRepository $soldeRepo, CelluleRepository $celluleRepo): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_CELLULE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $cellule = $this->getUser()->getCellule();
          $cellule2 = $celluleRepo->findOneCellule($cellule);
        $user = $this->getUser();
        $solde = $soldeRepo->findBy(['cellule' => $cellule2]);
        $seancecellule = $seancecelluleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $difference = $seancecelluleRepository->getSeanceByDates();
        return $this->render('seancecellule/index.html.twig', [
                    'seancecellules' => $seancecellule,
                    'differences' => $difference,
                    'soldes' => $solde,
        ]);
    } 

    #[Route('/{id}/edit', name: 'seancecellule_edit', methods: ['GET', 'POST'])]
    #[Route('/new', name: 'seancecellule_new', methods: ['GET', 'POST'])]
    public function new(EntityManagerInterface $entityManager, Request $request,FileUploader $fileUploader, CelluleRepository $celluleRepository, FideleRepository $fideleRepository, SolecelluleRepository $soldeRepo, ?Seancecellule $seancecellule = null): Response {
        if (!$this->isGranted('ROLE_RESPONSABLE_CELLULE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
         $eglise = $this->getUser()->getEglise();
          $user = $this->getUser();
   
            //Recuperer le groupe et les membres
            $cellule = $celluleRepository->findOneByUser($user);
         if (!$cellule) {
            $this->addFlash('warning', 'Vous ne disposez pas de cellule à gérer.');
            return $this->redirectToRoute('seancecellule_index');
        }
        $type = $seancecellule === null ? 'new' : 'edit';
        $seancecellule = $seancecellule === null ? new Seancecellule() : $seancecellule;
      
        $cellule = $celluleRepository->findOneByUser($user);
        $fidele = $fideleRepository->findBy(['cellule' => $cellule, "deletedAt" => NULL, "etatfidele" => 1]);
        $form = $this->createForm(SeancecelluleType::class, $seancecellule, ['fidele' => $fidele]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

                //            Insertion rapport
            $brochureFile = $form->get('photo')->getData();
            if ($brochureFile) {
                $brochureFileName = $fileUploader->upload($brochureFile);
                $seancecellule->setPhoto($brochureFileName);
            }
            //Adresse ip de l'utilisateur
            if ($type === 'new') {
                $seancecellule->setCreatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setEglise($user->getEglise())
                        ->setCreatedBy($user)
                ;
                $offrande = $form['offrande']->getData();

                $cellule2 = $celluleRepository->findOneCellule($cellule);
                $dql = $soldeRepo->findBy(['cellule' => $cellule]);
                if ($dql) {
                    $id = $dql[0]->getId();
                    $activite = $soldeRepo->findOneBySoldeCellule($id);
                    $mont = $activite->getMontant();
                    $j = 0;
                    $j = $mont + $offrande;
                    $activite->setMontant($j);
                } else {

                    $montant = new Solecellule();
                    $montant->setMontant($offrande);
                    $montant->setCellule($cellule2);
                    $entityManager->persist($montant);
                }
            } else {
                $seancecellule->setUpdatedFromIp($this->GetIp()) // remplacement de la function par le trait
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


            $seancecellule->setCreatedFromIp($this->GetIp());
            $user = $this->getUser();

            $eglise = $this->getUser()->getEglise();
            $seancecellule->setCreatedBy($user);
            $seancecellule->setEglise($eglise);
            $seancecellule->setCellule($user->getCellule());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($seancecellule);
            $entityManager->flush();
            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'seancecellule_new' : 'seancecellule_index';
            if ($nextAction) {
                $this->addFlash('success', 'Enregistrement avec succès.');
            }

            return $this->redirectToRoute($nextAction);
//            return $this->redirectToRoute('seancecellule_index', [], Response::HTTP_SEE_OTHER);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('seancecellule/new.html.twig', [
                    'seancecellule' => $seancecellule,
                    'cellule' => $cellule,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    #[Route('/listeparticipantcellule', name: 'seancecellule_listeparticipant', methods: ['GET'])]
    public function indexpresence(PresencecelluleRepository $presenceRepository, Request $request): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_CELLULE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $presencecellule = $presenceRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $difference = $presenceRepository->getPresenceByDates();
        return $this->render('seancecellule/listeparticipant.html.twig', [
                    'presencecellules' => $presencecellule,
                    'differences' => $difference,
        ]);
    }


    
  
    /**
     * @Route("/search/invitecellules/{id}", name="seancecellule_search_invitecellules", requirements={"id"="\d+"}, methods={"POST"})
     *
     * @return Response
     */
    public function seancecelluleSearchEnfants(SerializerInterface $serializer, Seancecellule $seancecellule): Response {
        if (!$this->isGranted('ROLE_RESPONSABLE_CELLULE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($seancecellule) {
            $invitecellules = (array) json_decode($serializer->serialize($seancecellule->getInvitecellules()->toArray(), 'json', ['groups' => ['public']]));
        } else {
            $invitecellules = [];
        }

        return new Response($this->renderView('seancecellule/listefidele.html.twig', [
                    'invitecellules' => $invitecellules
        ]));
    }

        
        /**
         * Liste des présents pour une séance
         */
        #[Route('/presents', name: 'seancecellule_presents', methods: ['POST'])]
        public function getPresents(Request $request, FideleRepository $fideleRepository, SeancecelluleRepository $seancecelluleRepository): Response
        {
            $seanceId = $request->request->get('seance_id');
            $celluleId = $request->request->get('cellule_id');
            
            // Récupérer la séance
            $seance = $seancecelluleRepository->find($seanceId);
            
            // Récupérer les présents (les Presencecellule pour cette séance)
            $presents = [];
            if ($seance) {
                foreach ($seance->getPresencecellules() as $presence) {
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
            
            return $this->render('seancecellule/_presents_modal.html.twig', [
                'presents' => $presents,
                'seance' => $seance,
                'total' => count($presents)
            ]);
        }

        /**
         * Liste des absents pour une séance
         */
        #[Route('/absents', name: 'seancecellule_absents', methods: ['POST'])]
        public function getAbsents(Request $request, FideleRepository $fideleRepository, SeancecelluleRepository $seancecelluleRepository): Response
        {
            $seanceId = $request->request->get('seance_id');
            $celluleId = $request->request->get('cellule_id');
            
            // Récupérer la séance
            $seance = $seancecelluleRepository->find($seanceId);
            
            // Récupérer les IDs des présents
            $presentIds = [];
            if ($seance) {
                foreach ($seance->getPresencecellules() as $presence) {
                    $fidele = $presence->getFidele();
                    if ($fidele) {
                        $presentIds[] = $fidele->getId();
                    }
                }
            }
            
            // Récupérer tous les membres de la cellule
            $membresCellule = $fideleRepository->findBy([
                'cellule' => $celluleId,
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
            
            return $this->render('seancecellule/_absents_modal.html.twig', [
                'absents' => $absents,
                'seance' => $seance,
                'total' => count($absents),
                'totalMembres' => count($membresCellule)
            ]);
        }

//Fin liste
   #[Route('/presencecellule', name: 'seancecellule_presence', methods: ['POST', 'GET'])]
public function presenceCellule(FideleRepository $fideleRepository, Request $request, PresencecelluleRepository $presencecelluleRepository, CelluleRepository $celluleRepo, SeancecelluleRepository $seancecelluleRepository): Response {
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    
    if (!$this->isGranted('ROLE_RESPONSABLE_CELLULE')) {
        throw $this->createAccessDeniedException('Accès refusé, vous n\'avez pas les droits d\'accès ici!');
    }
    
    $user = $this->getUser();
    $eglise = $user->getEglise();
    
    // Récupérer la cellule de l'utilisateur
    $cellule = $celluleRepo->findOneByUser($user);
    
    if (!$cellule) {
        $this->addFlash('warning', 'Vous ne disposez pas de cellule à gérer.');
        return $this->redirectToRoute('seancecellule_listeparticipant');
    }
    
    if ($request->isMethod('POST')) {
        $seancecelluleId = $request->request->get('seancecellule');
        $tabpost = $request->request->get('tab');
        
        // Vérifications
        if (!$seancecelluleId) {
            $this->addFlash('error', 'Veuillez sélectionner une séance.');
            return $this->redirectToRoute('seancecellule_presence');
        }
        
        if (empty($tabpost)) {
            $this->addFlash('error', 'Veuillez sélectionner au moins un fidèle.');
            return $this->redirectToRoute('seancecellule_presence');
        }
        
        $idseancecellule = $seancecelluleRepository->find($seancecelluleId);
        
        if (!$idseancecellule) {
            $this->addFlash('error', 'Séance non trouvée.');
            return $this->redirectToRoute('seancecellule_presence');
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
            $presenceExistante = $presencecelluleRepository->findOneBy([
                'fidele' => $idfidele, 
                'seancecellule' => $idseancecellule
            ]);
            
            if ($presenceExistante) {
                $presencesDejaExistantes[] = $idfidele->getNomfidele();
            } else {
                $presencecellule = new Presencecellule();
                $presencecellule->setFidele($idfidele);
                $presencecellule->setCellule($cellule);
                $presencecellule->setSeancecellule($idseancecellule);
                $presencecellule->setEglise($eglise);
                $presencecellule->setCreatedBy($user);
                $presencecellule->setCreateAt(new \DateTimeImmutable());
                
                $em->persist($presencecellule);
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
        
        return $this->redirectToRoute('seancecellule_listeparticipant');
        
    } else {
        // Méthode GET
        $fideles = $fideleRepository->findBy([
            'cellule' => $cellule, 
            "deletedAt" => NULL, 
            "etatfidele" => 1
        ]);
        
        $seances = $seancecelluleRepository->findBy([
            'cellule' => $cellule, 
            "deletedAt" => NULL
        ], ['datesuper' => 'DESC']);
        
        return $this->render('seancecellule/presence.html.twig', [
            'fideles' => $fideles,
            'cellule' => $cellule,
            'seancecellules' => $seances
        ]);
    }
}

//Doublon

#[Route('/get-presences-by-seance', name: 'seancecellule_get_presences', methods: ['POST'])]
public function getPresencesBySeance(Request $request, PresencecelluleRepository $presencecelluleRepository, SeancecelluleRepository $seancecelluleRepository): JsonResponse
{
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    
    if (!$this->isGranted('ROLE_RESPONSABLE_CELLULE')) {
        return $this->json(['error' => 'Accès refusé'], 403);
    }
    
    $seanceId = $request->request->get('seanceId');
    
    if (!$seanceId) {
        return $this->json(['error' => 'Séance non spécifiée'], 400);
    }
    
    $seance = $seancecelluleRepository->find($seanceId);
    
    if (!$seance) {
        return $this->json(['error' => 'Séance non trouvée'], 404);
    }
    
    // Récupérer toutes les présences pour cette séance
    $presences = $presencecelluleRepository->findBy(['seancecellule' => $seance]);
    
    // Extraire les IDs des fidèles présents
    $presencesIds = [];
    foreach ($presences as $presence) {
        $presencesIds[] = $presence->getFidele()->getId();
    }
    
    // Compter le nombre de présences par fidèle (au cas où)
    $presencesCount = [];
    foreach ($presences as $presence) {
        $fideleId = $presence->getFidele()->getId();
        if (!isset($presencesCount[$fideleId])) {
            $presencesCount[$fideleId] = 0;
        }
        $presencesCount[$fideleId]++;
    }
    
    return $this->json([
        'success' => true,
        'presences' => $presencesIds,
        'presencesCount' => $presencesCount,
        'seanceId' => $seanceId,
        'seanceDate' => $seance->getDatesuper()->format('d-m-Y'),
        'seanceTheme' => $seance->getTheme()
    ]);
}

//Fin doublon presence

    #[Route('/{id}', name: 'seancecellule_show', methods: ['GET'])]
    public function show(Seancecellule $seancecellule): Response {
        if (!$this->isGranted('ROLE_RESPONSABLE_CELLULE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        return $this->render('seancecellule/show.html.twig', [
                    'seancecellule' => $seancecellule,
        ]);
    }

    #[Route('/{id}', name: 'seancecellule_delete', methods: ['POST'])]
    public function delete(Request $request, Seancecellule $seancecellule): Response {


        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->denyAccessUnlessGranted('seancecellule_delete', $seancecellule);

        if ($this->isCsrfTokenValid('delete' . $seancecellule->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();

            $seancecellule->setDeletedFromIp($this->GetIp());
            $seancecellule->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $seancecellule->setDeletedBy($user);
             $this->addFlash('danger', 'Suppression effectuée avec succès.');
            $entityManager->flush();
        }

        return $this->redirectToRoute('seancecellule_index');
    }

    #[Route('{id}/presencecellule', name: 'presencecellule_delete', methods: ['POST'])]
    public function deletePresence(Request $request, Presencecellule $presencecellule, PresencecelluleRepository $presencecelluleRepository): Response {
        if ($this->isCsrfTokenValid('delete' . $presencecellule->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();

            $presencecellule->setDeletedFromIp($this->GetIp());
            $presencecellule->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $presencecellule->setDeletedBy($user);
             $this->addFlash('danger', 'Suppression effectuée avec succès.');
            $entityManager->flush();
        }

        if ($request) {
            $this->addFlash('suppressionseancecellule', 'Suppression avec succès.');
        }

        return $this->redirectToRoute('seancecellule_listeparticipant', [], Response::HTTP_SEE_OTHER);
    }

}
