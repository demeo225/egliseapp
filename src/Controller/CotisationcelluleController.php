<?php

namespace App\Controller;

use App\Entity\Cotisationcellule;
use App\Form\CotisationcelluleType;
use App\Repository\CelluleRepository;
use App\Repository\CotisationcelluleRepository;
use App\Repository\CotisercelluleRepository;
use App\Repository\SolecelluleRepository;
use App\Repository\FideleRepository;
use App\Repository\DetailcotisationcelluleRepository;
use App\Traits\ClientIp;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cotisationcellule')]
class CotisationcelluleController extends AbstractController {

    use ClientIp;

    #[Route('/', name: 'cotisationcellule_index', methods: ['GET'])]
    public function index(CotisationcelluleRepository $cotisationcelluleRepository,SolecelluleRepository $soldeRepo,  CelluleRepository $celluleRepo): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_CELLULE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
       
                $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $cellule = $this->getUser()->getCellule();
        $cellule2 = $celluleRepo->findOneCellule($cellule);
        $solde = $soldeRepo->findBy(['cellule' => $cellule2]);
        
        $cotisationcellule = $cotisationcelluleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        return $this->render('cotisationcellule/index.html.twig', [
                    'cotisationcellules' => $cotisationcellule,
                    'soldes'=>$solde,
        ]);
    }

    #[Route('/{id}/edit', name: 'cotisationcellule_edit', methods: ['GET', 'POST'])]
    #[Route('/new', name: 'cotisationcellule_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CelluleRepository $celluleRepository, ?Cotisationcellule $cotisationcellule): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_CELLULE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $user = $this->getUser();
                //Recuperer le groupe et les membres
            //$cellule = $user->getCellule();
             $cellule = $celluleRepository->findOneByUser($user);
         if (!$cellule) {
            $this->addFlash('warning', 'Vous ne disposez pas de cellule à gérer.');
            return $this->redirectToRoute('seancecellule_listeparticipant');
        }
        $type = $cotisationcellule === null ? 'new' : 'edit';
        $cotisationcellule = $cotisationcellule === null ? new Cotisationcellule() : $cotisationcellule;
        $cellule = $celluleRepository->findOneByUser($user);
        $form = $this->createForm(CotisationcelluleType::class, $cotisationcellule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //Adresse ip de l'utilisateur
            if ($type === 'new') {
                $cotisationcellule->setCreatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setEglise($user->getEglise())
                        ->setCellule($user->getCellule())
                        ->setCreatedBy($user)
                        ->setEtatcotiser(1)
                ;
            } else {
                $cotisationcellule->setUpdatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setUpdatedBy($user)
                ;
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($cotisationcellule);
            $entityManager->flush();

            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'cotisationcellule_new' : 'cotisationcellule_index';
            if ($nextAction) {
                $this->addFlash('success', 'Enregistrement avec succès.');
            }

            return $this->redirectToRoute($nextAction);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('cotisationcellule/new.html.twig', [
                    'cotisationcellule' => $cotisationcellule,
                    'form' => $form->createView(),
                    'cellule' => $cellule,
                    'response' => $response,
                        ], $response);
    }

    #[Route('/{id}', name: 'cotisationcellule_show', methods: ['GET'])]
    public function show(Cotisationcellule $cotisationcellule): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_CELLULE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        return $this->render('cotisationcellule/show.html.twig', [
                    'cotisationcellule' => $cotisationcellule,
        ]);
    }

    

// #[Route('/cotiser/{id}', name: 'cotisationcellule_cotiser', methods: ['GET'])]
// public function detailCotisationcellule(
//     int $id,
//     CotisercelluleRepository $cotisercelluleRepository,
//     CotisationcelluleRepository $cotisationcelluleRepo,
//     FideleRepository $fideleRepository,
//     CelluleRepository $celluleRepository
// ): Response {
//     $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    
//     if (!$this->isGranted('ROLE_RESPONSABLE_CELLULE')) {
//         throw $this->createAccessDeniedException('Accès refusé, vous n\'avez pas les droits d\'accès ici!');
//     }
    
//     // Récupérer la cotisation
//     $cotisationcellule = $cotisationcelluleRepo->find($id);
    
//     if (!$cotisationcellule) {
//         $this->addFlash('danger', 'Cotisation non trouvée');
//         return $this->redirectToRoute('cotisationcellule_index');
//     }
    
//     // Récupérer la cellule
//     $cellule = $cotisationcellule->getCellule();
    
//     // Compter le nombre de membres de la cellule
//     $nbMembres = 0;
//     $membres = [];
//     if ($cellule) {
//         $membres = $fideleRepository->findBy(['cellule' => $cellule, 'deletedAt' => NULL]);
//         $nbMembres = count($membres);
//     }
    
