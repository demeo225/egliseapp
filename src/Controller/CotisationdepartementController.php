<?php

namespace App\Controller;

use App\Entity\Cotisationdepartement;
use App\Form\CotisationdepartementType;
use App\Repository\CotisationdepartementRepository;
use App\Repository\CotiserdepartementRepository;
use App\Repository\DepartementRepository;
use App\Repository\SoldedepartementRepository;
use App\Repository\FideleRepository;

use App\Traits\ClientIp;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cotisationdepartement')]
class CotisationdepartementController extends AbstractController {

    use ClientIp;

    #[Route('/', name: 'cotisationdepartement_index', methods: ['GET'])]
    public function index(CotisationdepartementRepository $cotisationdepartementRepository, DepartementRepository $departementRepo, SoldedepartementRepository $soldeRepo, ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_DEPARTEMENT')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
            $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $departement = $this->getUser()->getDepartement();
        $departement2 = $departementRepo->findOneDepartement($departement);
        $solde = $soldeRepo->findBy(['departement' => $departement2]);
        $cotisationdepartement = $cotisationdepartementRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        return $this->render('cotisationdepartement/index.html.twig', [
             'cotisationdepartements' => $cotisationdepartement,
            'soldes' =>$solde,
        ]);
    }

    #[Route('/{id}/edit', name: 'cotisationdepartement_edit', methods: ['GET', 'POST'])]
    #[Route('/new', name: 'cotisationdepartement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, DepartementRepository $departementRepository, ?Cotisationdepartement $cotisationdepartement = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_DEPARTEMENT')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }

        $type = $cotisationdepartement === null ? 'new' : 'edit';
        $cotisationdepartement = $cotisationdepartement === null ? new Cotisationdepartement() : $cotisationdepartement;
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
       // $departement = $user->getDepartement();
         $departement = $departementRepository->findOneByUser($user);
             if (!$departement) {
            $this->addFlash('warning', 'Vous ne disposez pas de Famille à gérer.');
            return $this->redirectToRoute('cotisationdepartement_index');
        }
        $form = $this->createForm(CotisationdepartementType::class, $cotisationdepartement,);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //Adresse ip de l'utilisateur
            if ($type === 'new') {
                $cotisationdepartement->setCreatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setEglise($user->getEglise())
                        ->setCreatedBy($user)
                        ->setEtatcotiser(1)

                ;
            } else {
                $cotisationdepartement->setUpdatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setUpdatedBy($user)
                ;
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($cotisationdepartement);
            $entityManager->flush();

            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'cotisationdepartement_new' : 'cotisationdepartement_index';
            if ($nextAction) {
                $this->addFlash('success', 'Action effectuée avec succès.');
            }

            return $this->redirectToRoute($nextAction);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('cotisationdepartement/new.html.twig', [
                    'cotisationdepartement' => $cotisationdepartement,
                    'departement' => $departement,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

#[Route('/cotiser/{id}', name: 'cotisationdepartement_cotiser', methods: ['GET'])]
public function detailCotisationdepartement(
    int $id,
    CotiserdepartementRepository $cotiserdepartementRepository,
    CotisationdepartementRepository $cotisationdepartementRepo,
    FideleRepository $fideleRepository,
    DepartementRepository $departementRepository
): Response {
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    
    if (!$this->isGranted('ROLE_RESPONSABLE_DEPARTEMENT')) {
        throw $this->createAccessDeniedException('Accès refusé, vous n\'avez pas les droits d\'accès ici!');
    }
    
    // Récupérer la cotisation
    $cotisationdepartement = $cotisationdepartementRepo->find($id);
    
    if (!$cotisationdepartement) {
        $this->addFlash('danger', 'Cotisation non trouvée');
        return $this->redirectToRoute('cotisationdepartement_index');
    }
    
    // Récupérer le département
    $departement = $cotisationdepartement->getDepartement();
    
    // Récupérer les membres du département (via la fonction dédiée)
    $membres = [];
    $nbMembres = 0;
    if ($departement) {
        $membres = $fideleRepository->findFidelesByDepartement($departement->getId());
        $nbMembres = count($membres);
    }
    
    // Montant unitaire de la cotisation
    $montantUnitaire = $cotisationdepartement->getMontant() ?? 0;
    $montantTotalPrevu = $nbMembres * $montantUnitaire;
    
    // Récupérer tous les paiements (Cotiserdepartement)
    $listeCotiserdepartement = $cotiserdepartementRepository->findBy(
        ['cotisationdepartement' => $cotisationdepartement, 'deletedAt' => NULL],
        ['datecotiser' => 'DESC']
    );
    
    // Calculer le total payé
    $totalPaye = 0;
    foreach ($listeCotiserdepartement as $paiement) {
        $totalPaye += $paiement->getMontantpayer() ?? 0;
    }
    
    $totalReste = $montantTotalPrevu - $totalPaye;
    
    // Organiser les paiements par fidèle
    $paiementsParFidele = [];
    foreach ($listeCotiserdepartement as $paiement) {
        $fideleId = $paiement->getFidele() ? $paiement->getFidele()->getId() : null;
        if ($fideleId) {
            if (!isset($paiementsParFidele[$fideleId])) {
                $paiementsParFidele[$fideleId] = [
                    'montant_total' => 0,
                    'paiements' => []
                ];
            }
            $paiementsParFidele[$fideleId]['montant_total'] += $paiement->getMontantpayer();
            $paiementsParFidele[$fideleId]['paiements'][] = $paiement;
        }
    }
    
    // Statistiques par fidèle
    $statsParFidele = [];
    foreach ($membres as $membre) {
        $aPaye = isset($paiementsParFidele[$membre->getId()]);
        $montantPaye = $aPaye ? $paiementsParFidele[$membre->getId()]['montant_total'] : 0;
        
        $statsParFidele[] = [
            'fidele' => $membre,
            'a_paye' => $aPaye,
            'montant_paye' => $montantPaye,
            'reste' => $montantUnitaire - $montantPaye,
            'nb_paiements' => $aPaye ? count($paiementsParFidele[$membre->getId()]['paiements']) : 0
        ];
    }
    
    return $this->render('cotisationdepartement/detail.html.twig', [
        'cotisationdepartement' => $cotisationdepartement,
        'cotiserdepartements' => $listeCotiserdepartement,
        'totalPaye' => $totalPaye,
        'totalReste' => $totalReste,
        'montantTotalPrevu' => $montantTotalPrevu,
        'montantUnitaire' => $montantUnitaire,
        'nbMembres' => $nbMembres,
        'nbPaiements' => count($listeCotiserdepartement),
        'membres' => $membres,
        'statsParFidele' => $statsParFidele,
        'departement' => $departement,
    ]);
}

    #[Route('/{id}', name: 'cotisationdepartement_show', methods: ['GET'])]
    public function show(Cotisationdepartement $cotisationdepartement): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_DEPARTEMENT')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        return $this->render('cotisationdepartement/show.html.twig', [
                    'cotisationdepartement' => $cotisationdepartement,
        ]);
    }

