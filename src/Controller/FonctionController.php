<?php

namespace App\Controller;

use App\Entity\Fonction;
use App\Form\FonctionType;
use App\Form\FonctionMultipleType;
use App\Repository\FideleRepository;
use App\Repository\FonctionRepository;
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

#[Route('/fonction')]
class FonctionController extends AbstractController {

    use ClientIp;

    #[Route('/', name: 'fonction', methods: ['GET', 'POST'])]
    public function index(FonctionRepository $fonctionRepositonry): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $fonction = $fonctionRepositonry->findBy(['eglise' => $eglise, "deletedAt" => NULL,]);
        return $this->render('fonction/index.html.twig', [
                    'fonction' => $fonction,
        ]);
    }

    #[Route('/detail/{id}', name: 'fonction_detail', methods: ['GET'])]
    public function detailfonction(Request $request, FideleRepository $fideleRepository, FonctionRepository $fonctionRepo) {
        //Recuperation id fonction
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        $idfonction = $request->query->get('id');
        //Recuperation de la liste des fidele par fonction
        $listeFidele = $fideleRepository->findBy(['fonction' => $idfonction, "etatfidele" => 1]);
        $lignefonction = $fonctionRepo->find($idfonction);

        $nom = $lignefonction->getLibelle();
        return $this->render('fonction/detail.html.twig', [
                    'fidele' => $listeFidele,
                    'id' => $idfonction,
                    'nom' => $nom,
        ]);
    }

    
    
         /**
     * @Route("/search/fideles/{id}", name="fonction_search_fideles", requirements={"id"="\d+"}, methods={"POST"})
     *
     * @return Response
     */
    public function fonctionSearchFideles(SerializerInterface $serializer, Fonction $fonction): Response {
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($fonction) {
            $fideles = (array) json_decode($serializer->serialize($fonction->getFideles()->toArray(), 'json', ['groups' => ['public']]));
        } else {
            $fideles = [];
        }

        return new Response($this->renderView('fonction/listefidele.html.twig', [
                    'fideles' => $fideles
        ]));
    }

    
    
    #[Route('/{id}/update', name: 'fonction_update', methods: ['GET', 'POST'])]
    #[Route('/add', name: 'fonction_add', methods: ['GET', 'POST'])]
    public function add(EntityManagerInterface $entityManager, Request $request, ?Fonction $fonction = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();
        $eglise = $this->getUser()->getEglise();
        $type = $fonction === null ? 'add' : 'update';
        $fonction = $fonction === null ? new Fonction() : $fonction;
        $form = $this->createForm(FonctionType::class, $fonction);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {


            if ($type === 'add') {
                $fonction->setCreatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setEglise($user->getEglise())
                       // ->setIdeglise($user->getEglise()->GetId())
                        ->setCreatedBy($user)
                ;
            } else {
                $fonction->setUpdatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setUpdatedBy($user)
                ;
            }
                        //Verification doublon
            $existingFonction = $entityManager->getRepository(Fonction::class)->findOneBy([
            'libelle' => $fonction->getLibelle(),
            'eglise' => $eglise,
            'deletedAt' => null
                ]);

                if ($existingFonction && ($type === 'add' || $existingFonction->getId() !== $fonction->getId())) {
                    $this->addFlash('danger', 'Une fonction avec ce nom existe déjà.');
                    return $this->redirectToRoute('fonctions_multiple_add');
                }
            //Fin doublon
            $entityManager->persist($fonction);
            $entityManager->flush();

            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'fonctions_multiple_add' : 'fonction';
            if ($nextAction) {
                $this->addFlash('success', 'Action effectuée avec succès.');
            }

            return $this->redirectToRoute($nextAction);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);

        return $this->render('fonction/add.html.twig', [
                    'fonction' => $fonction,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    #[Route('/print', name: 'fonction_print', methods: ['GET', 'POST'])]
    public function printfonction(FonctionRepository $fonctionRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();

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

        $fonction = $fonctionRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('fonction/print.html.twig', [
            'fonction' => $fonction,
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


     
    
     #[Route('/update/datatable/{id}', name: 'fonction_update_datatable', methods: ['POST'])]
    public function fonctionUpdateDatatable(EntityManagerInterface $entityManager, Request $request, ?Fonction $fonction = null): JsonResponse {

        $return = [
            'update' => false,
            'notification' => false,
        ];
        $new_fonction = \strip_tags($request->request->get('fonction'));

        // Si l'entité existe et que le nouveau nom de la fonction apre le strip_tags comporte plus que 0 caractères
        if ($fonction && strlen($new_fonction) > 0) {
            // strip_tags pour enlever tout code html
            // évite d'envoyer des balise <script>
            // ref: https://www.php.net/manual/fr/function.strip-tags.php
            $fonction->setLibelle($new_fonction);
            $user = $this->getUser();
            $fonction->setUpdatedBy($user)
                    ->setUpdatedFromIp($this->GetIp());
            $entityManager->persist($fonction);
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

    #[Route('/add1', name: 'fonction_add1', methods: ['GET', 'POST'])]
    public function newFonction(Request $request, EntityManagerInterface $entityManager): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $fonction = new Fonction();
        $form = $this->createForm(FonctionType::class, $fonction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $fonction->setCreatedFromIp($this->GetIp());
            $user = $this->getUser();
            $eglise = $this->getUser()->getEglise();
            $fonction->setCreatedBy($user);
            $fonction->setEglise($eglise);
            $entityManager->persist($fonction);
            $entityManager->flush();
            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'fonction_add1' : 'fonction';
            if ($nextAction) {
                $this->addFlash('success', 'Action effectuée avec succès.');
            }

            return $this->redirectToRoute($nextAction);
        }

        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('fonction/add1.html.twig', [
                    'fonction' => $fonction,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

 
    
    
    #[Route('/printmembre/', name: 'fonction_printmembre', methods: ['GET', 'POST'])]
    public function printmembre(FonctionRepository $fonctionRepository, FideleRepository $fideleRepository, Request $request): Response {
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

        //Recuperation de la liste des fidele par fonction

        $listeMembre = $fideleRepository->findBy(['fonction' => $id]);
        $lignefonction = $fonctionRepository->find($id);
        $nomfonction = $lignefonction->getLibelle();
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('fonction/printmembre.html.twig', [
            'fideles' => $listeMembre,
            'nomfonction' => $nomfonction,
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

    #[Route('fonction/{id}', name: 'fonction_delete', methods: ['POST'])]
    public function delete(Request $request, Fonction $fonction): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($this->isCsrfTokenValid('delete' . $fonction->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();

            $fonction->setDeletedFromIp($this->GetIp());
            $fonction->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $fonction->setDeletedBy($user);
            $this->addFlash('danger', 'Suppression effectuée avec succès.');

            $entityManager->flush();
        }

        return $this->redirectToRoute('fonction');
    }

      // Enregistrement multiple lignes
 #[Route('/multiple/add', name: 'fonctions_multiple_add', methods: ['GET', 'POST'])]
public function addMultiple(EntityManagerInterface $entityManager, Request $request): Response
{
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    if (!$this->isGranted('ROLE_SECRETAIRE')) {
        throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
    }
    
    $user = $this->getUser();
    $eglise = $user->getEglise();
    
    // Récupérer toutes les fonctions existantes pour cette église
    $existingFonctions = $entityManager->getRepository(Fonction::class)->findBy([
        'eglise' => $eglise,
        'deletedAt' => null
    ]);
    
    // Créer un tableau associatif des noms existants
    $existingNames = [];
    foreach ($existingFonctions as $existing) {
        $existingNames[strtolower(trim($existing->getLibelle()))] = [
            'id' => $existing->getId(),
            'nom' => $existing->getLibelle()
        ];
    }
    
    // Créer le formulaire multiple
    $fonctionsData = ['fonctions' => []];
    $form = $this->createForm(FonctionMultipleType::class, $fonctionsData);
    $form->handleRequest($request);
    
    if ($form->isSubmitted() && $form->isValid()) {
        $data = $form->getData();
        $fonctions = $data['fonctions'];
        $savedCount = 0;
        $errors = [];
        $submittedNames = [];
        
        // Validation de toutes les fonctions
        foreach ($fonctions as $index => $fonction) {
            $nom = trim($fonction->getLibelle());
            $nomLower = strtolower($nom);
            $lineNumber = $index + 1;
            
            // Vérification champ vide
            if (empty($nom)) {
                $errors[] = [
                    'line' => $lineNumber,
                    'message' => "Ligne {$lineNumber}: Le nom de la fonction ne peut pas être vide."
                ];
                continue;
            }
            
            // Vérification longueur
            if (strlen($nom) < 2) {
                $errors[] = [
                    'line' => $lineNumber,
                    'message' => "Ligne {$lineNumber}: Le nom '{$nom}' est trop court (minimum 2 caractères)."
                ];
                continue;
            }
            
            // Vérification doublon avec base de données
            if (isset($existingNames[$nomLower])) {
                $errors[] = [
                    'line' => $lineNumber,
                    'message' => "Ligne {$lineNumber}: La fonction '{$nom}' existe déjà dans votre église."
                ];
                continue;
            }
            
            // Vérification doublon dans la soumission
            if (in_array($nomLower, $submittedNames)) {
                $errors[] = [
                    'line' => $lineNumber,
                    'message' => "Ligne {$lineNumber}: La fonction '{$nom}' est en double dans la liste."
                ];
                continue;
            }
            
            $submittedNames[] = $nomLower;
        }
        
        // Affichage des erreurs
        if (!empty($errors)) {
            foreach ($errors as $error) {
                $this->addFlash('danger', $error['message']);
            }
            return $this->redirectToRoute('fonctions_multiple_add');
        }
        
        // Enregistrement des fonctions
        foreach ($fonctions as $fonction) {
            $nom = trim($fonction->getLibelle());
            
            if (!empty($nom)) {
                $fonction
                    ->setLibelle($nom)
                    ->setCreatedFromIp($this->GetIp())
                    ->setEglise($eglise)
                   // ->setIdeglise($eglise->getId())
                    ->setCreatedBy($user)
                    ->setCreatedAt(new \DateTime());
                
                $entityManager->persist($fonction);
                $savedCount++;
            }
        }
        
        if ($savedCount > 0) {
            $entityManager->flush();
            $this->addFlash('success', sprintf(
                '%d fonction(s) ont été enregistrée(s) avec succès.',
                $savedCount
            ));
        }
        
        return $this->redirectToRoute('fonction');
    }
    
    $response = new Response(null, $form->isSubmitted() ? 422 : 200);
    return $this->render('fonction/multiple_add.html.twig', [
        'form' => $form->createView(),
        'response' => $response,
    ], $response);
}
    //Fin multiple lignes
}
