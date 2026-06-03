<?php

namespace App\Controller;

use App\Entity\Quartier;
use App\Form\QuartierType;
use App\Form\QuartierMultipleType;
use App\Repository\CommuneRepository;
use App\Repository\FideleRepository;
use App\Repository\QuartierRepository;
use App\Traits\ClientIp;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/quartier')]
class QuartierController extends AbstractController {

    use ClientIp;

    #[Route('/', name: 'quartier')]
    public function index(QuartierRepository $quartierRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $eglise = $this->getUser()->getEglise();

        $user = $this->getUser();
        $quartier = $quartierRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        return $this->render('quartier/index.html.twig', [
                    'quartier' => $quartier,
        ]);
    }

    #[Route('/detail/{id}', name: 'quartier_detail', methods: ['GET'])]
    public function detailquartier(Request $request, FideleRepository $fideleRepository, QuartierRepository $quartierRepository) {
        //Recuperation id quartier
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $idquartier = $request->query->get('id');
        //Recuperation de la liste des fidele par quartier
        $listeFidele = $fideleRepository->findBy(['quartier' => $idquartier, 'etatfidele' => 1]);
        $lignequartier = $quartierRepository->find($idquartier);
        $nomquartier = $lignequartier->getLibelle();
        return $this->render('quartier/detail.html.twig', [
                    'fideles' => $listeFidele,
                    'id' => $idquartier,
                    'nomquartier' => $nomquartier,
                    'eglise' => $eglise,
        ]);
    }


    /**
     * @Route("/search/fideles/{id}", name="quartier_search_fideles", requirements={"id"="\d+"}, methods={"POST"})
     *
     * @return Response
     */
    public function quartierSearchFideles(SerializerInterface $serializer, Quartier $quartier): Response {
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($quartier) {
            $fideles = (array) json_decode($serializer->serialize($quartier->getFidele()->toArray(), 'json', ['groups' => ['public']]));
        } else {
            $fideles = [];
        }

        return new Response($this->renderView('quartier/listefidele.html.twig', [
                    'fideles' => $fideles
        ]));
    }

    /**
     * @Route("/search/enfants/{id}", name="quartier_search_enfants", requirements={"id"="\d+"}, methods={"POST"})
     *
     * @return Response
     */
    public function quartierSearchEnfants(SerializerInterface $serializer, Quartier $quartier): Response {
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($quartier) {
            $enfants = (array) json_decode($serializer->serialize($quartier->getEnfants()->toArray(), 'json', ['groups' => ['public']]));
        } else {
            $enfants = [];
        }

        return new Response($this->renderView('quartier/enfant.html.twig', [
                    'enfants' => $enfants
        ]));
    }