      /**
     * Action unique pour activer ou clôturer une cotisation
     */
    #[Route('/{id}/toggle', name: 'cotisationdepartement_toggle', methods: ['POST'])]
    public function toggle(Request $request, Cotisationdepartement $cotisationdepartement): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        // Vérifier les droits
        if (!$this->isGranted('ROLE_RESPONSABLE_DEPARTEMENT') && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Accès refusé, vous n\'avez pas les droits d\'accès ici!');
        }
        
        // Vérifier le token CSRF
        if ($this->isCsrfTokenValid('toggle' . $cotisationdepartement->getId(), $request->request->get('_token'))) {
            
            $entityManager = $this->getDoctrine()->getManager();
            $user = $this->getUser();
            
            // Basculer l'état (1 -> 0 ou 0 -> 1)
            $nouvelEtat = $cotisationdepartement->getEtatcotiser() == 1 ? 0 : 1;
            $cotisationdepartement->setEtatcotiser($nouvelEtat);
            
            // Mettre à jour les informations de modification
            $cotisationdepartement->setUpdatedFromIp($this->getIp())
                ->setUpdatedBy($user);
                
            
            $entityManager->flush();
            
            // Message personnalisé selon l'action
            if ($nouvelEtat == 1) {
                $this->addFlash('success', 'Cotisation réactivée avec succès.');
            } else {
                $this->addFlash('success', 'Cotisation clôturée avec succès.');
            }
        } else {
            $this->addFlash('error', 'Token CSRF invalide.');
        }
        
        return $this->redirectToRoute('cotisationdepartement_index', [], Response::HTTP_SEE_OTHER);
    } 

    #[Route('/{id}', name: 'cotisationdepartement_delete', methods: ['POST'])]
    public function delete(Request $request, Cotisationdepartement $cotisationdepartement, CotisationdepartementRepository $cotisationdepartementRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($this->isCsrfTokenValid('delete' . $cotisationdepartement->getId(), $request->request->get('_token'))) {
            $cotisationdepartementRepository->remove($cotisationdepartement, true);
        }
        $this->addFlash('danger', 'Supression avec succès');

        return $this->redirectToRoute('cotisationdepartement_index', [], Response::HTTP_SEE_OTHER);
    }

}
