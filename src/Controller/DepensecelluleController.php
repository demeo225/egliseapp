<?php

namespace App\Controller;

use App\Entity\Depensecellule;
use App\Entity\Solecellule;
use App\Form\DepensecelluleType;
use App\Form\UpdatedepensecelluleType;
use App\Repository\CelluleRepository;
use App\Repository\DepensecelluleRepository;
use App\Repository\SolecelluleRepository;
use App\Traits\ClientIp;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/depensecellule')]
class DepensecelluleController extends AbstractController {

    use ClientIp;

    #[Route('/', name: 'app_depensecellule_index', methods: ['GET'])]
    public function index(DepensecelluleRepository $depensecelluleRepository, SolecelluleRepository $soldeRepo, CelluleRepository $celluleRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        $user = $this->getUser();
        $eglise = $user->getEglise();
        
        // Construction de la requête selon le rôle
        $qb = $depensecelluleRepository->createQueryBuilder('d')
            ->leftJoin('d.cellule', 'c')
            ->leftJoin('c.zone', 'z')
            ->where('d.eglise = :eglise')
            ->andWhere('d.deletedAt IS NULL')
            ->setParameter('eglise', $eglise);
        
        // Filtres selon les rôles
        if ($this->isGranted('ROLE_ADMIN') || $this->isGranted('ROLE_PASTEUR') || $this->isGranted('ROLE_SECRETAIRE')) {
            // Ces rôles voient toutes les dépenses
        } 
        elseif ($this->isGranted('ROLE_RESPONSABLE_ZONE')) {
            $zone = $user->getZone();
            if ($zone) {
                $qb->andWhere('z.id = :zoneId')
                   ->setParameter('zoneId', $zone->getId());
            } else {
                $this->addFlash('warning', 'Aucune zone associée à votre compte.');
                return $this->redirectToRoute('home');
            }
        }
        elseif ($this->isGranted('ROLE_RESPONSABLE_CELLULE')) {
            $cellule = $user->getCellule();
            if ($cellule) {
                $qb->andWhere('c.id = :celluleId')
                   ->setParameter('celluleId', $cellule->getId());
            } else {
                $this->addFlash('warning', 'Aucune cellule associée à votre compte.');
                return $this->redirectToRoute('home');
            }
        }
        else {
            $this->addFlash('error', 'Vous n\'avez pas les droits pour voir les dépenses.');
            return $this->redirectToRoute('home');
        }
        
        $depenses = $qb->orderBy('d.datedepense', 'DESC')->getQuery()->getResult();

              $user = $this->getUser();
              $cellule = $user->getCellule();
        $solde = $soldeRepo->findBy(['cellule' => $cellule]);
        
        return $this->render('depensecellule/index.html.twig', [
            'depensecellules' => $depenses,
             'soldes' => $solde,
        ]);
    }

