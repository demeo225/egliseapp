<?php

namespace App\Controller;

use App\Entity\Don;
use App\Entity\Solde;
use App\Form\DonType;
use App\Repository\DonRepository;
use App\Repository\SoldeRepository;
use App\Traits\ClientIp;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/don')]
class DonController extends AbstractController {

    use ClientIp;

    #[Route('/', name: 'don_index', methods: ['GET'])]
    public function index(DonRepository $donRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_FINANCE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $don = $donRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        return $this->render('don/index.html.twig', [
                    'dons' => $don,
        ]);
    }

    #[Route('/{id}/edit', name: 'don_edit', methods: ['GET', 'POST'])]
    #[Route('/new', name: 'don_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SoldeRepository $soldeRepo, ?Don $don = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_FINANCE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $user = $this->getUser();
        $eglise = $this->getUser()->getEglise();

        $type = $don === null ? 'new' : 'edit';
        $don = $don === null ? new Don() : $don;
        $form = $this->createForm(DonType::class, $don);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            if ($type === 'new') {
                $don->setCreatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setEglise($user->getEglise())
                        ->setCreatedBy($user)
                        ->setEtatdon(1)
                ;
            } else {
                $don->setUpdatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setUpdatedBy($user)
                ;
            }

            $nature = $form['nature']->getData();
            if ($nature == 'Espece') {
                $valeur = $form['valeurdon']->getData();

                // On cumule le montant total dans une table Montantoff
                $dql = $soldeRepo->findBy(['eglise' => $eglise]);
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
            }

            $entityManager->persist($don);
            $entityManager->flush();

            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'don_new' : 'don_index';
            if ($nextAction) {
                $this->addFlash('success', 'Action  effectuée avec succès.');
            }
            return $this->redirectToRoute($nextAction);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('don/new.html.twig', [
                    'don' => $don,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    #[Route('/{id}', name: 'don_show', methods: ['GET'])]
    public function show(Don $don): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        return $this->render('don/show.html.twig', [
                    'don' => $don,
        ]);
    }

    #[Route('/{id}', name: 'don_delete', methods: ['POST'])]
    public function delete(Request $request, Don $don, SoldeRepository $soldeRepo): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_FINANCE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($this->isCsrfTokenValid('delete' . $don->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();

            $don->setDeletedFromIp($this->GetIp());
            $don->setDeletedAt(new DateTime("now"));
            $eglise = $this->getUser()->getEglise();
            $nature2 = $don->getNature();
            if ($nature2 == 'Espece') {
                $total = $don->getValeurdon();
                $dql = $soldeRepo->findBy(['eglise' => $eglise]);
                $id = $dql[0]->getId();
                $activite = $soldeRepo->findOneBySolde($id);
                $mont = $activite->getMontant();
                $j = 0;
                $j = $mont - $total;
                $activite->setMontant($j);
            }

            $user = $this->getUser();
            $don->setDeletedBy($user);
            $this->addFlash('danger', 'Suppression avec succès.');

            $entityManager->flush();
        }

        return $this->redirectToRoute('don_index');
    }

}
