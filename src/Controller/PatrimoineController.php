<?php

namespace App\Controller;

use App\Entity\Patrimoine;
use App\Repository\PatrimoineRepository;
use App\Form\PatrimoineType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Dompdf\Dompdf;
use Dompdf\Options;
use DateTime;
use App\Traits\ClientIp;


#[Route('/patrimoine')]

class PatrimoineController extends AbstractController {
   use ClientIp;
    
    #[Route('/', name: 'patrimoine')]

    public function index(PatrimoineRepository $patrimoineRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
            if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $patrimoine = $patrimoineRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        return $this->render('patrimoine/index.html.twig', [
                    'patrimoine' => $patrimoine,
        ]);
    }

    #[Route('/{id}/detail', name: 'patrimoine_detail', methods: ['GET', 'POST'])]

    public function detail(Patrimoine $patrimoine): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        return $this->render('patrimoine/detail.html.twig', [
                    'patrimoine' => $patrimoine,
        ]);
    }

    #[Route('/{id}/update', name: 'patrimoine_update', methods: ['GET', 'POST'])]

    public function update(Request $request, Patrimoine $patrimoine, int $quantite = null, int $prixunitaire = null, int $prixtotal = null, int $valeuregl = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $data = json_decode($request->getContent(), true);

        $form = $this->createForm(PatrimoineType::class, $patrimoine);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $patrimoine->setUpdatedFromIp($this->GetIp());
            $quantite = $form['quantite']->getData();
            $prixunitaire = $form['prixunitaire']->getData();
            $prixtotal = $quantite * $prixunitaire;
            $patrimoine->setPrixtotal($prixtotal);
            $user = $this->getUser();
            $patrimoine->setUpdatedBy($user);
             $this->addFlash('success', 'Modification effectuée avec succès.');
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('patrimoine');
        }

        return $this->render('patrimoine/update.html.twig', [
                    'patrimoine' => $patrimoine,
                    'form' => $form->createView(),
        ]);
    }

    #[Route('/add', name: 'patrimoine_add', methods: ['GET', 'POST'])]

    public function add(EntityManagerInterface $entityManager, Request $request, int $quantite = null, int $prixunitaire = null, int $prixtotal = null, int $valeuregl = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
            if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $patrimoine = new Patrimoine();
        $form = $this->createForm(PatrimoineType::class, $patrimoine);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            //Adresse ip de l'utilisateur
  
            $patrimoine->setCreatedFromIp($this->GetIp());
            $quantite = $form['quantite']->getData();
            $prixunitaire = $form['prixunitaire']->getData();
            $prixtotal = $quantite * $prixunitaire;
            $patrimoine->setPrixtotal($prixtotal);
            $eglise = $this->getUser()->getEglise();
            $user = $this->getUser();
            $patrimoine->setCreatedBy($user);
            $patrimoine->setEglise($eglise);
            $patrimoine = $form->getData();
            $entityManager->persist($patrimoine);
            $entityManager->flush();

            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'patrimoine_add' : 'patrimoine';
            if ($nextAction) {
                $this->addFlash('success', 'Enregistrement avec succès.');
            }

            return $this->redirectToRoute($nextAction);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('patrimoine/add.html.twig', [
                    'patrimoine' => $patrimoine,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    #[Route('/print', name: 'patrimoine_print', methods: ['GET', 'POST'])]

    public function printpatrimoine(PatrimoineRepository $patrimoineRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
            if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise()->getId();
        $user = $this->getUser();

        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        $patrimoine = $patrimoineRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);

//        $patrimoine = $patrimoineRepository->findAll();
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('patrimoine/print.html.twig', [
            'patrimoine' => $patrimoine,
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

//    #[Route('/{id}', name: 'patrimoine_delete', methods: ['POST'])]
//
//    public function delete(Request $request, Patrimoine $patrimoine, PatrimoineRepository $patrimoineRepository): Response { {
//            $id = $request->request->get("id");
//            $user = $this->getUser();
//            try {
//                $deletePatrimoine = $patrimoineRepository->find($id);
//                if ($deletePatrimoine) {
//                    $deletePatrimoine->setUpdatedAt(new DateTime("now"));
//                    $deletePatrimoine->setDeletedAt(new DateTime("now"));
//                    $deletePatrimoine->setDeletedBy($user);
//                }
//                $entityManager = $this->getDoctrine()->getManager();
//                $entityManager->persist($deletePatrimoine);
//                $entityManager->flush();
//                return new JsonResponse(["code" => 1, "msg" => "Succès"]);
//            } catch (Exception $ex) {
//                return new JsonResponse(["code" => -1, "msg" => $ex->getMessage()]);
//            }
//        }
//    }
    
        #[Route('/{id}', name: 'patrimoine_delete', methods: ['POST'])]
    public function delete(Request $request, Patrimoine $patrimoine): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
            if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($this->isCsrfTokenValid('delete' . $patrimoine->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();

            $patrimoine->setDeletedFromIp($this->GetIp());
            $patrimoine->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $patrimoine->setDeletedBy($user);
             $this->addFlash('danger', 'Suppression effectuée avec succès.');
            $entityManager->flush();
        }

        return $this->redirectToRoute('patrimoine');
    }

}