    #[Route('/new', name: 'app_depensecellule_new', methods: ['GET', 'POST'])]
    public function new(Request $request, DepensecelluleRepository $depensecelluleRepository, EntityManagerInterface $entityManager, CelluleRepository $celluleRepo, SolecelluleRepository $soldeRepo): Response {
        if (!$this->isGranted('ROLE_RESPONSABLE_CELLULE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $depensecellule = new Depensecellule();
        $user = $this->getUser();

        $eglise = $this->getUser()->getEglise();
          $cellule = $celluleRepo->findOneByUser($user);
         if (!$cellule) {
            $this->addFlash('warning', 'Vous ne disposez pas de cellule à gérer.');
            return $this->redirectToRoute('app_depensecellule_index');
        }

        $form = $this->createForm(DepensecelluleType::class, $depensecellule,);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $idgpe = $celluleRepo->findOneByUser($user);

            $offrande = $form['montant']->getData();

            // le solde est reduit si existe et est créé puis à moins si non existant
            $dql2 = $soldeRepo->findBy(['cellule' => $idgpe]);
            if ($dql2) {
                $id = $dql2[0]->getId();
                $activite = $soldeRepo->findOneBySoldeCellule($id);
                $mont = $activite->getMontant();
                $j = 0;
                $j = $mont - $offrande;
                $activite->setMontant($j);
            } else {

                $offrandeSole = new Solecellule();
                $off = 0 - $offrande;
                $offrandeSole->setMontant($off);
                $offrandeSole->setCellule($idgpe);
                $entityManager->persist($offrandeSole);

             
            }
               $depensecellule->setCreatedBy($user);
               $depensecellule->setCellule($user->getCellule());
               $depensecellule->setEglise($eglise);
                $depensecellule->setCreatedFromIp($this->GetIp());
                $depensecelluleRepository->add($depensecellule);
            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'app_depensecellule_new' : 'app_depensecellule_index';
            if ($nextAction) {
                $this->addFlash('enregdepensecellule', 'Action effectuée avec succès.');
            }

            return $this->redirectToRoute($nextAction);
//            return $this->redirectToRoute('app_depensecellule_index', [], Response::HTTP_SEE_OTHER);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('depensecellule/new.html.twig', [
                    'depensecellule' => $depensecellule,
                    'cellule' => $cellule,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    #[Route('/{id}', name: 'app_depensecellule_show', methods: ['GET'])]
    public function show(Depensecellule $depensecellule): Response {
        return $this->render('depensecellule/show.html.twig', [
                    'depensecellule' => $depensecellule,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_depensecellule_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Depensecellule $depensecellule, CelluleRepository $celluleRepo, EntityManagerInterface $entityManager, DepensecelluleRepository $depensecelluleRepository, SolecelluleRepository $soldeRepo): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_CELLULE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici! Veuillez vous adresser au secretariat');
        }
        $user = $this->getUser();
            $zone = $celluleRepo->findOneByUser($user);
         if (!$zone) {
            $this->addFlash('warning', 'Vous ne disposez pas de zone/secteur à gérer.');
            return $this->redirectToRoute('app_depensecellule_index');
        } 

        $form = $this->createForm(UpdatedepensecelluleType::class, $depensecellule,);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
        
            $idgpe = $depensecellule->getCellule();

            $depensecellule->setUpdatedFromIp($this->GetIp());
//            $user = $this->getUser();
            $depensecellule->setUpdatedBy($user);

            $nature = $form['typeoff']->getData();

            if ($nature == 1) {
                $valeur = $form['ajout']->getData();

                $user = $this->getUser();

                $mont1 = $depensecellule->getMontant();
                $mon = $valeur + $mont1;
                $depensecellule->setMontant($mon);
                // On retranche montant au solde si la depense augmente et on ajoute si la depense diminue

                $dql2 = $soldeRepo->findBy(['cellule' => $idgpe]);
                if ($dql2) {
                    $id = $dql2[0]->getId();
                    $activite = $soldeRepo->findOneBySoldeCellule($id);
                    $mont = $activite->getMontant();
                    $j = 0;
                    $j = $mont - $valeur;
                    $activite->setMontant($j);
                } else {

                    $offrandeSole = new Solecellule();
                    $off = 0 - $valeur;
                    $offrandeSole->setMontant($off);
                    $offrandeSole->setCellule($idgpe);
                    $entityManager->persist($offrandeSole);
                }
            } elseif ($nature == 0) {
                // $valeur2 = $form['ajout']->getData();
                //$user = $this->getUser();
                $valeur2 = $form['ajout']->getData();
                $mont3 = $depensecellule->getMontant();
                $mon0 = $mont3 - $valeur2;
                $depensecellule->setMontant($mon0);
                $dql2 = $soldeRepo->findBy(['cellule' => $idgpe]);
                if ($dql2) {
                    $id = $dql2[0]->getId();
                    $activite = $soldeRepo->findOneBySoldeCellule($id);
                    $mont = $activite->getMontant();
                    $j = 0;
                    $j = $mont + $valeur2;
                    $activite->setMontant($j);
                } else {

                    $offrandeSole = new Solecellule();
                    $offrandeSole->setMontant($valeur2);
                    $offrandeSole->setCellule($idgpe);
                    $entityManager->persist($offrandeSole);
                }
            }
            $depensecelluleRepository->add($depensecellule);
            return $this->redirectToRoute('app_depensecellule_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('depensecellule/edit.html.twig', [
                    'depensecellule' => $depensecellule,
                    'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_depensecellule_delete', methods: ['POST'])]
    public function delete(Request $request, Depensecellule $depensecellule, CelluleRepository $celluleRepo, SolecelluleRepository $soldeRepo): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_CELLULE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici! Veuillez vous adresser au secretariat');
        }
        if ($this->isCsrfTokenValid('delete' . $depensecellule->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();
            $cellule = $this->getUser()->getCellule();
            $cellule2 = $celluleRepo->findOneCellule($cellule);
            $dql = $soldeRepo->findBy(['cellule' => $cellule2]);

            $total = $depensecellule->getMontant();
            if ($dql) {
                $id = $dql[0]->getId();
                $activite = $soldeRepo->findOneBySoldeCellule($id);
                $mont = $activite->getMontant();
                $j = 0;
                $j = $mont + $total;
                $activite->setMontant($j);
            }

            $depensecellule->setDeletedFromIp($this->GetIp());
            $depensecellule->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $this->addFlash('suppressiondepensecellule', 'Suppression avec succès');
            $depensecellule->setDeletedBy($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_depensecellule_index', [], Response::HTTP_SEE_OTHER);
    }

}
