<?php

namespace App\Controller;

use App\Entity\Depensedepartement;
use App\Entity\Soldedepartement;
use App\Form\DepensedepartementType;
use App\Form\UpdatedepensedepartementType;
use App\Repository\DepartementRepository;
use App\Repository\DepensedepartementRepository;
use App\Repository\SoldedepartementRepository;
use App\Traits\ClientIp;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/depensedepartement')]
class DepensedepartementController extends AbstractController {

    use ClientIp;

    #[Route('/', name: 'app_depensedepartement_index', methods: ['GET'])]
   public function index(DepensedepartementRepository $depensedepartementRepository, SoldedepartementRepository $soldeRepo, DepartementRepository $departementRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        $user = $this->getUser();
        $eglise = $user->getEglise();
        
        // Construction de la requête selon le rôle
        $qb = $depensedepartementRepository->createQueryBuilder('d')
            ->leftJoin('d.departement', 'c')
           // ->leftJoin('c.zone', 'z')
            ->where('d.eglise = :eglise')
            ->andWhere('d.deletedAt IS NULL')
            ->setParameter('eglise', $eglise);
        
        // Filtres selon les rôles
        if ($this->isGranted('ROLE_ADMIN') || $this->isGranted('ROLE_PASTEUR') || $this->isGranted('ROLE_SECRETAIRE')) {
            // Ces rôles voient toutes les dépenses
        } 
      
        elseif ($this->isGranted('ROLE_RESPONSABLE_DEPARTEMENT')) {
            $departement = $user->getDepartement();
            if ($departement) {
                $qb->andWhere('c.id = :departementId')
                   ->setParameter('departementId', $departement->getId());
            } else {
                $this->addFlash('warning', 'Aucune departement associée à votre compte.');
                return $this->redirectToRoute('home');
            }
        }
        else {
            $this->addFlash('error', 'Vous n\'avez pas les droits pour voir les dépenses.');
            return $this->redirectToRoute('home');
        }
        
        $depenses = $qb->orderBy('d.datedepense', 'DESC')->getQuery()->getResult();

              $user = $this->getUser();
              $departement = $user->getDepartement();
        $solde = $soldeRepo->findBy(['departement' => $departement]);
        
        return $this->render('depensedepartement/index.html.twig', [
            'depensedepartements' => $depenses,
             'soldes' => $solde,
        ]);
    }
    #[Route('/new', name: 'app_depensedepartement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, DepensedepartementRepository $depensedepartementRepository, DepartementRepository $departementRepo, SoldedepartementRepository $soldeRepo): Response {
        if (!$this->isGranted('ROLE_RESPONSABLE_DEPARTEMENT')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $depensedepartement = new Depensedepartement();

        $user = $this->getUser();
        $eglise = $user->getEglise();
             //Recuperer le groupe et les membres
        $departement = $departementRepo->findOneByUser($user);
     if (!$departement) {
        $this->addFlash('warning', 'Vous ne disposez pas de direction à gérer.');
        return $this->redirectToRoute('app_depensedepartement_index');
     }
      
        

        $form = $this->createForm(DepensedepartementType::class, $depensedepartement,);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $idgpe = $departementRepo->findOneByUser($user);

            $offrande = $form['montant']->getData();

            // le solde est reduit si existe et est créé puis à moins si non existant
            $dql2 = $soldeRepo->findBy(['departement' => $idgpe]);
            if ($dql2) {
                $id = $dql2[0]->getId();
                $activite = $soldeRepo->findOneBySoldeDepartement($id);
                $mont = $activite->getMontant();
                $j = 0;
                $j = $mont - $offrande;
                $activite->setMontant($j);
            } else {

                $offrandeSole = new Soldedepartement();
                $off = 0 - $offrande;
                $offrandeSole->setMontant($off);
                $offrandeSole->setDepartement($idgpe);
                $entityManager->persist($offrandeSole);
            }
            $depensedepartement->setEglise($eglise);
            $depensedepartement->setDepartement($idgpe);
            $depensedepartement->setCreatedBy($user);
            $depensedepartement->setCreatedFromIp($this->GetIp());
            $depensedepartementRepository->add($depensedepartement);
            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'app_depensedepartement_new' : 'app_depensedepartement_index';
            if ($nextAction) {
                $this->addFlash('success', 'Action effectuée avec succès.');
            }

            return $this->redirectToRoute($nextAction);
//            return $this->redirectToRoute('app_depensedepartement_index', [], Response::HTTP_SEE_OTHER);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('depensedepartement/new.html.twig', [
                    'depensedepartement' => $depensedepartement,
                    'departement' => $departement,
                    'partement' => $departement,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    #[Route('/{id}', name: 'app_depensedepartement_show', methods: ['GET'])]
    public function show(Depensedepartement $depensedepartement): Response {
        return $this->render('depensedepartement/show.html.twig', [
                    'depensedepartement' => $depensedepartement,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_depensedepartement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $entityManager, Depensedepartement $depensedepartement, DepartementRepository $departementRepo, DepensedepartementRepository $depensedepartementRepository, SoldedepartementRepository $soldeRepo): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_DEPARTEMENT')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici! Veuillez vous adresser au secretariat');
        }
        $user = $this->getUser(); 
      //  $departement = $departementRepo->findBy(["user" => $user, "deletedAt" => NULL]);
        $form = $this->createForm(UpdatedepensedepartementType::class, $depensedepartement,);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $idgpe = $departementRepo->findOneByUser($user);

            $depensedepartement->setUpdatedFromIp($this->GetIp());
//            $user = $this->getUser();
            $depensedepartement->setUpdatedBy($user);

            $nature = $form['typeoff']->getData();

            if ($nature == 1) {
                $valeur = $form['ajout']->getData();

                $user = $this->getUser();

                $mont1 = $depensedepartement->getMontant();
                $mon = $valeur + $mont1;
                $depensedepartement->setMontant($mon);
                // On retranche montant au solde si la depense augmente et on ajoute si la depense diminue

                $dql2 = $soldeRepo->findBy(['departement' => $idgpe]);
                if ($dql2) {
                    $id = $dql2[0]->getId();
                    $activite = $soldeRepo->findOneBySoldeDepartement($id);
                    $mont = $activite->getMontant();
                    $j = 0;
                    $j = $mont - $valeur;
                    $activite->setMontant($j);
                } else {

                    $offrandeSole = new Soldedepartement();
                    $off = 0 - $valeur;
                    $offrandeSole->setMontant($off);
                    $offrandeSole->setDepartement($idgpe);
                    $entityManager->persist($offrandeSole);
                }
            } elseif ($nature == 0) {
                // $valeur2 = $form['ajout']->getData();
                //$user = $this->getUser();
                $valeur2 = $form['ajout']->getData();
                $mont3 = $depensedepartement->getMontant();
                $mon0 = $mont3 - $valeur2;
                $depensedepartement->setMontant($mon0);
                $dql2 = $soldeRepo->findBy(['departement' => $idgpe]);
                if ($dql2) {
                    $id = $dql2[0]->getId();
                    $activite = $soldeRepo->findOneBySoldeDepartement($id);
                    $mont = $activite->getMontant();
                    $j = 0;
                    $j = $mont + $valeur2;
                    $activite->setMontant($j);
                } else {

                    $offrandeSole = new Soldedepartement();
                    $offrandeSole->setMontant($valeur2);
                    $offrandeSole->setDepartement($idgpe);
                    $entityManager->persist($offrandeSole);
                }
            }
         $this->addFlash('success', 'Modification effectuée avec succès.');
            $depensedepartementRepository->add($depensedepartement);
            return $this->redirectToRoute('app_depensedepartement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('depensedepartement/edit.html.twig', [
                    'depensedepartement' => $depensedepartement,
                    'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_depensedepartement_delete', methods: ['POST'])]
    public function delete(Request $request, Depensedepartement $depensedepartement, DepartementRepository $departementRepo, SoldedepartementRepository $soldeRepo): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_DEPARTEMENT')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici! Veuillez vous adresser au secretariat');
        }
        if ($this->isCsrfTokenValid('delete' . $depensedepartement->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();
            $departement = $this->getUser()->getDepartement();
            $departement2 = $departementRepo->findOneDepartement($departement);
            $dql = $soldeRepo->findBy(['departement' => $departement2]);

            $total = $depensedepartement->getMontant();
            if ($dql) {
                $id = $dql[0]->getId();
                $activite = $soldeRepo->findOneBySoldeDepartement($id);
                $mont = $activite->getMontant();
                $j = 0;
                $j = $mont + $total;
                $activite->setMontant($j);
            }

            $depensedepartement->setDeletedFromIp($this->GetIp());
            $depensedepartement->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $this->addFlash('danger', 'Suppression avec succès');
            $depensedepartement->setDeletedBy($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_depensedepartement_index', [], Response::HTTP_SEE_OTHER);
    }

}
