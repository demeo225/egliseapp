<?php

namespace App\Controller;

use App\Entity\Depensefamille;
use App\Entity\Soldefamille;
use App\Form\DepensefamilleType;
use App\Form\UpdatedepensefamilleType;
use App\Repository\DepensefamilleRepository;
use App\Repository\FamilleRepository;
use App\Repository\SoldefamilleRepository;
use App\Traits\ClientIp;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/depensefamille')]
class DepensefamilleController extends AbstractController {

    use ClientIp;

// Dans DepensefamilleController.php

#[Route('/', name: 'app_depensefamille_index', methods: ['GET'])]
public function index(DepensefamilleRepository $depensefamilleRepository, SoldefamilleRepository $soldeRepo, FamilleRepository $familleRepo): Response {
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    
    $user = $this->getUser();
    $eglise = $user->getEglise();
    
    // Construction de la requête selon le rôle
    $qb = $depensefamilleRepository->createQueryBuilder('d')
        ->leftJoin('d.famille', 'f')
        ->leftJoin('f.zone', 'z')
        ->where('d.eglise = :eglise')
        ->andWhere('d.deletedAt IS NULL')
        ->setParameter('eglise', $eglise);
    
    // Filtres selon les rôles
    if ($this->isGranted('ROLE_ADMIN') || $this->isGranted('ROLE_PASTEUR') || $this->isGranted('ROLE_SECRETAIRE')) {
        // Ces rôles voient toutes les dépenses
        // Pas de filtre supplémentaire
    } 
    elseif ($this->isGranted('ROLE_RESPONSABLE_ZONE')) {
        $zone = $user->getZone();
        if ($zone) {
            $qb->andWhere('z.id = :zoneId')
               ->setParameter('zoneId', $zone->getId());
        } else {
            $this->addFlash('warning', 'Aucune zone associée à votre compte.');
            return $this->redirectToRoute('app_home');
        }
    }
    elseif ($this->isGranted('ROLE_RESPONSABLE_FAMILLE')) {
        $famille = $user->getFamille();
        if ($famille) {
            $qb->andWhere('f.id = :familleId')
               ->setParameter('familleId', $famille->getId());
        } else {
            $this->addFlash('warning', 'Aucune famille associée à votre compte.');
            return $this->redirectToRoute('app_home');
        }
    }
    else {
        $this->addFlash('error', 'Vous n\'avez pas les droits pour voir les dépenses.');
        return $this->redirectToRoute('app_home');
    }
    
    // Exécution de la requête
    $depenses = $qb->orderBy('d.datedepense', 'DESC')->getQuery()->getResult();
    
    // Récupération du solde pour la famille (uniquement si l'utilisateur a une famille)
    $solde = [];
    $famille = $user->getFamille();
    if ($famille) {
        $famille2 = $familleRepo->findOneFamille($famille);
        if ($famille2) {
            $solde = $soldeRepo->findBy(['famille' => $famille2]);
        }
    }
    
    return $this->render('depensefamille/index.html.twig', [
        'depensefamilles' => $depenses,
        'soldes' => $solde,
    ]);
}
    #[Route('/new', name: 'app_depensefamille_new', methods: ['GET', 'POST'])]
    public function new(Request $request, DepensefamilleRepository $depensefamilleRepository, EntityManagerInterface $entityManager, FamilleRepository $familleRepo, SoldefamilleRepository $soldeRepo): Response {
        if (!$this->isGranted('ROLE_RESPONSABLE_FAMILLE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $depensefamille = new Depensefamille();

     $user = $this->getUser();
     $eglise = $user->getEglise();
                    //Recuperer le groupe et les membres
            $famille = $familleRepo->findOneByUser($user);
         if (!$famille) {
            $this->addFlash('warning', 'Vous ne disposez pas de Famille à gérer.');
            return $this->redirectToRoute('app_depensefamille_index');
        }
      

        $form = $this->createForm(DepensefamilleType::class, $depensefamille,);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $offrande = $form['montant']->getData();
            $idgpe = $familleRepo->findOneByUser($user);

            $dql2 = $soldeRepo->findBy(['famille' => $idgpe]);
            if ($dql2) {
                $id = $dql2[0]->getId();
                $activite = $soldeRepo->findOneBySoldeFamille($id);
                $mont = $activite->getMontant();
                $j = 0;
                $j = $mont - $offrande;
                $activite->setMontant($j);
            } else {

                $offrandeSole = new Soldefamille();
                $off = 0 - $offrande;
                $offrandeSole->setMontant($off);
                $offrandeSole->setFamille($idgpe);
                $entityManager->persist($offrandeSole);
            }


            $depensefamille->setEglise($eglise);
            $depensefamille->setFamille($idgpe);
            $depensefamille->setDeletedBy($user);
            $depensefamille->setDeletedFromIp($this->GetIp());
            $depensefamilleRepository->add($depensefamille);
            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'app_depensefamille_new' : 'app_depensefamille_index';
            if ($nextAction) {
                $this->addFlash('success', 'Action effectuée avec succès.');
            }

            return $this->redirectToRoute($nextAction);
//            return $this->redirectToRoute('app_depensefamille_index', [], Response::HTTP_SEE_OTHER);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('depensefamille/new.html.twig', [
                    'depensefamille' => $depensefamille,
                    'famille' => $famille,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    #[Route('/{id}', name: 'app_depensefamille_show', methods: ['GET'])]
    public function show(Depensefamille $depensefamille): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        return $this->render('depensefamille/show.html.twig', [
                    'depensefamille' => $depensefamille,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_depensefamille_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Depensefamille $depensefamille, FamilleRepository $familleRepo, EntityManagerInterface $entityManager, DepensefamilleRepository $depensefamilleRepository, SoldefamilleRepository $soldeRepo): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_FAMILLE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici! Veuillez vous adresser au secretariat');
        }

        $user = $this->getUser();
        $famille = $familleRepo->findOneByUser($user);
         if (!$famille) {
            $this->addFlash('warning', 'Vous ne disposez pas de Famille à gérer.');
            return $this->redirectToRoute('app_depensefamille_index');
        }
      
        $form = $this->createForm(UpdatedepensefamilleType::class, $depensefamille,);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $idgpe = $familleRepo->findOneByUser($user);

            $depensefamille->setUpdatedFromIp($this->GetIp());
//            $user = $this->getUser();
            $depensefamille->setUpdatedBy($user);

            $nature = $form['typeoff']->getData();

            if ($nature == 1) {
                $valeur = $form['ajout']->getData();

                $user = $this->getUser();

                $mont1 = $depensefamille->getMontant();
                $mon = $valeur + $mont1;
                $depensefamille->setMontant($mon);
                // On retranche montant au solde si la depense augmente et on ajoute si la depense diminue

                $dql2 = $soldeRepo->findBy(['famille' => $idgpe]);
                if ($dql2) {
                    $id = $dql2[0]->getId();
                    $activite = $soldeRepo->findOneBySoldeFamille($id);
                    $mont = $activite->getMontant();
                    $j = 0;
                    $j = $mont - $valeur;
                    $activite->setMontant($j);
                } else {

                    $offrandeSole = new Soldefamille();
                    $off = 0 - $valeur;
                    $offrandeSole->setMontant($off);
                    $offrandeSole->setFamille($idgpe);
                    $entityManager->persist($offrandeSole);
                }
            } elseif ($nature == 0) {
                // $valeur2 = $form['ajout']->getData();
                //$user = $this->getUser();
                $valeur2 = $form['ajout']->getData();
                $mont3 = $depensefamille->getMontant();
                $mon0 = $mont3 - $valeur2;
                $depensefamille->setMontant($mon0);
                $dql2 = $soldeRepo->findBy(['famille' => $idgpe]);
                if ($dql2) {
                    $id = $dql2[0]->getId();
                    $activite = $soldeRepo->findOneBySoldeFamille($id);
                    $mont = $activite->getMontant();
                    $j = 0;
                    $j = $mont + $valeur2;
                    $activite->setMontant($j);
                } else {

                    $offrandeSole = new Soldefamille();
                    $offrandeSole->setMontant($valeur2);
                    $offrandeSole->setFamille($idgpe);

                    $entityManager->persist($offrandeSole);
                }
            }
             $this->addFlash('success', 'Modification effectuée avec succès.');
            $depensefamilleRepository->add($depensefamille);
            return $this->redirectToRoute('app_depensefamille_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('depensefamille/edit.html.twig', [
                    'depensefamille' => $depensefamille,
                    'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_depensefamille_delete', methods: ['POST'])]
    public function delete(Request $request, Depensefamille $depensefamille, FamilleRepository $familleRepo, SoldefamilleRepository $soldeRepo): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_FAMILLE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici! Veuillez vous adresser au secretariat');
        }
        if ($this->isCsrfTokenValid('delete' . $depensefamille->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();
            $famille = $this->getUser()->getFamille();
            $famille2 = $familleRepo->findOneFamille($famille);
            $dql = $soldeRepo->findBy(['famille' => $famille2]);

            $total = $depensefamille->getMontant();
            if ($dql) {
                $id = $dql[0]->getId();
                $activite = $soldeRepo->findOneBySoldeFamille($id);
                $mont = $activite->getMontant();
                $j = 0;
                $j = $mont + $total;
                $activite->setMontant($j);
            }

            $depensefamille->setDeletedFromIp($this->GetIp());
            $depensefamille->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $this->addLink('danger', 'Suppression avec succès');
            $depensefamille->setDeletedBy($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_depensefamille_index', [], Response::HTTP_SEE_OTHER);
    }

}
