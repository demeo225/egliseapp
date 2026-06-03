<?php

namespace App\Controller;

use App\Entity\Cotisationgroupe;
use App\Form\CotisationgroupeType;
use App\Repository\CotisationgroupeRepository;
use App\Repository\CotisergroupeRepository;
use App\Repository\GroupeRepository;
use App\Repository\SoldegroupeRepository;
use App\Repository\FideleRepository;

use App\Traits\ClientIp;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cotisationgroupe')]
class CotisationgroupeController extends AbstractController {

    use ClientIp;

    #[Route('/', name: 'cotisationgroupe_index', methods: ['GET'])]
    public function index(CotisationgroupeRepository $cotisationgroupeRepository, SoldegroupeRepository $soldeRepo, GroupeRepository $groupeRepo): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_GROUPE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $groupe = $this->getUser()->getGroupe();
        $groupe2 = $groupeRepo->findOneGroupe($groupe);
        $solde = $soldeRepo->findBy(['groupe' => $groupe2]);
        $cotisationgroupe = $cotisationgroupeRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        return $this->render('cotisationgroupe/index.html.twig', [
                    'cotisationgroupes' => $cotisationgroupe,
            'soldes' =>$solde,
        ]);
    }

    #[Route('/{id}/edit', name: 'cotisationgroupe_edit', methods: ['GET', 'POST'])]
    #[Route('/new', name: 'cotisationgroupe_new', methods: ['GET', 'POST'])]
    public function new(Request $request, GroupeRepository $groupeRepository, ?Cotisationgroupe $cotisationgroupe = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_GROUPE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $user = $this->getUser();
        $type = $cotisationgroupe === null ? 'new' : 'edit';
        $cotisationgroupe = $cotisationgroupe === null ? new Cotisationgroupe() : $cotisationgroupe;

        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $groupe = $user->getGroupe();
         if (!$groupe) {
            $this->addFlash('warning', 'Vous ne disposez pas sous-groupe à gérer.');
            return $this->redirectToRoute('cotisationgroupe_index');
        }
        $form = $this->createForm(CotisationgroupeType::class, $cotisationgroupe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //Adresse ip de l'utilisateur
            //Adresse ip de l'utilisateur
            if ($type === 'new') {
                $cotisationgroupe->setCreatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setEglise($user->getEglise())
                        ->setCreatedBy($user)
                        ->setEtatcotiser("1")
                ;
            } else {
                $cotisationgroupe->setUpdatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setUpdatedBy($user)
                ;
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($cotisationgroupe);
            $entityManager->flush();

            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'cotisationgroupe_new' : 'cotisationgroupe_index';
            if ($nextAction) {
                $this->addFlash('success', 'Action effectuée avec succès.');
            }

            return $this->redirectToRoute($nextAction);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('cotisationgroupe/new.html.twig', [
                    'cotisationgroupe' => $cotisationgroupe,
                    'groupe' => $groupe,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }
//Detail perçu par cotisation

#[Route('/cotiser/{id}', name: 'cotisationgroupe_cotiser', methods: ['GET'])]
public function detailCotisationgroupe(
    int $id,
    CotisergroupeRepository $cotisergroupeRepository,
    CotisationgroupeRepository $cotisationgroupeRepo,
    FideleRepository $fideleRepository,
    GroupeRepository $groupeRepository
): Response {
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    
    if (!$this->isGranted('ROLE_RESPONSABLE_GROUPE')) {
        throw $this->createAccessDeniedException('Accès refusé, vous n\'avez pas les droits d\'accès ici!');
    }
    
    // Récupérer la cotisation
    $cotisationgroupe = $cotisationgroupeRepo->find($id);
    
    if (!$cotisationgroupe) {
        $this->addFlash('danger', 'Cotisation non trouvée');
        return $this->redirectToRoute('cotisationgroupe_index');
    }
    
    // Récupérer le groupe
    $groupe = $cotisationgroupe->getGroupe();
    
    // Récupérer les membres du groupe (via la fonction dédiée)
    $membres = [];
    $nbMembres = 0;
    if ($groupe) {
        $membres = $fideleRepository->findFidelesByGroupe($groupe->getId());
        $nbMembres = count($membres);
    }
    
    // Montant unitaire de la cotisation
    $montantUnitaire = $cotisationgroupe->getMontant() ?? 0;
    $montantTotalPrevu = $nbMembres * $montantUnitaire;
    
    // Récupérer tous les paiements (Cotisergroupe)
    $listeCotisergroupe = $cotisergroupeRepository->findBy(
        ['cotisationgroupe' => $cotisationgroupe, 'deletedAt' => NULL],
        ['datecotiser' => 'DESC']
    );
    
    // Calculer le total payé
    $totalPaye = 0;
    foreach ($listeCotisergroupe as $paiement) {
        $totalPaye += $paiement->getMontantpayer() ?? 0;
    }
    
    $totalReste = $montantTotalPrevu - $totalPaye;
    
    // Organiser les paiements par fidèle
    $paiementsParFidele = [];
    foreach ($listeCotisergroupe as $paiement) {
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
    
    return $this->render('cotisationgroupe/detail.html.twig', [
        'cotisationgroupe' => $cotisationgroupe,
        'cotisergroupes' => $listeCotisergroupe,
        'totalPaye' => $totalPaye,
        'totalReste' => $totalReste,
        'montantTotalPrevu' => $montantTotalPrevu,
        'montantUnitaire' => $montantUnitaire,
        'nbMembres' => $nbMembres,
        'nbPaiements' => count($listeCotisergroupe),
        'membres' => $membres,
        'statsParFidele' => $statsParFidele,
        'groupe' => $groupe,
    ]);
}

    #[Route('/{id}', name: 'cotisationgroupe_show', methods: ['GET'])]
    public function show(Cotisationgroupe $cotisationgroupe): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_GROUPE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        return $this->render('cotisationgroupe/show.html.twig', [
                    'cotisationgroupe' => $cotisationgroupe,
        ]);
    }

    
      /**
     * Action unique pour activer ou clôturer une cotisation
     */
    #[Route('/{id}/toggle', name: 'cotisationgroupe_toggle', methods: ['POST'])]
    public function toggle(Request $request, Cotisationgroupe $cotisationgroupe): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        // Vérifier les droits
        if (!$this->isGranted('ROLE_RESPONSABLE_GROUPE') && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Accès refusé, vous n\'avez pas les droits d\'accès ici!');
        }
        
        // Vérifier le token CSRF
        if ($this->isCsrfTokenValid('toggle' . $cotisationgroupe->getId(), $request->request->get('_token'))) {
            
            $entityManager = $this->getDoctrine()->getManager();
            $user = $this->getUser();
            
            // Basculer l'état (1 -> 0 ou 0 -> 1)
            $nouvelEtat = $cotisationgroupe->getEtatcotiser() == 1 ? 0 : 1;
            $cotisationgroupe->setEtatcotiser($nouvelEtat);
            
            // Mettre à jour les informations de modification
            $cotisationgroupe->setUpdatedFromIp($this->getIp())
                ->setUpdatedBy($user);
                
             
            $entityManager->flush();
            
            // Message personnalisé selon l'action
            if ($nouvelEtat == 1) {
                $this->addFlash('success', 'Cotisation réactivée avec succès.');
            } else {
                $this->addFlash('danger', 'Cotisation clôturée avec succès.');
            }
        } else {
            $this->addFlash('error', 'Token CSRF invalide.');
        }
        
        return $this->redirectToRoute('cotisationgroupe_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}', name: 'cotisationgroupe_delete', methods: ['POST'])]
    public function delete(Request $request, Cotisationgroupe $cotisationgroupe, CotisationgroupeRepository $cotisationgroupeRepository): Response {
        $this->denyAccessUnlessGranted('cotisationgroupe_edit', $cotisationgroupe);
        if ($this->isCsrfTokenValid('delete' . $cotisationgroupe->getId(), $request->request->get('_token'))) {
            $cotisationgroupeRepository->remove($cotisationgroupe, true);
        }
        $this->addFlash('danger', 'Supression avec succès');

        return $this->redirectToRoute('cotisationgroupe_index', [], Response::HTTP_SEE_OTHER);
    }

}
