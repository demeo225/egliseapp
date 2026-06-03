<?php

namespace App\Controller;

use App\Entity\Dimeglobale;
use App\Entity\Solde;
use App\Form\DimeglobaleType;
use App\Form\UpdatedimeglobaleType;
use App\Repository\DimeglobaleRepository;
use App\Repository\SoldeRepository;
use App\Traits\ClientIp;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/dimeglobale')]
class DimeglobaleController extends AbstractController {

    use ClientIp;

    #[Route('/', name: 'dimeglobale_index', methods: ['GET'])]
    public function index(DimeglobaleRepository $dimeglobaleRepository, SoldeRepository $soldeRepo): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_FINANCE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $dimeglobale = $dimeglobaleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $solde = $soldeRepo->findBy(['eglise' => $eglise]);

        return $this->render('dimeglobale/index.html.twig', [
                    'dimeglobales' => $dimeglobale,
                    'soldes' => $solde,
        ]);
    }

    #[Route('/new', name: 'dimeglobale_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SoldeRepository $soldeRepo): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_FINANCE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $dimeglobale = new Dimeglobale();
        $form = $this->createForm(DimeglobaleType::class, $dimeglobale);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $dimeglobale->setCreatedFromIp($this->GetIp());
            $eglise = $this->getUser()->getEglise();
            $user = $this->getUser();
            $dimeglobale->setCreatedBy($user);
            $dimeglobale->setEglise($eglise);
            $dixmille = $form['dixmille']->getData();
            $cinqmille = $form['cinqmille']->getData();
            $deuxmille = $form['deuxmille']->getData();
            $mille = $form['mille']->getData();
            $cinqcentbillet = $form['centbillet']->getData();
            $cinqcentpiece = $form['centpiece']->getData();
            $deuxcent = $form['deuxcent']->getData();
            $cent = $form['cent']->getData();
            $cinquante = $form['cinquante']->getData();
            $vingtcinq = $form['vingtcinq']->getData();
            $dix = $form['dix']->getData();
            $cinq = $form['cinq']->getData();

            $total = ($dixmille * 10000) + ($cinqmille * 5000) + ($deuxmille * 2000) + ($mille * 1000) + ($cinqcentbillet * 500) + ($cinqcentpiece * 500) + ($deuxcent * 200) + ($cent * 100) + ($cinquante * 50) + ($vingtcinq * 25) + ($dix * 10) + ( $cinq * 5);
            $dimeglobale->setMontant("$total");
            $entityManager = $this->getDoctrine()->getManager();

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

            $entityManager->persist($dimeglobale);
            $entityManager->flush();

            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'dimeglobale_new' : 'dimeglobale_index';
            if ($nextAction) {
                $this->addFlash('success', 'Enregistrement avec succès.');
            }
            return $this->redirectToRoute($nextAction);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('dimeglobale/new.html.twig', [
                    'dimeglobale' => $dimeglobale,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    #[Route('/{id}', name: 'dimeglobale_show', methods: ['GET'])]
    public function show(Dimeglobale $dimeglobale): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_FINANCE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        return $this->render('dimeglobale/show.html.twig', [
                    'dimeglobale' => $dimeglobale,
        ]);
    }

  #[Route('/{id}/update', name: 'dimeglobale_update', methods: ['GET', 'POST'])]
    public function update(EntityManagerInterface $entityManager, Request $request, Dimeglobale $dimeglobale, SoldeRepository $soldeRepo): Response {
        if (!$this->isGranted('ROLE_RESPONSABLE_FINANCE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $data = json_decode($request->getContent(), true);

        $form = $this->createForm(UpdatedimeglobaleType::class, $dimeglobale);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $dimeglobale->setUpdatedFromIp($this->GetIp());
            $eglise = $this->getUser()->getEglise();
            $user = $this->getUser();
            $dimeglobale->setUpdatedBy($user);

            $nature = $form['typeof']->getData();

            if ($nature == 1) {
                $valeur = $form['ajout']->getData();

                // On cumule le montant total dans une table Montantoff
                $dql = $soldeRepo->findBy(['eglise' => $eglise]);

                $mont1 = $dimeglobale->getMontant();
                $mon = $valeur + $mont1;
                $dimeglobale->setMontant($mon);
              
                
            if ($dql) {
                $id = $dql[0]->getId();
                $activite = $soldeRepo->findOneBySolde($id);
                $mont = $activite->getMontant();
                $j = 0;
                $j = $mont + $valeur;
                $activite->setMontant($j);
            } else {
                $montant = new Solde();
                $montant->setMontant($valeur);
                $montant->setEglise($eglise);
                $entityManager->persist($montant);
            }
                
            } elseif ($nature == 0) {
                $valeur2 = $form['ajout']->getData();
                $mont2 = $dimeglobale->getMontant();
                $mon1 = $mont2 - $valeur2;
                $dimeglobale->setMontant($mon1);
                
                
                // On crée le solde si inexitant et on decremente avec la valeur concernée sinon on decrement directement
                
                         $dql2 = $soldeRepo->findBy(['eglise' => $eglise]);
            if ($dql2) {
                $id = $dql2[0]->getId();
                $activite = $soldeRepo->findOneBySolde($id);
                $mont = $activite->getMontant();
                $j = 0;
                $j = $mont - $valeur2;
                $activite->setMontant($j);
            } else {
                $montant = new Solde();
                $mont0 = 0 - $valeur2 ;
                $montant->setMontant($mont0);
                $montant->setEglise($eglise);
                $entityManager->persist($montant);
            }
                
                
            }
    $this->addFlash('success', 'Modification effectuée avec succès.');
            $entityManager->persist($dimeglobale);

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('dimeglobale_index');
        }

        return $this->render('dimeglobale/update.html.twig', [
                    'dimeglobale' => $dimeglobale,
                    'form' => $form->createView(),
        ]);
    }


    #[Route('/{id}', name: 'dimeglobale_delete', methods: ['POST'])]
    public function delete(Request $request, Dimeglobale $dimeglobale, SoldeRepository $soldeRepo): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici! Veuillez vous adresser au secretariat');
        }
        if ($this->isCsrfTokenValid('delete' . $dimeglobale->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();

            $total = $dimeglobale->getMontant();
            $eglise = $this->getUser()->getEglise();


            $dql = $soldeRepo->findBy(['eglise' => $eglise]);
            if ($dql) {
                $id = $dql[0]->getId();
                $activite = $soldeRepo->findOneBySolde($id);
                $mont = $activite->getMontant();
                $j = 0;
                $j = $mont - $total;
                $activite->setMontant($j);
            }

            $dimeglobale->setDeletedFromIp($this->GetIp());
            $dimeglobale->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $dimeglobale->setDeletedBy($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('dimeglobale_index');
    }

}
