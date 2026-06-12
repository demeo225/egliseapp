<?php

namespace App\Controller;

use App\Entity\Cotisationzone;
use App\Form\CotisationzoneType;
use App\Repository\CotiserzoneRepository;
use App\Repository\SoldezoneRepository;
use App\Repository\CotisationzoneRepository;
use App\Repository\ZoneRepository;
use App\Repository\FideleRepository;
use App\Repository\DetailcotisationzoneRepository;

use App\Traits\ClientIp;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cotisationzone')]
class CotisationzoneController extends AbstractController {

    use ClientIp;

   #[Route('/', name: 'app_cotisationzone_index', methods: ['GET'])]
    public function index(CotisationzoneRepository $cotisationzoneRepository, SoldezoneRepository $soldeRepo, ZoneRepository $zoneRepo): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        $user = $this->getUser();
        $eglise = $user->getEglise();
        $zone = $user->getZone();
        
        // Vérifier les droits d'accès
        if ($this->isGranted('ROLE_ADMIN') || $this->isGranted('ROLE_PASTEUR') || $this->isGranted('ROLE_SECRETAIRE')) {
            // Rôle supérieur : voit toutes les cotisations
            $cotisations = $cotisationzoneRepository->findBy([
                'eglise' => $eglise, 
                'deletedAt' => NULL
            ], ['createAt' => 'DESC']);
        } 
        elseif ($this->isGranted('ROLE_RESPONSABLE_ZONE') && $zone) {
            // Responsable de zone : voit uniquement les cotisations de sa zone
            $cotisations = $cotisationzoneRepository->findBy([
                'eglise' => $eglise, 
                'zone' => $zone,
                'deletedAt' => NULL
            ], ['createAt' => 'DESC']);
        } 
        else {
            $this->addFlash('warning', 'Vous n\'avez pas les droits pour voir les cotisations.');
            return $this->redirectToRoute('home');
        }
        
        // Récupération du solde
        $solde = [];
        if ($zone) {
            $zone2 = $zoneRepo->findOneZone($zone);
            if ($zone2) {
                $solde = $soldeRepo->findBy(['zone' => $zone2]);
            }
        }
        
