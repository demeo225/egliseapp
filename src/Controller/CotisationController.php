<?php

namespace App\Controller;

use App\Entity\Cotisation;
use App\Form\CotisationType;
use App\Repository\CotisationRepository;
use App\Repository\FidelecotiserRepository;
use App\Traits\ClientIp;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cotisation')]
class CotisationController extends AbstractController {

    use ClientIp;

    #[Route('/', name: 'cotisation')]
    public function index(CotisationRepository $cotisationRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $cotisation = $cotisationRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        return $this->render('cotisation/index.html.twig', [
                    'cotisation' => $cotisation,
        ]);
    }

    #[Route('/{id}/detail', name: 'cotisation_detail', methods: ['GET', 'POST'])]
    public function detail(Cotisation $cotisation): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        return $this->render('cotisation/detail.html.twig', [
                    'cotisation' => $cotisation,
        ]);
    }


    #[Route('/add', name: 'cotisation_add', methods: ['GET', 'POST'])]
    #[Route('/{id}/update', name: 'cotisation_update', methods: ['GET', 'POST'])]
    public function add(EntityManagerInterface $entityManager, Request $request, ?Cotisation $cotisation = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $user = $this->getUser();
        $type = $cotisation === null ? 'add' : 'update';
        $cotisation = $cotisation === null ? new Cotisation() : $cotisation;
        $form = $this->createForm(CotisationType::class, $cotisation);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            if ($type === 'add') {
                $cotisation->setCreatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setEglise($user->getEglise())
                        ->setCreatedBy($user)
                        ->setEtatcotisation("1")
                ;
            } else {
                $cotisation->setUpdatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setUpdatedBy($user)
                ;
            }
            $cotisation = $form->getData();
            $entityManager->persist($cotisation);
            $entityManager->flush();

            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'cotisation_add' : 'cotisation';
            if ($nextAction) {
                $this->addFlash('success', 'Action effectuée avec succès.');
            }

            return $this->redirectToRoute($nextAction);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);

        return $this->render('cotisation/add.html.twig', [
                    'cotisation' => $cotisation,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    #[Route('/print', name: 'cotisation_print', methods: ['GET', 'POST'])]
    public function printcotisation(CotisationRepository $cotisationRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        $cotisation = $cotisationRepository->findAll();

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('cotisation/print.html.twig', [
            'cotisation' => $cotisation,
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

    #[Route('/fidelecotiser/{id}', name: 'cotisation_fidelecotiser', methods: ['GET'])]
    public function detailCotisation(Request $request, FidelecotiserRepository $fidelecotiserRepository, CotisationRepository $cotisationRepo) {
        //Recuperation id cotisation
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_FINANCE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $idcotisation = $request->query->get('id');
        //Recuperation de la liste des fidele par cotisation 
        $listeFidelecotiser = $fidelecotiserRepository->findBy(['cotisation' => $idcotisation, 'deletedAt' => NULL]);
        $ligneCotisation = $cotisationRepo->find($idcotisation);
        $nomCotisation = $ligneCotisation->getObjet();
        return $this->render('cotisation/detail.html.twig', [
                    'fidelecotisers' => $listeFidelecotiser,
                    'id' => $idcotisation,
                    'nomcotisation' => $nomCotisation,
        ]);
    }

    
    
    
    
    #[Route('/fidelecotiser2/{id}', name: 'cotisation_fidelecotiser2', methods: ['GET'])]
    public function detailCotisation2(Request $request, FidelecotiserRepository $fidelecotiserRepository, CotisationRepository $cotisationRepo) {
        //Recuperation id cotisation
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_FINANCE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $idcotisation = $request->query->get('id');
        //Recuperation de la liste des fidele par cotisation 
        $listeFidelecotiser = $fidelecotiserRepository->findBy(['cotisation' => $idcotisation, 'deletedAt' => NULL]);
        $ligneCotisation = $cotisationRepo->find($idcotisation);
        $nomCotisation = $ligneCotisation->getObjet();
        return $this->render('cotisation/detail2.html.twig', [
                    'fidelecotisers' => $listeFidelecotiser,
                    'id' => $idcotisation,
                    'nomcotisation' => $nomCotisation,
        ]);
    }
    
    #[Route('/{id}', name: 'cotisation_delete', methods: ['POST'])]
    public function delete(Request $request, Cotisation $cotisation, CotisationRepository $cotisationRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if ($this->isCsrfTokenValid('delete' . $cotisation->getId(), $request->request->get('_token'))) {
            if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
                throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
            }
            $entityManager = $this->getDoctrine()->getManager();
            $user = $this->getUser();

            $cotisation->setDeletedFromIp($this->GetIp());
            $cotisation->setDeletedAt(new DateTime("now"));
            $cotisation->setDeletedBy($user);
            $this->addFlash('danger', 'Suppression avec succès');
            $entityManager->flush();
        }

        return $this->redirectToRoute('cotisation');
    }

    #[Route('/{id}/cotisation/', name: 'cotisation_cloture', methods: ['POST'])]
    public function clotureCotyisation(Request $request, Cotisation $cotisation): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($this->isCsrfTokenValid('cloture' . $cotisation->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();
            $user = $this->getUser();
            $cotisation->setUpdatedFromIp($this->GetIp()) // remplacement de la function par le trait
                    ->setUpdatedBy($user)
            ;

            $cotisation->setEtatcotisation("0");

            $this->addFlash('warning', 'Cotisation cloturée avec succès.');
            $entityManager->flush();
        }

        return $this->redirectToRoute('cotisation');
    }

    #[Route('/{id}/cotisation', name: 'cotisation_active', methods: ['POST'])]
    public function activeCotisation(Request $request, Cotisation $cotisation): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($this->isCsrfTokenValid('activecotisation' . $cotisation->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();

            $user = $this->getUser();
            $cotisation->setUpdatedFromIp($this->GetIp()) // remplacement de la function par le trait
                    ->setUpdatedBy($user)
            ;
            $cotisation->setEtatcotisation("1");

            $this->addFlash('success', 'Cotisation réactivée avec succès.');
            $entityManager->flush();
        }

        return $this->redirectToRoute('cotisation');
    }

}
