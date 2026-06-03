<?php

namespace App\Controller;

use App\Entity\Recommandation;
use App\Form\RecommandationType;
use App\Repository\RecommandationRepository;
use App\Repository\FideleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use DateTime;
use App\Traits\ClientIp;

#[Route('/recommandation')]
class RecommandationController extends AbstractController {

    use ClientIp;

    #[Route('/', name: 'recommandation_index', methods: ['GET'])]
    public function index(RecommandationRepository $recommandationRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $recommandation = $recommandationRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        return $this->render('recommandation/index.html.twig', [
                    'recommandations' => $recommandation,
        ]);
    }

    #[Route('/new', name: 'recommandation_new', methods: ['GET', 'POST'])]
    #[Route('/{id}/edit', name: 'recommandation_edit', methods: ['GET', 'POST'])]
    public function new(Request $request, FideleRepository $fideleRepository, ?Recommandation $recommandation = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $type = $recommandation === null ? 'new' : 'edit';
        $recommandation = $recommandation === null ? new Recommandation() : $recommandation;
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $fidele = $fideleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL, "etatfidele" => 1]);
        $form = $this->createForm(RecommandationType::class, $recommandation, ['fidele' => $fidele]);
        $entityManager = $this->getDoctrine()->getManager();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //Adresse ip de l'utilisateur
            if ($type === 'new') {
                $recommandation->setCreatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setEglise($user->getEglise())
                        ->setCreatedBy($user)
                ;
            } else {
                $recommandation->setUpdatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setUpdatedBy($user)
                ;
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($recommandation);
            $entityManager->flush();
            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'recommandation_new' : 'recommandation_index';
            if ($nextAction) {
                $this->addFlash('success', 'Action effectuée avec succès.');
            }

            return $this->redirectToRoute($nextAction);
//            return $this->redirectToRoute('recommandation_index', [], Response::HTTP_SEE_OTHER);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('recommandation/new.html.twig', [
                    'recommandation' => $recommandation,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    #[Route('/{id}', name: 'recommandation_show', methods: ['GET'])]
    public function show(Recommandation $recommandation): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        return $this->render('recommandation/show.html.twig', [
                    'recommandation' => $recommandation,
        ]);
    }

   
   #[Route('/{id}/print', name: 'recommandation_print', methods: ['GET', 'POST'])]
public function printById(int $id, RecommandationRepository $recommandationRepository, Request $request): Response
{
   // $this->denyAccessUnlessGranted('IS_AUTHEMATICATED_FULLY');
    // if (!$this->isGranted('ROLE_SECRETAIRE')) {
    //     throw $this->createAccessDeniedException('Accès refusé, vous n\'avez pas les droits d\'accès ici!');
    // }

    $eglise = $this->getUser()->getEglise();
    $recommandation = $recommandationRepository->findOneById($id);
    
    if (!$recommandation) {
        throw $this->createNotFoundException('Recommandation non trouvée');
    }
    
    $fidele = $recommandation->getFidele();
  // Convertir la photo du fidèle en base64
    $photoBase64 = null;
    if ($fidele->getPhotoFile()) {
        $photoPath = $this->getParameter('kernel.project_dir') . '/public/uploads/brochures/' . $fidele->getPhotoFile();
        
        if (file_exists($photoPath)) {
            $photoData = base64_encode(file_get_contents($photoPath));
            $photoMime = mime_content_type($photoPath);
            $photoBase64 = 'data:' . $photoMime . ';base64,' . $photoData;
        } else {
            // Essayer dans le dossier photos
            $photoPath = $this->getParameter('kernel.project_dir') . '/public/uploads/photos/' . $fidele->getPhotoFile();
            if (file_exists($photoPath)) {
                $photoData = base64_encode(file_get_contents($photoPath));
                $photoMime = mime_content_type($photoPath);
                $photoBase64 = 'data:' . $photoMime . ';base64,' . $photoData;
            }
        }
    }
    // Gestion de l'image en base64
    $imageBase64 = null;
    if ($eglise->getLogo()) {
        $imagePath = $this->getParameter('kernel.project_dir') . '/public/uploads/photos/' . $eglise->getLogo();
        if (file_exists($imagePath)) {
            $imageData = base64_encode(file_get_contents($imagePath));
            $imageMime = mime_content_type($imagePath);
            $imageBase64 = 'data:' . $imageMime . ';base64,' . $imageData;
        }
    }

    // Configuration Dompdf
    $pdfOptions = new Options();
    $pdfOptions->set('defaultFont', 'Arial');
    $pdfOptions->set('isHtml5ParserEnabled', true);
    $pdfOptions->set('isRemoteEnabled', false); // Désactiver remote car on utilise base64
    $pdfOptions->set('isFontSubsettingEnabled', true);
    
    $dompdf = new Dompdf($pdfOptions);
    
    // Nettoyer le buffer avant de générer
    if (ob_get_length()) {
        ob_end_clean();
    }

    // Générer le HTML
    $html = $this->renderView('recommandation/print.html.twig', [
        'recommandation' => $recommandation,
        'eglise' => $eglise,
        'fidele' => $fidele,
        'imageBase64' => $imageBase64,
          'photoBase64' => $photoBase64,
    ]);

    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    
    // Streamer le PDF
    $dompdf->stream("recommandation_{$recommandation->getReference()}.pdf", [
        "Attachment" => false
    ]);
    
    return new Response();
} 

    #[Route('/{id}', name: 'recommandation_delete', methods: ['POST'])]
    public function delete(Request $request, Recommandation $recommandation): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($this->isCsrfTokenValid('delete' . $recommandation->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();

            $recommandation->setDeletedFromIp($this->GetIp());
            $recommandation->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $recommandation->setDeletedBy($user);
             $this->addFlash('danger', 'Suppression effectuée avec succès.');
            $entityManager->flush();
        }

        return $this->redirectToRoute('recommandation_index');
    }

}
