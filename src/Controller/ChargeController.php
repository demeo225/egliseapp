<?php

namespace App\Controller;

use App\Entity\Charge;
use App\Entity\Solde;
use App\Form\ChargeType;
use App\Form\UpdatechargeType;
use App\Repository\ChargeRepository;
use App\Repository\DepartementRepository;
use App\Repository\ObjetchargeRepository;
use App\Repository\SoldeRepository;
use App\Traits\ClientIp;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/charge')]
class ChargeController extends AbstractController
{
    use ClientIp;

    #[Route('/', name: 'app_charge_index', methods: ['GET'])]
    public function index(ChargeRepository $chargeRepository, SoldeRepository $soldeRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $charge = $chargeRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $solde = $soldeRepository->findBy(['eglise' => $eglise]);
        return $this->render('charge/index.html.twig', [
            'charges' => $charge,
            'soldes' => $solde,
        ]);
    } 
  
    #[Route('/new', name: 'app_charge_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ObjetchargeRepository $objetchargeRepository, DepartementRepository $departementRepository ,SoldeRepository $soldeRepo ,EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $charge = new Charge();
        $eglise = $this->getUser()->getEglise();
        $objetcharge = $objetchargeRepository->findBy(['eglise'=> $eglise, 'deletedAt'=> NULL]);
        $departement = $departementRepository->findBy(['eglise'=> $eglise, 'deletedAt'=> NULL]);
        $form = $this->createForm(ChargeType::class, $charge, ['objetcharge' => $objetcharge, 'departement' => $departement, ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ideglise = $this->getUser()->getEglise()->getId();
            $eglise = $this->getUser()->getEglise();
          
            $user= $this->getUser();
            $charge->setIdeglise($ideglise);
            $charge->setEglise($eglise);
            $charge->setCreatedBy($this->getUser());
            $charge->setCreatedFromIp($this->GetIp());
            // $dixmille = $form['dixmille']->getData();
            // $cinqmille = $form['cinqmille']->getData();
            // $deuxmille = $form['deuxmille']->getData();
            // $mille = $form['mille']->getData();
            // $cinqcentbillet = $form['centbillet']->getData();
            // $cinqcentpiece = $form['centpiece']->getData();
            // $deuxcent = $form['deuxcent']->getData();
            // $cent = $form['cent']->getData();
            // $cinquante = $form['cinquante']->getData();
            // $vingtcinq = $form['vingtcinq']->getData();
            // $dix = $form['dix']->getData();
            $montant = $form['montant']->getData();

          //  $total = ($dixmille * 10000) + ($cinqmille * 5000) + ($deuxmille * 2000) + ($mille * 1000) + ($cinqcentbillet * 500) + ($cinqcentpiece * 500) + ($deuxcent * 200) + ($cent * 100) + ($cinquante * 50) + ($vingtcinq * 25) + ($dix * 10) + ( $cinq * 5);
           // $charge->setMontant($total);
            // On cumule le montant total dans une table Montantoff
            $dql = $soldeRepo->findBy(['eglise' => $eglise]);
            if ($dql) {
                $id = $dql[0]->getId();
                $activite = $soldeRepo->findOneBySolde($id);
                $mont = $activite->getMontant();
                $j = 0;
                $j = $mont - $montant;
                $activite->setMontant($j);
            } else {
                $solde = new Solde();
                $e = 0 - $montant;
                $solde->setMontant($e);
                $solde->setEglise($eglise);
                $entityManager->persist($solde);
            }



            $charge->setCreatedAt(new DateTime('now'));
            $entityManager->persist($charge);
            $entityManager->flush();
            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'app_charge_new' : 'app_charge_index';
            if ($nextAction) {
                $this->addFlash('success', 'Enregistrement avec succès.');
            }
            return $this->redirectToRoute($nextAction);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);

        return $this->render('charge/new.html.twig', [
        'charge' => $charge,
       'form' => $form->createView(),
        'response' => $response,
        ], $response);
    }

