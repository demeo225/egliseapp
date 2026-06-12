<?php

namespace App\Controller;

use App\Entity\Depensegroupe;
use App\Entity\Soldegroupe;
use App\Form\DepensegroupeType;
use App\Form\UpdatedepensegroupeType;
use App\Repository\DepensegroupeRepository;
use App\Repository\GroupeRepository;
use App\Repository\SoldegroupeRepository;
use App\Traits\ClientIp;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/depensegroupe')]
class DepensegroupeController extends AbstractController {

    use ClientIp;

 #[Route('/', name: 'app_depensegroupe_index', methods: ['GET'])]
    public function index(DepensegroupeRepository $depensegroupeRepository, SoldegroupeRepository $soldeRepo, GroupeRepository $groupeRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        $user = $this->getUser();
        $eglise = $user->getEglise();
        
        // Construction de la requête selon le rôle
        $qb = $depensegroupeRepository->createQueryBuilder('d')
            ->leftJoin('d.groupe', 'c')
            ->leftJoin('c.departement', 'z')
            ->where('d.eglise = :eglise')
            ->andWhere('d.deletedAt IS NULL')
            ->setParameter('eglise', $eglise);
        
        // Filtres selon les rôles
        if ($this->isGranted('ROLE_ADMIN') || $this->isGranted('ROLE_PASTEUR') || $this->isGranted('ROLE_SECRETAIRE')) {
            // Ces rôles voient toutes les dépenses
        } 
        elseif ($this->isGranted('ROLE_RESPONSABLE_DDEPARTEMENT')) {
            $departement = $user->getDepartement();
            if ($departement) {
                $qb->andWhere('z.id = :departementId')
                   ->setParameter('departementId', $departement->getId());
            } else {
                $this->addFlash('warning', 'Aucune departement associée à votre compte.');
                return $this->redirectToRoute('home');
            }
        }
        elseif ($this->isGranted('ROLE_RESPONSABLE_CELLULE')) {
            $groupe = $user->getGroupe();
            if ($groupe) {
                $qb->andWhere('c.id = :groupeId')
                   ->setParameter('groupeId', $groupe->getId());
            } else {
                $this->addFlash('warning', 'Aucune groupe associée à votre compte.');
                return $this->redirectToRoute('home');
            }
        }
        else {
            $this->addFlash('error', 'Vous n\'avez pas les droits pour voir les dépenses.');
            return $this->redirectToRoute('home');
        }
        
        $depenses = $qb->orderBy('d.datedepense', 'DESC')->getQuery()->getResult();

              $user = $this->getUser();
              $groupe = $user->getGroupe();
        $solde = $soldeRepo->findBy(['groupe' => $groupe]);
        
        return $this->render('depensegroupe/index.html.twig', [
            'depensegroupes' => $depenses,
             'soldes' => $solde,
        ]);
    }

