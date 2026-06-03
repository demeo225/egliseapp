<?php

namespace App\Controller;

use App\Entity\Ethnie;
use App\Form\EthnieType;
use App\Form\EthnieMultipleType;
use App\Repository\EthnieRepository;
use App\Repository\FideleRepository;
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

#[Route('/ethnie')]
class EthnieController extends AbstractController {
use ClientIp;
    
    #[Route('/', name: 'ethnie')]
    public function index(EthnieRepository $ethnieRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $ethnie = $ethnieRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        return $this->render('ethnie/index.html.twig', [
                    'ethnie' => $ethnie,
        ]);
    }

    #[Route('/detail/{id}', name: 'ethnie_detail', methods: ['GET'])]
    public function detailethnie(Request $request, FideleRepository $fideleRepository) {
        //Recuperation id ethnie
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $idethnie = $request->query->get('id');
        //Recuperation de la liste des fidele par ethnie
        $listeFidele = $fideleRepository->findBy(['ethnie' => $idethnie]);

        return $this->render('ethnie/detail.html.twig', [
                    'fidele' => $listeFidele,
                    'id' => $idethnie,
        ]);
    }
   // #[Route('/{id}/update', name: 'ethnie_update', methods: ['GET', 'POST'])]
    #[Route('/add', name: 'ethnie_add', methods: ['GET', 'POST'])]
    public function add(EntityManagerInterface $entityManager, Request $request, ?Ethnie $ethnie=null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

       
        $user = $this->getUser();
        $type = $ethnie === null ? 'add' : 'update';
        $ethnie = $ethnie === null ? new Ethnie() : $ethnie;
        $form = $this->createForm(EthnieType::class, $ethnie);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

     
           if ($type === 'add') {
                $ethnie->setCreatedFromIp($this->GetIp()) // remplacement de la function par le trait
                    ->setEglise($user->getEglise())
                    //->setIdeglise($user->getEglise()->GetId())
                    ->setCreatedBy($user)
                ;
            } else {
                $ethnie->setUpdatedFromIp($this->GetIp()) // remplacement de la function par le trait
                    ->setUpdatedBy($user)
                ;
            }
            $entityManager->persist($ethnie);
            $entityManager->flush();
            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'ethnie_add' : 'ethnie';
            if ($nextAction) {
                $this->addFlash('success', 'Action effectuée avec succès.');
            }

            return $this->redirectToRoute($nextAction);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('ethnie/add.html.twig', [
                    'ethnie' => $ethnie,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    #[Route('/print', name: 'ethnie_print', methods: ['GET', 'POST'])]
    public function printEthnie(EthnieRepository $ethnieRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $eglise = $this->getUser()->getEglise();
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

        $ethnie = $ethnieRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('ethnie/print.html.twig', [
            'ethnie' => $ethnie,
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
        $dompdf->stream("ekkllesia.pdf", [
            "Attachment" => false
        ]);
    }

    
    
         /**
     * @Route("/search/fideles/{id}", name="ethnie_search_fideles", requirements={"id"="\d+"}, methods={"POST"})
     *
     * @return Response
     */
    public function ethnieSearchFideles(SerializerInterface $serializer, Ethnie $ethnie): Response {
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($ethnie) {
            $fideles = (array) json_decode($serializer->serialize($ethnie->getFidele()->toArray(), 'json', ['groups' => ['public']]));
        } else {
            $fideles = [];
        }

        return new Response($this->renderView('ethnie/listefidele.html.twig', [
                    'fideles' => $fideles
        ]));
    }

    /**
     * @Route("/search/enfants/{id}", name="ethnie_search_enfants", requirements={"id"="\d+"}, methods={"POST"})
     *
     * @return Response
     */
    public function ethnieSearchEnfants(SerializerInterface $serializer, Ethnie $ethnie): Response {
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($ethnie) {
            $enfants = (array) json_decode($serializer->serialize($ethnie->getEnfants()->toArray(), 'json', ['groups' => ['public']]));
        } else {
            $enfants = [];
        }

        return new Response($this->renderView('ethnie/enfant.html.twig', [
                    'enfants' => $enfants
        ]));
    }
    
    
    
     #[Route('/update/datatable/{id}', name: 'ethnie_update_datatable', methods: ['POST'])]
    public function ethnieUpdateDatatable(EntityManagerInterface $entityManager, Request $request, ?Ethnie $ethnie = null): JsonResponse {

        $return = [
            'update' => false,
            'notification' => false,
        ];
        $new_ethnie = \strip_tags($request->request->get('ethnie'));

        // Si l'entité existe et que le nouveau nom de la ethnie apre le strip_tags comporte plus que 0 caractères
        if ($ethnie && strlen($new_ethnie) > 0) {
            // strip_tags pour enlever tout code html
            // évite d'envoyer des balise <script>
            // ref: https://www.php.net/manual/fr/function.strip-tags.php
            $ethnie->setLibelle($new_ethnie);
            $user = $this->getUser();
            $ethnie->setUpdatedBy($user)
                    ->setUpdatedFromIp($this->GetIp());
            $entityManager->persist($ethnie);
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

    #[Route('/add1', name: 'ethnie_add1', methods: ['GET', 'POST'])]
    public function newCommune(Request $request, EntityManagerInterface $entityManager): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $ethnie = new Ethnie();
        $form = $this->createForm(EthnieType::class, $ethnie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ethnie->setCreatedFromIp($this->GetIp());
            $user = $this->getUser();
            $eglise = $this->getUser()->getEglise();
            $ethnie->setCreatedBy($user);
            $ethnie->setEglise($eglise);
            $entityManager->persist($ethnie);
            $entityManager->flush();
            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'ethnie_add1' : 'ethnie';
            if ($nextAction) {
                $this->addFlash('success', 'Action effectuée avec succès.');
            }

            return $this->redirectToRoute($nextAction);
        }

        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('ethnie/add1.html.twig', [
                    'ethnie' => $ethnie,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    
    
    #[Route('/printmembre/', name: 'ethnie_printmembre', methods: ['GET', 'POST'])]
    public function printmembre(EthnieRepository $ethnieRepository, FideleRepository $fideleRepository, Request $request): Response {
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

        //Recuperation de la liste des fidele par ethnie

        $listeMembre = $fideleRepository->findBy(['ethnie' => $id]);
        $ligneethnie = $ethnieRepository->find($id);
        $nomethnie = $ligneethnie->getLibelle();
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('ethnie/printmembre.html.twig', [
            'fideles' => $listeMembre,
            'nomethnie' => $nomethnie,
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

    #[Route('ethnie/{id}', name: 'ethnie_delete', methods: ['POST'])]
    public function delete(Request $request, Ethnie $ethnie): Response {
        if ($this->isCsrfTokenValid('delete' . $ethnie->getId(), $request->request->get('_token'))) {
            if (!$this->isGranted('ROLE_SECRETAIRE')) {
                throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
            }
            $entityManager = $this->getDoctrine()->getManager();


            $ethnie->setDeletedFromIp($this->GetIp());
            $ethnie->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $this->addFlash('danger', 'Suppression avec succès');
            $ethnie->setDeletedBy($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('ethnie');
    }

      // Enregistrement multiple lignes
 #[Route('/multiple/add', name: 'ethnies_multiple_add', methods: ['GET', 'POST'])]
public function addMultiple(EntityManagerInterface $entityManager, Request $request): Response
{
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    if (!$this->isGranted('ROLE_SECRETAIRE')) {
        throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
    }
    
    $user = $this->getUser();
    $eglise = $user->getEglise();
    
    // Récupérer toutes les ethnies existantes pour cette église
    $existingethnies = $entityManager->getRepository(Ethnie::class)->findBy([
        'eglise' => $eglise,
        'deletedAt' => null
    ]);
    
    // Créer un tableau associatif des noms existants
    $existingNames = [];
    foreach ($existingethnies as $existing) {
        $existingNames[strtolower(trim($existing->getLibelle()))] = [
            'id' => $existing->getId(),
            'libelle' => $existing->getLibelle()
        ];
    }
    
    // Créer le formulaire multiple
    $ethniesData = ['ethnies' => []];
    $form = $this->createForm(EthnieMultipleType::class, $ethniesData);
    $form->handleRequest($request);
    
    if ($form->isSubmitted() && $form->isValid()) {
        $data = $form->getData();
        $ethnies = $data['ethnies'];
        $savedCount = 0;
        $errors = [];
        $submittedNames = [];
        
        // Validation de toutes les ethnies
        foreach ($ethnies as $index => $ethnie) {
            $libelle = trim($ethnie->getLibelle());
            $nomLower = strtolower($libelle);
            $lineNumber = $index + 1;
            
            // Vérification champ vide
            if (empty($libelle)) {
                $errors[] = [
                    'line' => $lineNumber,
                    'message' => "Ligne {$lineNumber}: Le libelle de la ethnie ne peut pas être vide."
                ];
                continue;
            }
            
            // Vérification longueur
            if (strlen($libelle) < 2) {
                $errors[] = [
                    'line' => $lineNumber,
                    'message' => "Ligne {$lineNumber}: Le libelle '{$libelle}' est trop court (minimum 2 caractères)."
                ];
                continue;
            }
            
            // Vérification doublon avec base de données
            if (isset($existingNames[$nomLower])) {
                $errors[] = [
                    'line' => $lineNumber,
                    'message' => "Ligne {$lineNumber}: Ethnie '{$libelle}' existe déjà dans votre église."
                ];
                continue;
            }
            
            // Vérification doublon dans la soumission
            if (in_array($nomLower, $submittedNames)) {
                $errors[] = [
                    'line' => $lineNumber,
                    'message' => "Ligne {$lineNumber}: Ethnie '{$libelle}' est en double dans la liste."
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
            return $this->redirectToRoute('ethnies_multiple_add');
        }
        
        // Enregistrement des ethnies
        foreach ($ethnies as $ethnie) {
            $libelle = trim($ethnie->getLibelle());
            
            if (!empty($libelle)) {
                $ethnie
                    ->setLibelle($libelle)
                    ->setCreatedFromIp($this->GetIp())
                    ->setEglise($eglise)
                   // ->setIdeglise($eglise->getId())
                    ->setCreatedBy($user)
                    ->setCreateAt(new \DateTime());
                
                $entityManager->persist($ethnie);
                $savedCount++;
            }
        }
        
        if ($savedCount > 0) {
            $entityManager->flush();
            $this->addFlash('success', sprintf(
                '%d ethnie(s) ont été enregistrée(s) avec succès.',
                $savedCount
            ));
        }
        
        return $this->redirectToRoute('ethnie');
    }
    
    $response = new Response(null, $form->isSubmitted() ? 422 : 200);
    return $this->render('ethnie/multiple_add.html.twig', [
        'form' => $form->createView(),
        'response' => $response,
    ], $response);
}
    //Fin multiple lignes

}