//     // Calculer le montant prévu réel (nbMembres * montantCotisation)
//     $montantCotisationUnitaire = $cotisationcellule->getMontant() ?? 0;
//     $montantTotalPrevu = $nbMembres * $montantCotisationUnitaire;
    
//     // Récupérer tous les paiements (Cotisercellule) pour cette cotisation
//     $listeCotisercellule = $cotisercelluleRepository->findBy(
//         ['cotisationcellule' => $cotisationcellule, 'deletedAt' => NULL],
//         ['datecotiser' => 'DESC']
//     );
    
//     // Calculer les totaux des paiements
//     $totalPaye = 0;
//     foreach ($listeCotisercellule as $paiement) {
//         $totalPaye += $paiement->getMontantpayer() ?? 0;
//     }
    
//     // Calculer le reste à payer
//     $totalReste = $montantTotalPrevu - $totalPaye;
    
//     // Pour chaque fidèle, calculer s'il a payé ou non
//     $paiementsParFidele = [];
//     foreach ($listeCotisercellule as $paiement) {
//         $fideleId = $paiement->getFidele() ? $paiement->getFidele()->getId() : null;
//         if ($fideleId) {
//             $paiementsParFidele[$fideleId] = $paiement;
//         }
//     }
    
//     // Statistiques par fidèle
//     $statsParFidele = [];
//     foreach ($membres as $membre) {
//         $aPaye = isset($paiementsParFidele[$membre->getId()]);
//         $montantPaye = $aPaye ? $paiementsParFidele[$membre->getId()]->getMontantpayer() : 0;
        
//         $statsParFidele[] = [
//             'fidele' => $membre,
//             'a_paye' => $aPaye,
//             'montant_paye' => $montantPaye,
//             'reste' => $montantCotisationUnitaire - $montantPaye
//         ];
//     }
    
//     return $this->render('cotisationcellule/detail.html.twig', [
//         'cotisationcellule' => $cotisationcellule,
//         'cotisercellules' => $listeCotisercellule,
//         'totalPaye' => $totalPaye,
//         'totalReste' => $totalReste,
//         'montantTotalPrevu' => $montantTotalPrevu,
//         'montantUnitaire' => $montantCotisationUnitaire,
//         'nbMembres' => $nbMembres,
//         'nbPaiements' => count($listeCotisercellule),
//         'membres' => $membres,
//         'statsParFidele' => $statsParFidele,
//         'cellule' => $cellule,
//     ]);
// }


#[Route('/cotiser/{id}', name: 'cotisationcellule_cotiser', methods: ['GET'])]
public function detailCotisationcellule(
    int $id,
    CotisercelluleRepository $cotisercelluleRepository,
    CotisationcelluleRepository $cotisationcelluleRepo,
    FideleRepository $fideleRepository
): Response {
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    
    if (!$this->isGranted('ROLE_RESPONSABLE_CELLULE')) {
        throw $this->createAccessDeniedException('Accès refusé');
    }
    
    $cotisationcellule = $cotisationcelluleRepo->find($id);
    if (!$cotisationcellule) {
        $this->addFlash('danger', 'Cotisation non trouvée');
        return $this->redirectToRoute('app_cotisationcellule_index');
    }
    
    $cellule = $cotisationcellule->getCellule();
    $membres = $cellule ? $fideleRepository->findBy(['cellule' => $cellule, 'deletedAt' => NULL]) : [];
    $nbMembres = count($membres);
    $montantUnitaire = $cotisationcellule->getMontant() ?? 0;
    $montantTotalPrevu = $nbMembres * $montantUnitaire;
    
    // Récupérer tous les paiements
    $listeCotisercellule = $cotisercelluleRepository->findBy(
        ['cotisationcellule' => $cotisationcellule, 'deletedAt' => NULL],
        ['datecotiser' => 'DESC']
    );
    
    $totalPaye = 0;
    foreach ($listeCotisercellule as $paiement) {
        $totalPaye += $paiement->getMontantpayer() ?? 0;
    }
    $totalReste = $montantTotalPrevu - $totalPaye;
    
    // Paiements par fidèle
    $paiementsParFidele = [];
    foreach ($listeCotisercellule as $paiement) {
        $fideleId = $paiement->getFidele() ? $paiement->getFidele()->getId() : null;
        if ($fideleId) {
            $paiementsParFidele[$fideleId] = $paiement;
        }
    }
    
    // Statistiques par fidèle
    $statsParFidele = [];
    foreach ($membres as $membre) {
        $aPaye = isset($paiementsParFidele[$membre->getId()]);
        $montantPaye = $aPaye ? $paiementsParFidele[$membre->getId()]->getMontantpayer() : 0;
        $statsParFidele[] = [
            'fidele' => $membre,
            'a_paye' => $aPaye,
            'montant_paye' => $montantPaye,
            'reste' => $montantUnitaire - $montantPaye,
        ];
    }
    
    return $this->render('cotisationcellule/detail.html.twig', [
        'cotisationcellule' => $cotisationcellule,
        'cotisercellules' => $listeCotisercellule,
        'totalPaye' => $totalPaye,
        'totalReste' => $totalReste,
        'montantTotalPrevu' => $montantTotalPrevu,
        'montantUnitaire' => $montantUnitaire,
        'nbMembres' => $nbMembres,
        'nbPaiements' => count($listeCotisercellule),
        'statsParFidele' => $statsParFidele,
    ]);
}

       #[Route('/detail-paiement/{id}', name: 'detail_paiement_cellule', methods: ['POST'])]