    #[Route('/new', name: 'app_depensegroupe_new', methods: ['GET', 'POST'])]
    public function new(Request $request, DepensegroupeRepository $depensegroupeRepository, EntityManagerInterface $entityManager, GroupeRepository $groupeRepo, SoldegroupeRepository $soldeRepo): Response {
        if (!$this->isGranted('ROLE_RESPONSABLE_GROUPE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $depensegroupe = new Depensegroupe();

        //$eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $groupe = $groupeRepo->findOneByUser($user);
          
         if (!$groupe) {
            $this->addFlash('warning', 'Vous ne disposez pas de groupe à gérer.');
            return $this->redirectToRoute('app_depensegroupe_index');
        } 

        $form = $this->createForm(DepensegroupeType::class, $depensegroupe,);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
          
            $idgpe = $groupeRepo->findOneByUser($user);;

            $offrande = $form['montant']->getData();

            // le solde est reduit si existe et est créé puis à moins si non existant
            $dql2 = $soldeRepo->findBy(['groupe' => $idgpe]);
            if ($dql2) {
                $id = $dql2[0]->getId();
                $activite = $soldeRepo->findOneBySoldeGroupe($id);
                $mont = $activite->getMontant();
                $j = 0;
                $j = $mont - $offrande;
                $activite->setMontant($j);
            } else {

                $offrandeSole = new Soldegroupe();
                $off = 0 - $offrande;
                $offrandeSole->setMontant($off);
                $offrandeSole->setGroupe($idgpe);
                $entityManager->persist($offrandeSole);

            
            }
                $depensegroupe->setCreatedBy($user);
                $depensegroupe->setGroupe($idgpe);
                $depensegroupe->setEglise($user->getEglise());
                $depensegroupe->setCreatedFromIp($this->GetIp());
                $depensegroupeRepository->add($depensegroupe);
            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'app_depensegroupe_new' : 'app_depensegroupe_index';
            if ($nextAction) {
                $this->addFlash('success', 'Action effectuée avec succès.');
            }

            return $this->redirectToRoute($nextAction);
//            return $this->redirectToRoute('app_depensegroupe_index', [], Response::HTTP_SEE_OTHER);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('depensegroupe/new.html.twig', [
                    'groupe' => $groupe,
                    'depensegroupe' => $depensegroupe,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    #[Route('/{id}', name: 'app_depensegroupe_show', methods: ['GET'])]
    public function show(Depensegroupe $depensegroupe): Response {
        return $this->render('depensegroupe/show.html.twig', [
                    'depensegroupe' => $depensegroupe,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_depensegroupe_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Depensegroupe $depensegroupe, GroupeRepository $groupeRepo, EntityManagerInterface $entityManager, DepensegroupeRepository $depensegroupeRepository, SoldegroupeRepository $soldeRepo): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_GROUPE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici! Veuillez vous adresser au secretariat');
        }
                $user = $this->getUser();
            $groupe = $groupeRepo->findOneByUser($user);        
           if (!$groupe) {
            $this->addFlash('warning', 'Vous ne disposez pas de groupe à gérer.');
            return $this->redirectToRoute('app_depensegroupe_index');
        } 

     
        $form = $this->createForm(UpdatedepensegroupeType::class, $depensegroupe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

        $idgpe = $groupeRepo->findOneByUser($user);  

            $depensegroupe->setUpdatedFromIp($this->GetIp());
//            $user = $this->getUser();
            $depensegroupe->setUpdatedBy($user);

            $nature = $form['typeoff']->getData();

            if ($nature == 1) {
                $valeur = $form['ajout']->getData();

                $user = $this->getUser();

                $mont1 = $depensegroupe->getMontant();
                $mon = $valeur + $mont1;
                $depensegroupe->setMontant($mon);
                // On retranche montant au solde si la depense augmente et on ajoute si la depense diminue

                $dql2 = $soldeRepo->findBy(['groupe' => $idgpe]);
                if ($dql2) {
                    $id = $dql2[0]->getId();
                    $activite = $soldeRepo->findOneBySoldeGroupe($id);
                    $mont = $activite->getMontant();
                    $j = 0;
                    $j = $mont - $valeur;
                    $activite->setMontant($j);
                } else {

                    $offrandeSole = new Soldegroupe();
                    $off = 0 - $valeur;
                    $offrandeSole->setMontant($off);
                    $offrandeSole->setGroupe($idgpe);
                    $entityManager->persist($offrandeSole);
                }
            } elseif ($nature == 0) {
                // $valeur2 = $form['ajout']->getData();
                //$user = $this->getUser();
                $valeur2 = $form['ajout']->getData();
                $mont3 = $depensegroupe->getMontant();
                $mon0 = $mont3 - $valeur2;
                $depensegroupe->setMontant($mon0);
                $dql2 = $soldeRepo->findBy(['groupe' => $idgpe]);
                if ($dql2) {
                    $id = $dql2[0]->getId();
                    $activite = $soldeRepo->findOneBySoldeGroupe($id);
                    $mont = $activite->getMontant();
                    $j = 0;
                    $j = $mont + $valeur2;
                    $activite->setMontant($j);
                } else {

                    $offrandeSole = new Soldegroupe();
                    $offrandeSole->setMontant($valeur2);
                    $offrandeSole->setGroupe($idgpe);
                    $entityManager->persist($offrandeSole);
                }
            }
             $this->addFlash('success', 'Modification effectuée avec succès.');
            $depensegroupeRepository->add($depensegroupe);
            return $this->redirectToRoute('app_depensegroupe_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('depensegroupe/edit.html.twig', [
                    'depensegroupe' => $depensegroupe,
                    'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_depensegroupe_delete', methods: ['POST'])]
    public function delete(Request $request, Depensegroupe $depensegroupe, GroupeRepository $groupeRepo, SoldegroupeRepository $soldeRepo): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_GROUPE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici! Veuillez vous adresser au secretariat');
        }
        if ($this->isCsrfTokenValid('delete' . $depensegroupe->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();
            $groupe = $this->getUser()->getGroupe();
            $groupe2 = $groupeRepo->findOneGroupe($groupe);
            $dql = $soldeRepo->findBy(['groupe' => $groupe2]);

            $total = $depensegroupe->getMontant();

            $id = $dql[0]->getId();
            $activite = $soldeRepo->findOneBySoldeGroupe($id);
            $mont = $activite->getMontant();
            $j = 0;
            $j = $mont + $total;
            $activite->setMontant($j);

            $depensegroupe->setDeletedFromIp($this->GetIp());
            $depensegroupe->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $this->addFlash('danger', 'Suppression avec succès');
            $depensegroupe->setDeletedBy($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_depensegroupe_index', [], Response::HTTP_SEE_OTHER);
    }

}
