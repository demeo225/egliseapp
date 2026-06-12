<?php

namespace App\Controller;

use App\Entity\Depensezone;
use App\Entity\Soldezone;
use App\Form\DepensezoneType;
use App\Form\UpdatedepensezoneType;
use App\Repository\DepensezoneRepository;
use App\Repository\SoldezoneRepository;
use App\Repository\ZoneRepository;
use App\Traits\ClientIp;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/depensezone')]
class DepensezoneController extends AbstractController {

    use ClientIp;

   #[Route('/', name: 'app_depensezone_index', methods: ['GET'])]
    public function index(DepensezoneRepository $depensezoneRepository, SoldezoneRepository $soldeRepo, ZoneRepository $zoneRepo): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        $user = $this->getUser();
        $eglise = $user->getEglise();
        $zone = $user->getZone();
        
        // Vérifier les droits d'accès
        if ($this->isGranted('ROLE_ADMIN') || $this->isGranted('ROLE_PASTEUR') || $this->isGranted('ROLE_SUPER_ADMIN') || $this->isGranted('ROLE_SECRETAIRE')) {
            // Rôle supérieur : voit toutes les cotisations
            $cotisations = $depensezoneRepository->findBy([
                'eglise' => $eglise, 
                'deletedAt' => NULL
            ], ['createAt' => 'DESC']);
        } 
        elseif ($this->isGranted('ROLE_RESPONSABLE_ZONE') && $zone) {
            // Responsable de zone : voit uniquement les cotisations de sa zone
            $cotisations = $depensezoneRepository->findBy([
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
        
        return $this->render('depensezone/index.html.twig', [
            'depensezones' => $cotisations,
            'soldes' => $solde,
        ]);
    }

    #[Route('/new', name: 'app_depensezone_new', methods: ['GET', 'POST'])]
    public function new(Request $request, DepensezoneRepository $depensezoneRepository, ZoneRepository $zoneRepo, EntityManagerInterface $entityManager, SoldezoneRepository $soldeRepo): Response {
        if (!$this->isGranted('ROLE_RESPONSABLE_ZONE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $depensezone = new Depensezone();

        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $zone = $zoneRepo->findOneByUser($user);
            $zone = $zoneRepo->findOneByUser($user);
         if (!$zone) {
            $this->addFlash('warning', 'Vous ne disposez pas de zone/secteur à gérer.');
            return $this->redirectToRoute('app_depensezone_index');
        } 
        $form = $this->createForm(DepensezoneType::class, $depensezone);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $idgpe = $zoneRepo->findOneByUser($user);
            $offrande = $form['montant']->getData();


            //$idgpe = $form['zone']->getData();
            // Decrementation du solde
            $dql2 = $soldeRepo->findBy(['zone' => $idgpe]);
            if ($dql2) {
                $id = $dql2[0]->getId();
                $activite = $soldeRepo->findOneBySoldeZone($id);
                $mont = $activite->getMontant();
                $j = 0;
                $j = $mont - $offrande;
                $activite->setMontant($j);
            } else {

                $offrandeSole = new Soldezone();
                $off = 0 - $offrande;
                $offrandeSole->setMontant($off);
                $offrandeSole->setZone($idgpe);
                $entityManager->persist($offrandeSole);
            }

            $depensezone->setCreatedBy($user);
            $depensezone->setEglise($eglise);
            $depensezone->setZone($user->getZone());
            $depensezone->setCreatedFromIp($this->GetIp());
            $depensezoneRepository->add($depensezone);
            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'app_depensezone_new' : 'app_depensezone_index';
            if ($nextAction) {
                $this->addFlash('success', 'Action effectuée avec succès.');
            }

            return $this->redirectToRoute($nextAction);
//            return $this->redirectToRoute('app_depensezone_index', [], Response::HTTP_SEE_OTHER);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('depensezone/new.html.twig', [
                    'depensezone' => $depensezone,
                    'zone' => $zone,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    #[Route('/{id}', name: 'app_depensezone_show', methods: ['GET'])]
    public function show(Depensezone $depensezone): Response {
        return $this->render('depensezone/show.html.twig', [
                    'depensezone' => $depensezone,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_depensezone_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Depensezone $depensezone, ZoneRepository $zoneRepo, DepensezoneRepository $depensezoneRepository, EntityManagerInterface $entityManager, SoldezoneRepository $soldeRepo): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_ZONE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici! Veuillez vous adresser au secretariat');
        }
        $user = $this->getUser();

                
            $zone = $zoneRepo->findOneByUser($user);
         if (!$zone) {
            $this->addFlash('warning', 'Vous ne disposez pas de zone/secteur à gérer.');
            return $this->redirectToRoute('app_depensezone_index');
        } 
     
        $form = $this->createForm(UpdatedepensezoneType::class, $depensezone,);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
             $user = $this->getUser();
            $idgpe = $zoneRepo->findOneByUser($user);

            $depensezone->setUpdatedFromIp($this->GetIp());
//            $user = $this->getUser();
            $depensezone->setUpdatedBy($user);

            $nature = $form['typeoff']->getData();

            if ($nature == 1) {
                $valeur = $form['ajout']->getData();

                $user = $this->getUser();

                $mont1 = $depensezone->getMontant();
                $mon = $valeur + $mont1;
                $depensezone->setMontant($mon);
                // On retranche montant au solde si la depense augmente et on ajoute si la depense diminue

                $dql2 = $soldeRepo->findBy(['zone' => $idgpe]);
                if ($dql2) {
                    $id = $dql2[0]->getId();
                    $activite = $soldeRepo->findOneBySoldeZone($id);
                    $mont = $activite->getMontant();
                    $j = 0;
                    $j = $mont - $valeur;
                    $activite->setMontant($j);
                } else {

                    $offrandeSole = new Soldezone();
                    $off = 0 - $valeur;
                    $offrandeSole->setMontant($off);
                    $offrandeSole->setZone($idgpe);
                    $entityManager->persist($offrandeSole);
                }
            } elseif ($nature == 0) {
                // $valeur2 = $form['ajout']->getData();
                //$user = $this->getUser();
                $valeur2 = $form['ajout']->getData();
                $mont3 = $depensezone->getMontant();
                $mon0 = $mont3 - $valeur2;
                $depensezone->setMontant($mon0);
                $dql2 = $soldeRepo->findBy(['zone' => $idgpe]);
                if ($dql2) {
                    $id = $dql2[0]->getId();
                    $activite = $soldeRepo->findOneBySoldeZone($id);
                    $mont = $activite->getMontant();
                    $j = 0;
                    $j = $mont + $valeur2;
                    $activite->setMontant($j);
                } else {

                    $offrandeSole = new Soldezone();
                    $offrandeSole->setMontant($valeur2);
                    $offrandeSole->setZone($idgpe);
                    $entityManager->persist($offrandeSole);
                }
            }
             $this->addFlash('success', 'Modification effectuée avec succès.');
            $depensezoneRepository->add($depensezone);
            return $this->redirectToRoute('app_depensezone_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('depensezone/edit.html.twig', [
                    'depensezone' => $depensezone,
                    'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_depensezone_delete', methods: ['POST'])]
    public function delete(Request $request, Depensezone $depensezone, ZoneRepository $zoneRepo, SoldezoneRepository $soldeRepo): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_ZONE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici! Veuillez vous adresser au secretariat');
        }
        if ($this->isCsrfTokenValid('delete' . $depensezone->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();
            $zone = $this->getUser()->getZone();
            $zone2 = $zoneRepo->findOneZone($zone);
            $dql = $soldeRepo->findBy(['zone' => $zone2]);

            $total = $depensezone->getMontant();
            if ($dql) {
                $id = $dql[0]->getId();
                $activite = $soldeRepo->findOneBySoldeZone($id);
                $mont = $activite->getMontant();
                $j = 0;
                $j = $mont + $total;
                $activite->setMontant($j);
            }

            $depensezone->setDeletedFromIp($this->GetIp());
            $depensezone->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $depensezone->setDeletedBy($user);

            $this->addFlash('danger', 'Suppression effectuée avec succès.');
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_depensezone_index', [], Response::HTTP_SEE_OTHER);
    }

}