    #[Route('show/{id}', name: 'app_charge_show', methods: ['GET'])]
    public function show(Charge $charge): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if($this->denyAccessUnlessGranted("ROLE_USER","Authentification", "Veuillez vous connecté SVP !!!")){
            return $this->redirectToRoute("app_login");
        };
        return $this->render('charge/show.html.twig', [
            'charge' => $charge,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_charge_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Charge $charge,ObjetchargeRepository $objetchargeRepository , DepartementRepository $departementRepository , SoldeRepository $soldeRepo, EntityManagerInterface $entityManager): Response
    {
        if($this->denyAccessUnlessGranted("ROLE_USER","Authentification", "Veuillez vous connecté SVP !!!")){
            return $this->redirectToRoute("app_login");
        };
        $eglise = $this->getUser()->getEglise();
        $objetcharge = $objetchargeRepository->findBy(['eglise'=> $eglise, 'deletedAt'=> NULL]);
        $departement = $departementRepository->findBy(['eglise'=> $eglise, 'deletedAt'=> NULL]);
        $form = $this->createForm(UpdatechargeType::class, $charge, ['objetcharge' => $objetcharge, 'departement'=> $departement]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $charge->setUpdatedAt(new \DateTime("now"));
           
            
            $user = $this->getUser();
            $charge->setUpdatedBy($user);

            $nature = $form['typeoff']->getData();

            if ($nature == 1) {
                $valeur = $form['ajout']->getData();

                // On cumule le montant total dans une table Montantoff
                $dql = $soldeRepo->findBy(['eglise' => $eglise]);

                // SI solde existe, on incremente le montant, sinon on crée solde et on incremente le montant

                if ($dql) {
                    $id = $dql[0]->getId();
                    $activite = $soldeRepo->findOneBySolde($id);
                    $mont = $activite->getMontant();
                    $j = 0;
                    $j = $mont - $valeur;
                    $activite->setMontant($j);
                } else {

                    $soldeEglise = new Solde();
                    $off = 0 - $valeur;
                    $soldeEglise->setMontant($off);
                    $soldeEglise->setEglise($eglise);
                    $entityManager->persist($soldeEglise);
                }

                $mont1 = $charge->getMontant();
                $mon = $valeur + $mont1;
                $charge->setMontant($mon);
            } elseif ($nature == 0) {
                $valeur2 = $form['ajout']->getData();
                $dql1 = $soldeRepo->findBy(['eglise' => $eglise]);

                // SI solde existe, on decremente le montant, sinon on crée solde et on decrement le montant

                if ($dql1) {
                    $id = $dql1[0]->getId();
                    $activite = $soldeRepo->findOneBySolde($id);
                    $mont = $activite->getMontant();
                    $j = 0;
                    $j = $mont + $valeur2;
                    $activite->setMontant($j);
                } else {

                    // $soldeEglise = new Solde();
                    // $off = 0 + $valeur2;
                    // $soldeEglise->setMontant($off);
                    // $soldeEglise->setEglise($eglise);
                    // $entityManager->persist($soldeEglise);
                }



                $mont2 = $charge->getMontant();
                $mon1 = $mont2 - $valeur2;
                $charge->setMontant($mon1);
            }
            $charge->setUpdatedFromIp($this->GetIp());
            $entityManager->flush();
            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'app_charge_new' : 'app_charge_index';
            if ($nextAction) {
                $this->addFlash('success', 'Modification avec succès.');
            }
            return $this->redirectToRoute($nextAction);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);

        return $this->render('charge/edit.html.twig', [
            'charge' => $charge,
            'form' => $form->createView(),
            'response' => $response,
        ], $response);
    }

    #[Route('/delete', name: 'app_charge_delete', methods: ['POST'])]
    public function delete(Request $request, Charge $charge, EntityManagerInterface $em): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($this->isCsrfTokenValid('delete' . $charge->getId(), $request->request->get('_token'))) {

           

            $charge->setDeletedFromIp($this->GetIp());
            $charge->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $charge->setDeletedBy($user);
            $this->addFlash('danger', 'Suppression avec succès.');

            $em->flush();
        }

        return $this->redirectToRoute('app_charge_index');
    }

}
