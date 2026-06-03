<?php

namespace App\Controller;

use App\Entity\Actiongrace;
use App\Form\ActiongraceType;
use App\Repository\ActiongraceRepository;
use App\Repository\FideleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use DateTime;
use App\Traits\ClientIp;

#[Route('/actiongrace')]
class ActiongraceController extends AbstractController {

    use ClientIp;

    #[Route('/', name: 'actiongrace')]
    public function index(ActiongraceRepository $actiongraceRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_FINANCE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $actiongrace = $actiongraceRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $difference = $actiongraceRepository->getActionByDates();
        return $this->render('actiongrace/index.html.twig', [
                    'actiongraces' => $actiongrace,
                    'differences' => $difference,
        ]);
    }

    #[Route('/{id}/detail', name: 'actiongrace_detail', methods: ['GET', 'POST'])]
    public function detail(Actiongrace $actiongrace): Response {
        if (!$this->isGranted('ROLE_RESPONSABLE_FINANCE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        return $this->render('actiongrace/detail.html.twig', [
                    'actiongrace' => $actiongrace,
        ]);
    }

    #[Route('/{id}/update', name: 'actiongrace_update', methods: ['GET', 'POST'])]
    #[Route('/add', name: 'actiongrace_add', methods: ['GET', 'POST'])]
    public function add(EntityManagerInterface $entityManager, Request $request, FideleRepository $fideleRepository, ?Actiongrace $actiongrace = null): Response {
        if (!$this->isGranted('ROLE_RESPONSABLE_FINANCE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $user = $this->getUser();
        $type = $actiongrace === null ? 'add' : 'update';
        $actiongrace = $actiongrace === null ? new Actiongrace() : $actiongrace;
        $eglise = $this->getUser()->getEglise();
        $fidele = $fideleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $form = $this->createForm(ActiongraceType::class, $actiongrace, ['fidele' => $fidele]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {


            if ($type === 'add') {
                $actiongrace->setCreatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setEglise($user->getEglise())
                        ->setCreatedBy($user)
                ;
                 $this->addFlash('success', 'Enregistreent effectué avec succès.');
            } else {
                $actiongrace->setUpdatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setUpdatedBy($user)
                ;
                 $this->addFlash('success', 'Modification effectuée avec succès.');
            }

            $actiongrace->setCreatedFromIp($this->GetIp());
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
            $actiongrace->setMontant("$total");

            $entityManager->persist($actiongrace);
              $this->addFlash('danger', 'Suppression avec succès.');
            $entityManager->flush(); 

            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'actiongrace_add' : 'actiongrace';
      
            return $this->redirectToRoute($nextAction);
//            return $this->redirectToRoute('actiongrace_index', [], Response::HTTP_SEE_OTHER);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('actiongrace/add.html.twig', [
                    'actiongrace' => $actiongrace,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    #[Route('actiongrace/print', name: 'actiongrace_print', methods: ['GET', 'POST'])]
    public function printactiongrace(ActiongraceRepository $actiongraceRepository): Response {
        if (!$this->isGranted('ROLE_RESPONSABLE_FINANCE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }

        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        $actiongrace = $actiongraceRepository->findAll();

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('actiongrace/print.html.twig', [
            'actiongrace' => $actiongrace,
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (force download)
        $dompdf->stream("mypdf.pdf", [
            "Attachment" => false
        ]);
    }


    #[Route('actiongrace/{id}', name: 'actiongrace_delete', methods: ['POST'])]
    public function delete(Request $request, Actiongrace $actiongrace): Response {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($this->isCsrfTokenValid('delete' . $actiongrace->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();

            $actiongrace->setDeletedFromIp($this->GetIp());
            $actiongrace->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $actiongrace->setDeletedBy($user);
            $entityManager->flush();
            $this->addFlash('danger', 'Suppression avec succès.');
        }

        return $this->redirectToRoute('actiongrace');
    }

}
