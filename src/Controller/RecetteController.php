<?php

namespace App\Controller;

use App\Entity\Recette;
use App\Entity\Solde;
use App\Form\RecetteType;
use App\Form\UpdaterecetteType;
use App\Repository\RecetteRepository;
use App\Repository\ObjetrecetteRepository;
use App\Repository\SoldeRepository;
use App\Traits\ClientIp;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/recette')]
class RecetteController extends AbstractController
{
    use ClientIp; 

    #[Route('/', name: 'app_recette_index', methods: ['GET'])]
    public function index(RecetteRepository $recetteRepository, SoldeRepository $soldeRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $recette = $recetteRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $solde = $soldeRepository->findBy(['eglise' => $eglise]);
        return $this->render('recette/index.html.twig', [
            'recettes' => $recette,
            'soldes' => $solde,
        ]);
    }

    #[Route('/new', name: 'app_recette_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ObjetrecetteRepository $objetrecetteRepository, SoldeRepository $soldeRepo, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $recette = new Recette();
        $eglise = $this->getUser()->getEglise();
        $objetrecette = $objetrecetteRepository->findBy(['eglise' => $eglise, 'deletedAt' => NULL]);
        $form = $this->createForm(RecetteType::class, $recette, ['objetrecette' => $objetrecette]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ideglise = $this->getUser()->getEglise()->getId();
            $eglise = $this->getUser()->getEglise();

            $user = $this->getUser();
            $recette->setIdeglise($ideglise);
            $recette->setEglise($eglise);
            $recette->setCreatedBy($this->getUser());
            $recette->setCreatedFromIp($this->GetIp());

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
            // $cinq = $form['cinq']->getData();
            $total = $form['montant']->getData();

           // $total = ($dixmille * 10000) + ($cinqmille * 5000) + ($deuxmille * 2000) + ($mille * 1000) + ($cinqcentbillet * 500) + ($cinqcentpiece * 500) + ($deuxcent * 200) + ($cent * 100) + ($cinquante * 50) + ($vingtcinq * 25) + ($dix * 10) + ($cinq * 5);
           // $recette->setMontant($total);
            // On cumule le montant total dans une table Montantoff
            $dql = $soldeRepo->findBy(['eglise' => $eglise]);
            if ($dql) {
                $id = $dql[0]->getId();
                $activite = $soldeRepo->findOneBySolde($id);
                $mont = $activite->getMontant();
                $j = 0;
                $j = $mont + $total;
                $activite->setMontant($j);
            } else {
                $montant = new Solde();
                $montant->setMontant($total);
                $montant->setEglise($eglise);
                $entityManager->persist($montant);
            }

 
            $recette->setCreatedAt(new DateTime('now'));
            $entityManager->persist($recette);
            $entityManager->flush();
            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'app_recette_new' : 'app_recette_index';
            if ($nextAction) {
                $this->addFlash('success', 'Enregistrement avec succès.');
            }
            return $this->redirectToRoute($nextAction);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);

        return $this->render('recette/new.html.twig', [
            'recette' => $recette,
            'form' => $form->createView(),
            'response' => $response,
        ], $response);
    }

    #[Route('show/{id}', name: 'app_recette_show', methods: ['GET'])]
    public function show(Recette $recette): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($this->denyAccessUnlessGranted("ROLE_USER", "Authentification", "Veuillez vous connecté SVP !!!")) {
            return $this->redirectToRoute("app_login");
        };
        return $this->render('recette/show.html.twig', [
            'recette' => $recette,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_recette_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Recette $recette, ObjetrecetteRepository $objetrecetteRepository, SoldeRepository $soldeRepo ,EntityManagerInterface $entityManager): Response
    {
        if ($this->denyAccessUnlessGranted("ROLE_USER", "Authentification", "Veuillez vous connecté SVP !!!")) {
            return $this->redirectToRoute("app_login");
        };
        $eglise = $this->getUser()->getEglise();
        $objetrecette = $objetrecetteRepository->findBy(criteria: ['eglise' => $eglise, 'deletedAt' => NULL]);
        $form = $this->createForm(UpdaterecetteType::class, $recette, ['objetrecette' => $objetrecette]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $recette->setUpdatedAt(new \DateTime("now"));
            $user = $this->getUser();
            $recette->setUpdatedBy($user);
            $recette->setUpdatedFromIp($this->GetIp());



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
                    $j = $mont + $valeur;
                    $activite->setMontant($j);
                } else {

                    $soldeEglise = new Solde();
                    $off = 0 - $valeur;
                    $soldeEglise->setMontant($off);
                    $soldeEglise->setEglise($eglise);
                    $entityManager->persist($soldeEglise);
                }

                $mont1 = $recette->getMontant();
                $mon = $valeur + $mont1;
                $recette->setMontant($mon);
            } elseif ($nature == 0) {
                $valeur2 = $form['ajout']->getData();
                $dql1 = $soldeRepo->findBy(['eglise' => $eglise]);

                // SI solde existe, on decremente le montant, sinon on crée solde et on decrement le montant

                if ($dql1) {
                    $id = $dql1[0]->getId();
                    $activite = $soldeRepo->findOneBySolde($id);
                    $mont = $activite->getMontant();
                    $j = 0;
                    $j = $mont - $valeur2;
                    $activite->setMontant($j);
                } else {

                    // $soldeEglise = new Solde();
                    // $off = 0 + $valeur2;
                    // $soldeEglise->setMontant($off);
                    // $soldeEglise->setEglise($eglise);
                    // $entityManager->persist($soldeEglise);
                }



                $mont2 = $recette->getMontant();
                $mon1 = $mont2 - $valeur2;
                $recette->setMontant($mon1);
            }

            $entityManager->flush();
            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'app_recette_new' : 'app_recette_index';
            if ($nextAction) {
                $this->addFlash('success', 'Modification avec succès.');
            }
            return $this->redirectToRoute($nextAction);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);

        return $this->render('recette/edit.html.twig', [
            'recette' => $recette,
            'form' => $form->createView(),
            'response' => $response,
        ], $response);
    }

    #[Route('/delete', name: 'app_recette_delete', methods: ['POST'])]
    public function delete(Request $request, Recette $recette, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($this->isCsrfTokenValid('delete' . $recette->getId(), $request->request->get('_token'))) {



            $recette->setDeletedFromIp($this->GetIp());
            $recette->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $recette->setDeletedBy($user);
            $this->addFlash('danger', 'Suppression avec succès.');

            $em->flush();
        }

        return $this->redirectToRoute('app_recette_index');
    }
}
