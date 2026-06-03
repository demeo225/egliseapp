<?php

namespace App\Controller;

use App\Entity\Dime;
use App\Entity\Solde;
use App\Form\DimeType;
use App\Form\UpdatedimeType;
use App\Repository\DimeRepository;
use App\Repository\FideleRepository;
use App\Repository\SoldeRepository;
use App\Traits\ClientIp;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/dime')]
class DimeController extends AbstractController {

    use ClientIp;

    #[Route('/', name: 'dime')]
    public function index(DimeRepository $dimeRepository, SoldeRepository $soldeRepo): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_FINANCE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $dime = $dimeRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $solde = $soldeRepo->findBy(['eglise' => $eglise]);

        $difference = $dimeRepository->getDimeByDates();
        return $this->render('dime/index.html.twig', [
                    'dimes' => $dime,
                    'differences' => $difference,
                    'soldes' => $solde,
        ]);
    }

    #[Route('/{id}/detail', name: 'dime_detail', methods: ['GET', 'POST'])]
    public function detail(Dime $dime): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_FINANCE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        return $this->render('dime/detail.html.twig', [
                    'dime' => $dime,
        ]);
    }

    #[Route('/{id}/update', name: 'dime_update', methods: ['GET', 'POST'])]
    public function update(EntityManagerInterface $entityManager, Request $request, Dime $dime, SoldeRepository $soldeRepo): Response {
        if (!$this->isGranted('ROLE_RESPONSABLE_FINANCE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $form = $this->createForm(UpdatedimeType::class, $dime);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $dime->setUpdatedFromIp($this->GetIp());
            $eglise = $this->getUser()->getEglise();
            $user = $this->getUser();
            $dime->setUpdatedBy($user);

            $nature = $form['typeoff']->getData();

            if ($nature == 1) {
                $valeur = $form['ajout']->getData();

                // On cumule le montant total dans une table Montantoff
                // On crée le solde si solde inexistant ou on incremente le solde
                $dql = $soldeRepo->findBy(['eglise' => $eglise]);

                $mont1 = $dime->getMontant();
                $mon = $valeur + $mont1;
                $dime->setMontant($mon);

                if ($dql) {
                    $id = $dql[0]->getId();
                    $activite = $soldeRepo->findOneBySolde($id);
                    $mont = $activite->getMontant();
                    $j = 0;
                    $j = $mont + $valeur;
                    $activite->setMontant($j);
                } else {
                    $solde = new Solde();
                    $mont1 = 0 + $valeur;
                    $solde->setMontant($mont1);
                    $solde->setEglise($eglise);
                    $entityManager->persist($solde);
                }
            } elseif ($nature == 0) {
                $valeur2 = $form['ajout']->getData();
                $mont2 = $dime->getMontant();
                $mon1 = $mont2 - $valeur2;
                $dime->setMontant($mon1);
                $dql1 = $soldeRepo->findBy(['eglise' => $eglise]);
                if ($dql1) {
                    $id = $dql1[0]->getId();
                    $activite = $soldeRepo->findOneBySolde($id);
                    $montant1 = $activite->getMontant();
                    $j = 0;
                    $j = $montant1 - $valeur2;
                    $activite->setMontant($j);
                } else {
                    $solde = new Solde();
                    $mont1 = 0 - $valeur2;
                    $solde->setMontant($mont1);
                    $solde->setEglise($eglise);
                    $entityManager->persist($solde);
                }
            }

            $entityManager->persist($dime);
             $this->addFlash('success', 'Modification effectuée avec succès.');

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('dime');
        }

        return $this->render('dime/update.html.twig', [
                    'dime' => $dime,
                    'form' => $form->createView(),
        ]);
    }

    #[Route('/add', name: 'dime_add', methods: ['GET', 'POST'])]
    public function add(EntityManagerInterface $entityManager, Request $request, FideleRepository $fideleRepository, SoldeRepository $soldeRepo): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_FINANCE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $dime = new Dime();
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();

        $fidele = $fideleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $form = $this->createForm(DimeType::class, $dime, ['fidele' => $fidele]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {


            $dime->setCreatedFromIp($this->GetIp());
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
            $dime->setMontant($total);

            // On cumule le montant total dans une table Montantdime
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

            $dime->setCreatedBy($user);
            $dime->setEglise($eglise);
            $dime = $form->getData();
            $entityManager->persist($dime);
            $entityManager->flush();
            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'dime_add' : 'dime';
            if ($nextAction) {
                $this->addFlash('success', 'Enregistrement avec succès.');
            }
            return $this->redirectToRoute($nextAction);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('dime/add.html.twig', [
                    'dime' => $dime,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    #[Route('dime/print', name: 'dime_print', methods: ['GET', 'POST'])]
    public function printdime(DimeRepository $dimeRepository): Response {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_FINANCE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        $dime = $$dimeRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('dime/print.html.twig', [
            'dime' => $dime,
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();
        ob_get_clean();
        // Output the generated PDF to Browser (force download)
        $dompdf->stream("mypdf.pdf", [
            "Attachment" => false
        ]);
    }

    #[Route('/{id}', name: 'dime_delete', methods: ['POST'])]
    public function delete(Request $request, Dime $dime, SoldeRepository $soldeRepo): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici! Veuillez vous adresser au secretariat');
        }
        if ($this->isCsrfTokenValid('delete' . $dime->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();

            $eglise = $this->getUser()->getEglise();
            $total = $dime->getMontant();

            $dql = $soldeRepo->findBy(['eglise' => $eglise]);
            if ($dql) {
                $id = $dql[0]->getId();
                $activite = $soldeRepo->findOneBySolde($id);
                $mont = $activite->getMontant();
                $j = 0;
                $j = $mont - $total;
                $activite->setMontant($j);
            }

            $dime->setDeletedFromIp($this->GetIp());
            $dime->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $dime->setDeletedBy($user);
                $this->addFlash('danger', 'Suppression effectuée avec succès.');
            $entityManager->flush();
        }

        return $this->redirectToRoute('dime');
    }

}
