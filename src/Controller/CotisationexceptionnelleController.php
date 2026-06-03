<?php

namespace App\Controller;

use App\Entity\Cotisationexceptionnelle;
use App\Repository\CotisationexceptionnelleRepository;
use App\Form\CotisationexceptionnelleType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Traits\ClientIp;
use DateTime;

#[Route('/cotisationexceptionnelle')]
class CotisationexceptionnelleController extends AbstractController {
    use ClientIp;

    #[Route('/', name: 'cotisationexceptionnelle')]
    public function index(CotisationexceptionnelleRepository $cotisationexceptionnelleRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $cotisationexceptionnelle = $cotisationexceptionnelleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        return $this->render('cotisationexceptionnelle/index.html.twig', [
                    'cotisationexceptionnelles' => $cotisationexceptionnelle,
        ]);
    }

    #[Route('/{id}/detail', name: 'cotisationexceptionnelle_detail', methods: ['GET', 'POST'])]
    public function detail(Cotisationexceptionnelle $cotisationexceptionnelle): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        return $this->render('cotisationexceptionnelle/detail.html.twig', [
                    'cotisationexceptionnelle' => $cotisationexceptionnelle,
        ]);
    }

    #[Route('/{id}/update', name: 'cotisationexceptionnelle_update', methods: ['GET', 'POST'])]
    #[Route('/add', name: 'cotisationexceptionnelle_add', methods: ['GET', 'POST'])]
    public function add(EntityManagerInterface $entityManager, Request $request, ?Cotisationexceptionnelle $cotisationexceptionnelle = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $user = $this->getUser();
        $type = $cotisationexceptionnelle === null ? 'new' : 'edit';
        $cotisationexceptionnelle = $cotisationexceptionnelle === null ? new Cotisationexceptionnelle() : $cotisationexceptionnelle;
        $form = $this->createForm(CotisationexceptionnelleType::class, $cotisationexceptionnelle);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {



            //Adresse ip de l'utilisateur
            if ($type === 'new') {
                $cotisationexceptionnelle->setCreatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setEglise($user->getEglise())
                        ->setCreatedBy($user)
                ;
            } else {
                $cotisationexceptionnelle->setUpdatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setUpdatedBy($user)
                ;
            }

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
            $cotisationexceptionnelle->setMontantpercu("$total");

            $entityManager->persist($cotisationexceptionnelle);
            $entityManager->flush();
            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'cotisationexceptionnelle_add' : 'cotisationexceptionnelle';
            if ($nextAction) {
                $this->addFlash('success', 'Action effectuée avec succès.');
            }

            return $this->redirectToRoute($nextAction);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);

        return $this->render('cotisationexceptionnelle/add.html.twig', [
                    'cotisationexceptionnelle' => $cotisationexceptionnelle,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    #[Route('cotisationexceptionnelle/print', name: 'cotisationexceptionnelle_print', methods: ['GET', 'POST'])]
    public function printcotisationexceptionnelle(CotisationexceptionnelleRepository $cotisationexceptionnelleRepository): Response {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        $cotisationexceptionnelle = $cotisationexceptionnelleRepository->findAll();

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('cotisationexceptionnelle/print.html.twig', [
            'cotisationexceptionnelle' => $cotisationexceptionnelle,
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (force download)
        $dompdf->stream("ekkllesia.pdf", [
            "Attachment" => false
        ]);
    }


    #[Route('cotisationexceptionnelle/{id}', name: 'cotisationexceptionnelle_delete', methods: ['POST'])]
    public function delete(Request $request, Cotisationexceptionnelle $cotisationexceptionnelle): Response {
        if ($this->isCsrfTokenValid('delete' . $cotisationexceptionnelle->getId(), $request->request->get('_token'))) {
            if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
                throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici! Veuillez informer le secretariat');
            }
            $entityManager = $this->getDoctrine()->getManager();

            $cotisationexceptionnelle->setDeletedFromIp($this->GetIp());
            $cotisationexceptionnelle->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $this->addFlash('danger', 'Suppression avec succès.');

            $cotisationexceptionnelle->setDeletedBy($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('cotisationexceptionnelle');
    }

}
