<?php

namespace App\Controller;

use App\Entity\Cotisationfamille;
use App\Form\CotisationfamilleType;
use App\Repository\CotisationfamilleRepository;
use App\Repository\CotiserfamilleRepository;
use App\Repository\FamilleRepository;
use App\Repository\SoldefamilleRepository;
use App\Repository\FideleRepository;

use App\Traits\ClientIp;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cotisationfamille')]

class CotisationfamilleController extends AbstractController {
    use ClientIp;
    
    #[Route('/', name: 'cotisationfamille_index', methods: ['GET'])]

    public function index(CotisationfamilleRepository $cotisationfamilleRepository, FamilleRepository $familleRepo, SoldefamilleRepository $soldeRepo): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_FAMILLE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
             $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $famille = $this->getUser()->getFamille();
        $famille2 = $familleRepo->findOneFamille($famille);
        $solde = $soldeRepo->findBy(['famille' => $famille2]);
        $cotisationfamille = $cotisationfamilleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        return $this->render('cotisationfamille/index.html.twig', [
                    'cotisationfamilles' => $cotisationfamille,
            'soldes' =>$solde,
        ]);
    }

    #[Route('/new', name: 'cotisationfamille_new', methods: ['GET', 'POST'])]
    #[Route('/{id}/edit', name: 'cotisationfamille_edit', methods: ['GET', 'POST'])]
    public function new(Request $request, FamilleRepository $familleRepository, ?Cotisationfamille $cotisationfamille=null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_FAMILLE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
              $user = $this->getUser();
                    //Recuperer le groupe et les membres
            $famille = $user->getFamille();
         if (!$famille) {
            $this->addFlash('warning', 'Vous ne disposez pas de Famille à gérer.');
            return $this->redirectToRoute('cotisationfamille_index');
        }
        $type = $cotisationfamille === null ? 'new' : 'edit';
        $cotisationfamille = $cotisationfamille === null ? new Cotisationfamille() : $cotisationfamille;
        $eglise = $this->getUser()->getEglise();
        $famille = $user->getFamille();
        $form = $this->createForm(CotisationfamilleType::class, $cotisationfamille, );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //Adresse ip de l'utilisateur
              //Adresse ip de l'utilisateur
            if ($type === 'new') {
                $cotisationfamille->setCreatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setEglise($user->getEglise())
                        ->setCreatedBy($user)
                        ->setEtatcotiser("1")
                ;
            } else {
                $cotisationfamille->setUpdatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setUpdatedBy($user)
                ;
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($cotisationfamille);
            $entityManager->flush();

            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'cotisationfamille_new' : 'cotisationfamille_index';
            if ($nextAction) {
                $this->addFlash('success', 'Action effectuée avec succès.');
            }
 
            return $this->redirectToRoute($nextAction);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('cotisationfamille/new.html.twig', [
                    'cotisationfamille' => $cotisationfamille,
                    'famille' => $famille,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    
           

#[Route('/cotiser/{id}', name: 'cotisationfamille_cotiser', methods: ['GET'])]
public function detailCotisationfamille(
    int $id,
    CotiserfamilleRepository $cotiserfamilleRepository,
    CotisationfamilleRepository $cotisationfamilleRepo,
    FideleRepository $fideleRepository,
    FamilleRepository $familleRepository
): Response {
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    
    if (!$this->isGranted('ROLE_RESPONSABLE_FAMILLE')) {
        throw $this->createAccessDeniedException('Accès refusé, vous n\'avez pas les droits d\'accès ici!');
    }
    
    // Récupérer la cotisation
    $cotisationfamille = $cotisationfamilleRepo->find($id);
    
    if (!$cotisationfamille) {
        $this->addFlash('danger', 'Cotisation non trouvée');
        return $this->redirectToRoute('cotisationfamille_index');
    }
    
    // Récupérer la famille
    $famille = $cotisationfamille->getFamille();
    
    // Compter le nombre de membres de la famille
    $nbMembres = 0;
    $membres = [];
    if ($famille) {
        $membres = $fideleRepository->findBy(['famille' => $famille, 'deletedAt' => NULL]);
        $nbMembres = count($membres);
    }
    
    // Calculer le montant prévu réel (nbMembres * montantCotisation)
    $montantCotisationUnitaire = $cotisationfamille->getMontant() ?? 0;
    $montantTotalPrevu = $nbMembres * $montantCotisationUnitaire;
    
    // Récupérer tous les paiements (Cotiserfamille) pour cette cotisation
    $listeCotiserfamille = $cotiserfamilleRepository->findBy(
        ['cotisationfamille' => $cotisationfamille, 'deletedAt' => NULL],
        ['datecotiser' => 'DESC']
    );
    
    // Calculer les totaux des paiements
    $totalPaye = 0;
    foreach ($listeCotiserfamille as $paiement) {
        $totalPaye += $paiement->getMontantpayer() ?? 0;
    }
    
    // Calculer le reste à payer
    $totalReste = $montantTotalPrevu - $totalPaye;
    
    // Pour chaque fidèle, calculer s'il a payé ou non
    $paiementsParFidele = [];
    foreach ($listeCotiserfamille as $paiement) {
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
            'reste' => $montantCotisationUnitaire - $montantPaye
        ];
    }
    
    return $this->render('cotisationfamille/detail.html.twig', [
        'cotisationfamille' => $cotisationfamille,
        'cotiserfamilles' => $listeCotiserfamille,
        'totalPaye' => $totalPaye,
        'totalReste' => $totalReste,
        'montantTotalPrevu' => $montantTotalPrevu,
        'montantUnitaire' => $montantCotisationUnitaire,
        'nbMembres' => $nbMembres,
        'nbPaiements' => count($listeCotiserfamille),
        'membres' => $membres,
        'statsParFidele' => $statsParFidele,
        'famille' => $famille,
    ]);
}

    
    #[Route('/{id}/toggle', name: 'cotisationfamille_toggle', methods: ['POST'])]
    public function toggle(Request $request, Cotisationfamille $cotisationfamille): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        // Vérifier les droits
        if (!$this->isGranted('ROLE_RESPONSABLE_DEPARTEMENT') && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Accès refusé, vous n\'avez pas les droits d\'accès ici!');
        }
        
        // Vérifier le token CSRF
        if ($this->isCsrfTokenValid('toggle' . $cotisationfamille->getId(), $request->request->get('_token'))) {
            
            $entityManager = $this->getDoctrine()->getManager();
            $user = $this->getUser();
            
            // Basculer l'état (1 -> 0 ou 0 -> 1)
            $nouvelEtat = $cotisationfamille->getEtatcotiser() == 1 ? 0 : 1;
            $cotisationfamille->setEtatcotiser($nouvelEtat);
            
            // Mettre à jour les informations de modification
            $cotisationfamille->setUpdatedFromIp($this->getIp())
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
        
        return $this->redirectToRoute('cotisationfamille_index', [], Response::HTTP_SEE_OTHER);
    }
    
    
    #[Route('/{id}', name: 'cotisationfamille_show', methods: ['GET'])]
    public function show(Cotisationfamille $cotisationfamille): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_FAMILLE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        return $this->render('cotisationfamille/show.html.twig', [
                    'cotisationfamille' => $cotisationfamille,
        ]);
    }

      #[Route('cotisationfamille/{id}', name: 'cotisationfamille_delete', methods: ['POST'])]
   public function delete(Request $request, Cotisationfamille $cotisationfamille, CotisationfamilleRepository $cotisationfamilleRepository): Response
    {
         $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($this->isCsrfTokenValid('delete'.$cotisationfamille->getId(), $request->request->get('_token'))) {
            $cotisationfamilleRepository->remove($cotisationfamille, true);
        }
                $this->addFlash('danger', 'Supression avec succès');


        return $this->redirectToRoute('cotisationfamille_index', [], Response::HTTP_SEE_OTHER);
    }
    

}
