<?php

namespace App\Controller;

use App\Entity\Programme;
use App\Repository\ProgrammeRepository;
use App\Form\ProgrammeType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use DateTime;
use App\Traits\ClientIp;

#[Route('/programme')]

class ProgrammeController extends AbstractController {
    use ClientIp;
    
    #[Route('/', name: 'programme')]
    public function index(ProgrammeRepository $programmeRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $programme = $programmeRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        return $this->render('programme/index.html.twig', [
                    'programme' => $programme,
                    
        ]);
    }

    #[Route('/detail', name: 'programme_detail', methods: ['GET', 'POST'])]
    public function fullCalendar(ProgrammeRepository $programmeRepository) {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $events = $programmeRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $rdvs = [];

        foreach ($events as $event) {
            $rdvs[] = [
                'id' => $event->getId(),
                'start' => $event->getStart()->format('Y-m-d H:i:s'),
                'end' => $event->getEnd()->format('Y-m-d H:i:s'),
                'title' => $event->getTitle(),
                'description' => $event->getDescription(),
                'backgroundColor' => $event->getBackgroundColor(),
                'borderColor' => $event->getBorderColor(),
                'textColor' => $event->getTextColor(),
                'allDay' => $event->getAllDay(),
            ];
        }

        $data = json_encode($rdvs);


        return $this->render('programme/detail.html.twig', compact('data'));
    }

    #[Route('/{id}/update', name: 'programme_update', methods: ['GET', 'POST'])]
    #[Route('/add', name: 'programme_add', methods: ['GET', 'POST'])]
    public function add(EntityManagerInterface $entityManager, Request $request, ?Programme $programme=null): Response {
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        
            $user = $this->getUser();
        $type = $programme === null ? 'add' : 'update';
        $programme = $programme === null ? new Programme() : $programme;
        $form = $this->createForm(ProgrammeType::class, $programme);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {



           if ($type === 'add') {
                $programme->setCreatedFromIp($this->GetIp()) // remplacement de la function par le trait
                    ->setEglise($user->getEglise())
                    ->setCreatedBy($user)
                ;
            } else {
                $programme->setUpdatedFromIp($this->GetIp()) // remplacement de la function par le trait
                    ->setUpdatedBy($user)
                ;
            }
            $start = $form['start']->getData();
            $end = $form['end']->getData();
            if ($end < $start) {
                $this->addFlash('warning', 'Echec traitement, date de fin est inferieur à la date de debut.');
                return $this->redirect('add');
            } else {


         
                $entityManager->persist($programme);
                $entityManager->flush();
                $nextAction = $form->get('saveAndAdd')->isClicked() ? 'programme_add' : 'programme';
                if ($nextAction) {
                    $this->addFlash('success', 'Action effectuée avec succès.');
                }
                return $this->redirectToRoute($nextAction);
            }
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('programme/add.html.twig', [
                    'programme' => $programme,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

   
#[Route('/api/{id}/edit', name: 'api_event_edit', methods: ['PUT', 'POST'])]
public function majEvent(?Programme $programme, Request $request)
{
    // On récupère les données
    $donnees = json_decode($request->getContent(), true);
    
    // Si c'est une requête POST avec _method=PUT
    if ($request->isMethod('POST') && $request->get('_method') === 'PUT') {
        $donnees = $request->request->all();
    }
    
    // Vérifier si les données sont valides
    if (!$donnees) {
        return $this->json(['error' => 'Données JSON invalides'], 400);
    }

    // Vérifier si l'événement existe
    if (!$programme) {
        return $this->json(['error' => 'Événement non trouvé'], 404);
    }

    // Vérifier les champs obligatoires
    if (!isset($donnees['title']) || empty($donnees['title'])) {
        return $this->json(['error' => 'Le titre est obligatoire'], 400);
    }

    // On hydrate l'objet avec les données
    $programme->setTitle($donnees['title']);
    
    if (isset($donnees['description'])) {
        $programme->setDescription($donnees['description']);
    }
    
    // Gestion des dates
    try {
        if (isset($donnees['start']) && !empty($donnees['start'])) {
            $startDate = new DateTime($donnees['start']);
            $programme->setStart($startDate);
        }
        
        if (isset($donnees['end']) && !empty($donnees['end'])) {
            $endDate = new DateTime($donnees['end']);
            $programme->setEnd($endDate);
        }
    } catch (\Exception $e) {
        return $this->json(['error' => 'Format de date invalide'], 400);
    }
    
    // Gestion de allDay
    if (isset($donnees['allDay'])) {
        $programme->setAllDay(filter_var($donnees['allDay'], FILTER_VALIDATE_BOOLEAN));
    }
    
    // Gestion des couleurs
    if (isset($donnees['backgroundColor'])) {
        $programme->setBackgroundColor($donnees['backgroundColor']);
    }
    if (isset($donnees['borderColor'])) {
        $programme->setBorderColor($donnees['borderColor']);
    }
    if (isset($donnees['textColor'])) {
        $programme->setTextColor($donnees['textColor']);
    }

    // Sauvegarde
    $em = $this->getDoctrine()->getManager();
    $em->persist($programme);
    $em->flush();

    return $this->json(['success' => true, 'message' => 'Événement mis à jour avec succès']);
}

    #[Route('/print', name: 'programme_print', methods: ['GET', 'POST'])]

    public function printprogramme(ProgrammeRepository $programmeRepository): Response {
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }

        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        $programme = $programmeRepository->findAll();

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('programme/print.html.twig', [
            'programme' => $programme,
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


    #[Route('/{id}', name: 'programme_delete', methods: ['POST'])]
    public function delete(Request $request, Programme $programme): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($this->isCsrfTokenValid('delete' . $programme->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();


            $programme->setDeletedFromIp($this->GetIp());
            $programme->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $this->addFlash('danger', 'Suppression avec succès');
            $programme->setDeletedBy($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('programme');
    }

}