    #[Route('/add', name: 'quartier_add', methods: ['GET', 'POST'])]
    public function add(EntityManagerInterface $entityManager, Request $request, CommuneRepository $communeRepository): Response {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $quartier = new Quartier();
        $eglise = $this->getUser()->getEglise();
        $commune = $communeRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $form = $this->createForm(QuartierType::class, $quartier, ['commune' => $commune,]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            //Adresse ip de l'utilisateur

            $quartier->setCreatedFromIp($this->GetIp()) // remplacement de la function par le trait
                    ->setEglise($user->getEglise())
                    ->setCreatedBy($user)
            ;

            $quartier->setCreatedFromIp($this->GetIp());

            $entityManager->persist($quartier);
            $entityManager->flush();
            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'quartier_add' : 'quartier';
            if ($nextAction) {
                $this->addFlash('success', 'Action effectuée avec succès.');
            }
            return $this->redirectToRoute($nextAction);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('quartier/add.html.twig', [
                    'quartier' => $quartier,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    #[Route('/update/datatable/{id}', name: 'quartier_update_datatable', methods: ['POST'])]
    public function quartierUpdateDatatable(EntityManagerInterface $entityManager, Request $request, ?Quartier $quartier = null): JsonResponse {

        $return = [
            'update' => false,
            'notification' => false,
        ];
        $new_quartier = \strip_tags($request->request->get('quartier'));

        // Si l'entité existe et que le nouveau nom de la quartier apre le strip_tags comporte plus que 0 caractères
        if ($quartier && strlen($new_quartier) > 0) {
            // strip_tags pour enlever tout code html
            // évite d'envoyer des balise <script>
            // ref: https://www.php.net/manual/fr/function.strip-tags.php
            $quartier->setLibelle($new_quartier);
            $user = $this->getUser();
            $quartier->setUpdatedBy($user)
                    ->setUpdatedFromIp($this->GetIp());
            $entityManager->persist($quartier);
            $entityManager->flush();

            $return = [
                'update' => true,
                'notification' => $this->renderView('notification/toasts.html.twig', [
                        // infos a passé au toasts si besoin
                ])
            ];
        }

        return new JsonResponse($return);
    }

    #[Route('/add1', name: 'quartier_add1', methods: ['GET', 'POST'])]
    public function newQuartier(Request $request, EntityManagerInterface $entityManager, CommuneRepository $communeRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $quartier = new Quartier();
        $eglise = $this->getUser()->getEglise();
        $commune = $communeRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $form = $this->createForm(QuartierType::class, $quartier, ['commune' => $commune,]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $quartier->setCreatedFromIp($this->GetIp());
            $user = $this->getUser();
            $eglise = $this->getUser()->getEglise();
            $quartier->setCreatedBy($user);
            $quartier->setEglise($eglise);
            $entityManager->persist($quartier);
            $entityManager->flush();
            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'quartier_add1' : 'quartier';
            if ($nextAction) {
                $this->addFlash('success', 'Action effectuée avec succès.');
            }

            return $this->redirectToRoute($nextAction);
        }

        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('quartier/add1.html.twig', [
                    'quartier' => $quartier,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    #[Route('/{id}/update', name: 'quartier_update', methods: ['GET', 'POST'])]
    public function updateQuartier(Request $request, Quartier $quartier, CommuneRepository $communeRepository): Response {
        $data = json_decode($request->getContent(), true);
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $user = $this->getUser();
              $eglise = $this->getUser()->getEglise();
        $commune = $communeRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $form = $this->createForm(QuartierType::class, $quartier, ['commune' => $commune,]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //Adresse ip de l'utilisateur
            $user = $this->getUser();

            $quartier->setUpdatedFromIp($this->GetIp());
            $quartier->setUpdatedBy($user);
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Modification avec succès');

            return $this->redirectToRoute('quartier');
        }
        return $this->render('quartier/update.html.twig', [
                    'quartier' => $quartier,
                    'form' => $form->createView(),
        ]);
    }

    #[Route('/print', name: 'quartier_print', methods: ['GET', 'POST'])]
    public function printquartier(QuartierRepository $quartierRepository): Response {

        // Configure Dompdf according to your needs
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();

        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->set(array('isRemoteEnabled' => true));
        $pdfOptions->set('isHtml5ParserEnabled', true);
        $dompdf = new Dompdf($pdfOptions);
        $contxt = stream_context_create([
            'ssl' => [
                'verify_peer' => FALSE,
                'verify_peer_name' => FALSE,
                'allow_self_signed' => TRUE
            ]
        ]);

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        $quartier = $quartierRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('quartier/print.html.twig', [
            'quartier' => $quartier,
            'eglise' => $eglise,
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

    #[Route('/printmembre/', name: 'quartier_printmembre', methods: ['GET', 'POST'])]
    public function printmembre(QuartierRepository $quartierRepository, FideleRepository $fideleRepository, Request $request): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $id = $request->query->get('id');

        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->set(array('isRemoteEnabled' => true));
        $pdfOptions->set('isHtml5ParserEnabled', true);
        $dompdf = new Dompdf($pdfOptions);
        $contxt = stream_context_create([
            'ssl' => [
                'verify_peer' => FALSE,
                'verify_peer_name' => FALSE,
                'allow_self_signed' => TRUE
            ]
        ]);

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        //Recuperation de la liste des fidele par quartier

        $listeMembre = $fideleRepository->findBy(['quartier' => $id]);
        $lignequartier = $quartierRepository->find($id);
        $nomquartier = $lignequartier->getLibelle();
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('quartier/printmembre.html.twig', [
            'fideles' => $listeMembre,
            'nomquartier' => $nomquartier,
            'eglise' => $eglise,
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();
        $x = 505;
        $y = 790;
        $text = "{PAGE_NUM} sur {PAGE_COUNT}";
        $font = $dompdf->getFontMetrics()->get_font('Helvetica', 'normal');
        $size = 10;
        $color = array(0, 0, 0);
        $word_space = 0.0;
        $char_space = 0.0;
        $angle = 0.0;

        $dompdf->getCanvas()->page_text(
                $x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle
        );
        ob_get_clean();
        // Output the generated PDF to Browser (force download)
        $dompdf->stream("mypdf.pdf", [
            "Attachment" => false
        ]);
    }

    #[Route('delete/{id}', name: 'quartier_delete', methods: ['POST'])]
    public function delete(Request $request, Quartier $quartier): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($this->isCsrfTokenValid('delete' . $quartier->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();

            $quartier->setDeletedFromIp($this->getIp());
            $quartier->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $this->addFlash('danger', 'Suppression avec succès.');

            $quartier->setDeletedBy($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('quartier');
    }

      // Nouvelle méthode pour l'enregistrement multiple
    #[Route('/multiple/add', name: 'quartier_multiple_add', methods: ['GET', 'POST'])]
    public function addMultiple(
        EntityManagerInterface $entityManager, 
        Request $request, 
        CommuneRepository $communeRepository
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }

        $user = $this->getUser();
        $eglise = $user->getEglise();

        // Récupérer les départements disponibles
        $communes = $communeRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        
        // Récupérer les quartiers existants pour vérifier les doublons
        $existingQuartiers = $entityManager->getRepository(Quartier::class)->findBy([
            'eglise' => $eglise,
            'deletedAt' => null
        ]);
        
        // Créer un tableau des noms existants
        $existingNames = [];
        foreach ($existingQuartiers as $existing) {
            $key = strtolower(trim($existing->getLibelle()));
            $existingNames[$key] = $existing->getLibelle();
        }
        
        // Créer le formulaire multiple
        $quartiersData = ['quartiers' => []];
        $form = $this->createForm(QuartierMultipleType::class, $quartiersData, [
            'commune' => $communes
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $quartiers = $data['quartiers'];
            $savedCount = 0;
            $errors = [];
            $submittedNames = [];
            
            // Validation de tous les quartiers
            foreach ($quartiers as $index => $quartier) {
                $nom = trim($quartier->getLibelle());
                $nomLower = strtolower($nom);
                $lineNumber = $index + 1;
                
                // Vérification champ vide
                if (empty($nom)) {
                    $errors[] = "Ligne {$lineNumber}: Le nom du quartier ne peut pas être vide.";
                    continue;
                }
                
                // Vérification doublon avec base de données
                if (isset($existingNames[$nomLower])) {
                    $errors[] = "Ligne {$lineNumber}: Le quartier '{$nom}' existe déjà.";
                    continue;
                }
                
                // Vérification doublon dans la soumission
                if (in_array($nomLower, $submittedNames)) {
                    $errors[] = "Ligne {$lineNumber}: Le quartier '{$nom}' est en double dans la liste.";
                    continue;
                }
                
                $submittedNames[] = $nomLower;
            }
            
            // Affichage des erreurs
            if (!empty($errors)) {
                foreach ($errors as $error) {
                    $this->addFlash('danger', $error);
                }
                return $this->redirectToRoute('quartier_multiple_add');
            }
            
            // Enregistrement des quartiers
            foreach ($quartiers as $quartier) {
                $nom = trim($quartier->getLibelle());
                
                if (!empty($nom)) {
                    $quartier
                        ->setLibelle($nom)

                        ->setCommune($quartier->getCommune())
                        ->setCreatedFromIp($this->getIp())
                        ->setEglise($eglise)
                       // ->setIdeglise($eglise->getId())
                        ->setCreatedBy($user)
                        ->setCreateAt(new \DateTime());
                    
                    $entityManager->persist($quartier);
                    $savedCount++;
                }
            }
            
            if ($savedCount > 0) {
                $entityManager->flush();
                $this->addFlash('success', $savedCount . ' quartier(s) ont été enregistré(s) avec succès.');
            }
            
            return $this->redirectToRoute('quartier');
        }
        
        return $this->render('quartier/multiple_add.html.twig', [
            'form' => $form->createView(),
            'communes' => $communes,
        ]);
    }
    
    private function getIp(): string
    {
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
}