public function detailPaiement(int $id, DetailcotisationcelluleRepository $detailcotisationcelluleRepository, CotisercelluleRepository $cotisercelluleRepository ): Response
{
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    
    // Alternative: chercher par cotisationcellule_id et fidele_id
    $cotisercellule = $cotisercelluleRepository->find($id);
    
    if (!$cotisercellule) {
        return $this->json(['error' => 'Paiement non trouvé'], 404);
    }
    
    // Chercher les détails par cotisationcellule et fidele
    $details = $detailcotisationcelluleRepository->findBy([
        'cotisationcellule' => $cotisercellule->getCotisationcellule(),
        'fidele' => $cotisercellule->getFidele(),
        'deletedAt' => NULL
    ], ['datedetail' => 'DESC']);
    
    $totalMontant = 0;
    $totalPaye = 0;
    $totalReste = 0;
    foreach ($details as $detail) {
        $totalMontant += $detail->getMontant() ?? 0;
        $totalPaye += $detail->getMontantpayer() ?? 0;
        $totalReste += $detail->getReste() ?? 0;
    }
    
    return $this->render('cotisationcellule/_detail_paiement_modal.html.twig', [
        'details' => $details,
        'cotisercellule' => $cotisercellule,
        'totalMontant' => $totalMontant,
        'totalPaye' => $totalPaye,
        'totalReste' => $totalReste,
    ]);
}

     
    #[Route('/{id}/toggle', name: 'cotisationcellule_toggle', methods: ['POST'])]
    public function toggle(Request $request, Cotisationcellule $cotisationcellule): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        // Vérifier les droits
        if (!$this->isGranted('ROLE_RESPONSABLE_DEPARTEMENT') && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Accès refusé, vous n\'avez pas les droits d\'accès ici!');
        }
        
        // Vérifier le token CSRF
        if ($this->isCsrfTokenValid('toggle' . $cotisationcellule->getId(), $request->request->get('_token'))) {
            
            $entityManager = $this->getDoctrine()->getManager();
            $user = $this->getUser();
            
            // Basculer l'état (1 -> 0 ou 0 -> 1)
            $nouvelEtat = $cotisationcellule->getEtatcotiser() == 1 ? 0 : 1;
            $cotisationcellule->setEtatcotiser($nouvelEtat);
            
            // Mettre à jour les informations de modification
            $cotisationcellule->setUpdatedFromIp($this->getIp())
                ->setUpdatedBy($user);
                
            
            $entityManager->flush();
            
            // Message personnalisé selon l'action
            if ($nouvelEtat == 1) {
                $this->addFlash('success', 'Cotisation réactivée avec succès.');
            } else {
                $this->addFlash('success', 'Cotisation clôturée avec succès.');
            }
        } else {
            $this->addFlash('warning', 'Token CSRF invalide.');
        }
        
        return $this->redirectToRoute('cotisationcellule_index', [], Response::HTTP_SEE_OTHER);
    }
    
    #[Route('/{id}', name: 'cotisationcellule_delete', methods: ['POST'])]
   public function delete(Request $request, Cotisationcellule $cotisationcellule, CotisationcelluleRepository $cotisationcelluleRepository): Response
    {
         $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($this->isCsrfTokenValid('delete'.$cotisationcellule->getId(), $request->request->get('_token'))) {
            $cotisationcelluleRepository->remove($cotisationcellule, true);
        }
                $this->addFlash('danger', 'Supression avec succès');


        return $this->redirectToRoute('cotisationcellule_index', [], Response::HTTP_SEE_OTHER);
    }
    

}