        return $this->render('cotisationzone/index.html.twig', [
            'cotisationzones' => $cotisations,
            'soldes' => $solde,
        ]);
    }


    #[Route('/{id}/edit', name: 'app_cotisationzone_edit', methods: ['GET', 'POST'])]
    #[Route('/new', name: 'app_cotisationzone_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CotisationzoneRepository $cotisationzoneRepository, ZoneRepository $zoneRepository, ?Cotisationzone $cotisationzone = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_ZONE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }

        $type = $cotisationzone === null ? 'new' : 'edit';
        $cotisationzone = $cotisationzone === null ? new Cotisationzone() : $cotisationzone;

        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
         $zone = $zoneRepository->findOneByUser($user);
           if (!$zone) {
            $this->addFlash('warning', 'Vous ne disposez pas zone/secteur à gérer.');
            return $this->redirectToRoute('app_cotisationzone_index');
        }
        $form = $this->createForm(CotisationzoneType::class, $cotisationzone,);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $zone = $zoneRepository->findOneByUser($user);
            if ($type === 'new') {
                $cotisationzone->setCreatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setEglise($user->getEglise())
                        ->setCreatedBy($user)
                        ->setZone($zone)
                        ->setEtatcotiser(1)
                ;
            } else {
                $cotisationzone->setUpdatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setUpdatedBy($user)
                ;
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($cotisationzone);
            $entityManager->flush();
            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'app_cotisationzone_new' : 'app_cotisationzone_index';
            if ($nextAction) {
                $this->addFlash('cotisationzone', 'Action effectuée avec succès.');
            }

            return $this->redirectToRoute($nextAction);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('cotisationzone/new.html.twig', [
                    'cotisationzone' => $cotisationzone,
                    'zone' => $zone,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    
    #[Route('/{id}/toggle', name: 'cotisationzone_toggle', methods: ['POST'])]
    public function toggle(Request $request, Cotisationzone $cotisationzone): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        // Vérifier les droits
        if (!$this->isGranted('ROLE_RESPONSABLE_DEPARTEMENT') && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Accès refusé, vous n\'avez pas les droits d\'accès ici!');
        }
        
        // Vérifier le token CSRF
        if ($this->isCsrfTokenValid('toggle' . $cotisationzone->getId(), $request->request->get('_token'))) {
            
            $entityManager = $this->getDoctrine()->getManager();
            $user = $this->getUser();
            
            // Basculer l'état (1 -> 0 ou 0 -> 1)
            $nouvelEtat = $cotisationzone->getEtatcotiser() == 1 ? 0 : 1;
            $cotisationzone->setEtatcotiser($nouvelEtat);
            
            // Mettre à jour les informations de modification
            $cotisationzone->setUpdatedFromIp($this->getIp())
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
        
        return $this->redirectToRoute('app_cotisationzone_index', [], Response::HTTP_SEE_OTHER);
    }
     
 

// #[Route('/cotiser/{id}', name: 'cotisationzone_cotiser', methods: ['GET'])]
// public function detailCotisationzone(
//     int $id,
//     CotiserzoneRepository $cotiserzoneRepository,
//     CotisationzoneRepository $cotisationzoneRepo,
//     FideleRepository $fideleRepository,
//     ZoneRepository $zoneRepository
// ): Response {
//     $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    
//     if (!$this->isGranted('ROLE_RESPONSABLE_CELLULE')) {
//         throw $this->createAccessDeniedException('Accès refusé, vous n\'avez pas les droits d\'accès ici!');
//     }
    
//     // Récupérer la cotisation
//     $cotisationzone = $cotisationzoneRepo->find($id);
    
//     if (!$cotisationzone) {
//         $this->addFlash('danger', 'Cotisation non trouvée');
//         return $this->redirectToRoute('cotisationzone_index');
//     }
    
//     // Récupérer la zone
//     $zone = $cotisationzone->getZone();
    
//     // Compter le nombre de membres de la zone
//     $nbMembres = 0;
//     $membres = [];
//     if ($zone) {
//         $membres = $fideleRepository->findBy(['zone' => $zone, 'deletedAt' => NULL]);
//         $nbMembres = count($membres);
//     }
    
//     // Calculer le montant prévu réel (nbMembres * montantCotisation)
//     $montantCotisationUnitaire = $cotisationzone->getMontant() ?? 0;
//     $montantTotalPrevu = $nbMembres * $montantCotisationUnitaire;
    
//     // Récupérer tous les paiements (Cotiserzone) pour cette cotisation
//     $listeCotiserzone = $cotiserzoneRepository->findBy(
//         ['cotisationzone' => $cotisationzone, 'deletedAt' => NULL],
//         ['datecotiser' => 'DESC']
//     );
    
//     // Calculer les totaux des paiements
//     $totalPaye = 0;
//     foreach ($listeCotiserzone as $paiement) {
//         $totalPaye += $paiement->getMontantpayer() ?? 0;
//     }
    
//     // Calculer le reste à payer
//     $totalReste = $montantTotalPrevu - $totalPaye;
    
//     // Pour chaque fidèle, calculer s'il a payé ou non
//     $paiementsParFidele = [];
//     foreach ($listeCotiserzone as $paiement) {
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
    
//     return $this->render('cotisationzone/detail.html.twig', [
//         'cotisationzone' => $cotisationzone,
//         'cotiserzones' => $listeCotiserzone,
//         'totalPaye' => $totalPaye,
//         'totalReste' => $totalReste,
//         'montantTotalPrevu' => $montantTotalPrevu,
//         'montantUnitaire' => $montantCotisationUnitaire,
//         'nbMembres' => $nbMembres,
//         'nbPaiements' => count($listeCotiserzone),
//         'membres' => $membres,
//         'statsParFidele' => $statsParFidele,
//         'zone' => $zone,
//     ]);
// }


#[Route('/cotiser/{id}', name: 'cotisationzone_cotiser', methods: ['GET'])]
public function detailCotisationzone(
    int $id,
    CotiserzoneRepository $cotiserzoneRepository,
    CotisationzoneRepository $cotisationzoneRepo,
    FideleRepository $fideleRepository
): Response {
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    
    if (!$this->isGranted('ROLE_RESPONSABLE_ZONE')) {
        throw $this->createAccessDeniedException('Accès refusé');
    }
    
    $cotisationzone = $cotisationzoneRepo->find($id);
    if (!$cotisationzone) {
        $this->addFlash('danger', 'Cotisation non trouvée');
        return $this->redirectToRoute('app_cotisationzone_index');
    }
    
    $zone = $cotisationzone->getZone();
    $membres = $zone ? $fideleRepository->findBy(['zone' => $zone, 'deletedAt' => NULL]) : [];
    $nbMembres = count($membres);
    $montantUnitaire = $cotisationzone->getMontant() ?? 0;
    $montantTotalPrevu = $nbMembres * $montantUnitaire;
    
    // Récupérer tous les paiements
    $listeCotiserzone = $cotiserzoneRepository->findBy(
        ['cotisationzone' => $cotisationzone, 'deletedAt' => NULL],
        ['datecotiser' => 'DESC']
    );
    
    $totalPaye = 0;
    foreach ($listeCotiserzone as $paiement) {
        $totalPaye += $paiement->getMontantpayer() ?? 0;
    }
    $totalReste = $montantTotalPrevu - $totalPaye;
    
    // Paiements par fidèle
    $paiementsParFidele = [];
    foreach ($listeCotiserzone as $paiement) {
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
    
    return $this->render('cotisationzone/detail.html.twig', [
        'cotisationzone' => $cotisationzone,
        'cotiserzones' => $listeCotiserzone,
        'totalPaye' => $totalPaye,
        'totalReste' => $totalReste,
        'montantTotalPrevu' => $montantTotalPrevu,
        'montantUnitaire' => $montantUnitaire,
        'nbMembres' => $nbMembres,
        'nbPaiements' => count($listeCotiserzone),
        'statsParFidele' => $statsParFidele,
    ]);
}

       #[Route('/detail-paiement/{id}', name: 'detail_paiement_zone', methods: ['POST'])]
public function detailPaiement(int $id, DetailcotisationzoneRepository $detailcotisationzoneRepository, CotiserzoneRepository $cotiserzoneRepository ): Response
{
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    
    // Alternative: chercher par cotisationzone_id et fidele_id
    $cotiserzone = $cotiserzoneRepository->find($id);
    
    if (!$cotiserzone) {
        return $this->json(['error' => 'Paiement non trouvé'], 404);
    }
    
    // Chercher les détails par cotisationzone et fidele
    $details = $detailcotisationzoneRepository->findBy([
        'cotisationzone' => $cotiserzone->getCotisationzone(),
        'fidele' => $cotiserzone->getFidele(),
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
    
    return $this->render('cotisationzone/_detail_paiement_modal.html.twig', [
        'details' => $details,
        'cotiserzone' => $cotiserzone,
        'totalMontant' => $totalMontant,
        'totalPaye' => $totalPaye,
        'totalReste' => $totalReste,
    ]);
}

    #[Route('/{id}', name: 'app_cotisationzone_show', methods: ['GET'])]
    public function show(Cotisationzone $cotisationzone): Response {
        if (!$this->isGranted('ROLE_RESPONSABLE_ZONE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }

        return $this->render('cotisationzone/show.html.twig', [
                    'cotisationzone' => $cotisationzone,
        ]);
    }

    #[Route('cotisationzone/{id}', name: 'app_cotisationzone_delete', methods: ['POST'])]
    public function delete(Request $request, Cotisationzone $cotisationzone, CotisationzoneRepository $cotisationzoneRepository): Response {
        if (!$this->isGranted('ROLE_RESPONSABLE_ZONE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($this->isCsrfTokenValid('delete' . $cotisationzone->getId(), $request->request->get('_token'))) {
            $cotisationzoneRepository->remove($cotisationzone, true);
        }

        $this->addFlash('suppcotisationzone', 'Supression avec succès');

        return $this->redirectToRoute('app_cotisationzone_index', [], Response::HTTP_SEE_OTHER);
    }

}
