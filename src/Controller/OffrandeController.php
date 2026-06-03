<?php

namespace App\Controller;

use App\Entity\Offrande;
use App\Entity\Solde;
use App\Form\OffrandeType;
use App\Form\UpdateoffType;
use App\Repository\OffrandeRepository;
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

#[Route('/offrande')]
class OffrandeController extends AbstractController {

    use ClientIp;

    #[Route('/', name: 'offrande')]
    public function index(OffrandeRepository $offrandeRepository, SoldeRepository $soldeRepo): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_FINANCE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $offrande = $offrandeRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $solde = $soldeRepo->findBy(['eglise' => $eglise]);
        return $this->render('offrande/index.html.twig', [
                    'offrandes' => $offrande,
                    'soldes' => $solde,
        ]);
    }

    #[Route('/{id}/detail', name: 'offrande_detail', methods: ['GET', 'POST'])]
    public function detail(Offrande $offrande): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_FINANCE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }

        return $this->render('offrande/detail.html.twig', [
                    'offrande' => $offrande,
        ]);
    }

    #[Route('/{id}/update', name: 'offrande_update', methods: ['GET', 'POST'])]
    public function update(EntityManagerInterface $entityManager, Request $request, Offrande $offrande, SoldeRepository $soldeRepo): Response {
        if (!$this->isGranted('ROLE_RESPONSABLE_FINANCE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $data = json_decode($request->getContent(), true);

        $form = $this->createForm(UpdateoffType::class, $offrande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $offrande->setUpdatedFromIp($this->GetIp());
            $eglise = $this->getUser()->getEglise();
            $user = $this->getUser();
            $offrande->setUpdatedBy($user);

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
                    $off = 0 + $valeur;
                    $soldeEglise->setMontant($off);
                    $soldeEglise->setEglise($eglise);
                    $entityManager->persist($soldeEglise);
                }

                $mont1 = $offrande->getMontant();
                $mon = $valeur + $mont1;
                $offrande->setMontant($mon);
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

                    $soldeEglise = new Solde();
                    $off = 0 - $valeur2;
                    $soldeEglise->setMontant($off);
                    $soldeEglise->setEglise($eglise);
                    $entityManager->persist($soldeEglise);
                }



                $mont2 = $offrande->getMontant();
                $mon1 = $mont2 - $valeur2;
                $offrande->setMontant($mon1);
            }

            $entityManager->persist($offrande);
            $this->addFlash('success', 'Modification effectuée avec succès.');
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('offrande');
        }

        return $this->render('offrande/update.html.twig', [
                    'offrande' => $offrande,
                    'form' => $form->createView(),
        ]);
    }

    #[Route('/add', name: 'offrande_add', methods: ['GET', 'POST'])]
    public function add(EntityManagerInterface $entityManager, SoldeRepository $soldeRepo, Request $request): Response {
        if (!$this->isGranted('ROLE_RESPONSABLE_FINANCE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $offrande = new Offrande();
        $form = $this->createForm(OffrandeType::class, $offrande);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            //Adresse ip de l'utilisateur

            $offrande->setCreatedFromIp($this->GetIp());
            $eglise = $this->getUser()->getEglise();
            $user = $this->getUser();
            $offrande->setCreatedBy($user);
            $offrande->setEglise($eglise);
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
            $offrande->setMontant($total);
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
            $entityManager->persist($offrande);

            $entityManager->flush();
            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'offrande_add' : 'offrande';
            if ($nextAction) {
                $this->addFlash('success', 'Enregistrement avec succès.');
            }
            return $this->redirectToRoute($nextAction);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);

        return $this->render('offrande/add.html.twig', [
                    'offrande' => $offrande,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    #[Route('offrande/print', name: 'offrande_print', methods: ['GET', 'POST'])]
    public function printoffrande(OffrandeRepository $offrandeRepository): Response {

        if (!$this->isGranted('ROLE_RESPONSABLE_FINANCE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        $offrande = $offrandeRepository->findAll();

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('offrande/print.html.twig', [
            'offrande' => $offrande,
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

    #[Route('/{id}', name: 'offrande_delete', methods: ['POST'])]
    public function delete(Request $request, Offrande $offrande, SoldeRepository $soldeRepo): Response {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($this->isCsrfTokenValid('delete' . $offrande->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();
            $total = $offrande->getMontant();
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

            $offrande->setDeletedFromIp($this->GetIp());
            $offrande->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $offrande->setDeletedBy($user);
             $this->addFlash('danger', 'Suppression effectuée avec succès.');
            $entityManager->flush();
        }

        return $this->redirectToRoute('offrande');
    }

}
