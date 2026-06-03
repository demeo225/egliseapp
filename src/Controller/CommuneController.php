<?php

namespace App\Controller;

use App\Entity\Commune;
use App\Form\CommuneType;
use App\Form\CommuneMultipleType ;
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

#[Route('/commune')]
class CommuneController extends AbstractController {

    use ClientIp;

    #[Route('/', name: 'commune')]
    public function index(CommuneRepository $communeRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $commune = $communeRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        return $this->render('commune/index.html.twig', [
                    'commune' => $commune,
        ]);
    }

//    #[Route('/detail/{id}', name: 'commune_detail', methods: ['GET'])]
//    public function detailcommune(Request $request, QuartierRepository $quartierRepository, CommuneRepository $communeRepo) {
//        //Recuperation id commune
//        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
//        if (!$this->isGranted('ROLE_SECRETAIRE')) {
//            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
//        }
//        $eglise = $this->getUser()->getEglise();
//        $user = $this->getUser();
//        $idcommune = $request->query->get('id');
//        //Recuperation de la liste des fidele par commune
//        $listeQuartier = $quartierRepository->findBy(['commune' => $idcommune, 'deletedAt' => NULL]);
//        $ligneCommune = $communeRepo->find($idcommune);
//        $nomCommune = $ligneCommune->getNom();
//        return $this->render('commune/detail.html.twig', [
//                    'quartier' => $listeQuartier,
//                    'id' => $idcommune,
//                    'nomcommune' => $nomCommune,
//                    'eglise' => $eglise,
//        ]);
//    }
//    #[Route('/enfant/{id}', name: 'commune_enfant', methods: ['GET'])]
//    public function listeEnfant(Request $request, EnfantRepository $enfantRepository, CommuneRepository $communeRepo) {
//        //Recuperation id commune
//        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
//        if (!$this->isGranted('ROLE_SECRETAIRE')) {
//            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
//        }
//        $eglise = $this->getUser()->getEglise();
//        $user = $this->getUser();
//        $idcommune = $request->query->get('id');
//        //Recuperation de la liste des fidele par commune
//        $listeEnfant = $enfantRepository->findBy(['commune' => $idcommune, 'deletedAt' => NULL]);
//        $ligneCommune = $communeRepo->find($idcommune);
//        $nomCommune = $ligneCommune->getNom();
//        return $this->render('commune/enfant.html.twig', [
//                    'enfants' => $listeEnfant,
//                    'id' => $idcommune,
//                    'nomcommune' => $nomCommune,
//                    'eglise' => $eglise,
//        ]);
//    }

    #[Route('/fidele/{id}', name: 'commune_fidele', methods: ['GET'])]
    public function listeAdulte(Request $request, FideleRepository $fideleRepository, CommuneRepository $communeRepo) {
        //Recuperation id commune
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $idcommune = $request->query->get('id');
        //Recuperation de la liste des fidele par commune
        $listeFidele = $fideleRepository->findBy(['commune' => $idcommune, 'deletedAt' => NULL]);
        $ligneCommune = $communeRepo->find($idcommune);
        $nomCommune = $ligneCommune->getNom();
        return $this->render('commune/fidele.html.twig', [
                    'fideles' => $listeFidele,
                    'id' => $idcommune,
                    'nomcommune' => $nomCommune,
                    'eglise' => $eglise,
        ]);
    }

    /**
     * @Route("/search/fideles/{id}", name="commune_search_fideles", requirements={"id"="\d+"}, methods={"POST"})
     *
     * @return Response
     */
    public function communeSearchFideles(SerializerInterface $serializer, Commune $commune): Response {
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($commune) {
            $fideles = (array) json_decode($serializer->serialize($commune->getFideles()->toArray(), 'json', ['groups' => ['public']]));
        } else {
            $fideles = [];
        }

        return new Response($this->renderView('commune/listefidele.html.twig', [
                    'fideles' => $fideles
        ]));
    }

    /**
     * @Route("/search/quartiers/{id}", name="commune_search_quartiers", requirements={"id"="\d+"}, methods={"POST"})
     *
     * @return Response
     */
    public function communeSearchQuartiers(SerializerInterface $serializer, Commune $commune): Response {
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($commune) {
            $quartiers = (array) json_decode($serializer->serialize($commune->getQuartiers()->toArray(), 'json', ['groups' => ['public']]));
        } else {
            $quartiers = [];
        }

        return new Response($this->renderView('commune/detail.html.twig', [
                    'quartiers' => $quartiers
        ]));
    }

    /**
     * @Route("/search/enfants/{id}", name="commune_search_enfants", requirements={"id"="\d+"}, methods={"POST"})
     *
     * @return Response
     */
    public function communeSearchEnfantss(SerializerInterface $serializer, Commune $commune): Response {
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($commune) {
            $enfants = (array) json_decode($serializer->serialize($commune->getEnfants()->toArray(), 'json', ['groups' => ['public']]));
        } else {
            $enfants = [];
        }

        return new Response($this->renderView('commune/enfant.html.twig', [
                    'enfants' => $enfants
        ]));
    }

     #[Route('/commune/{id}/update', name: 'commune_update', methods: ['GET', 'POST'])]
    #[Route('/add', name: 'commune_add', methods: ['GET', 'POST'])]
    public function add(EntityManagerInterface $entityManager, Request $request, ?Commune $commune = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $user = $this->getUser();
        $type = $commune === null ? 'add' : 'update';
        $commune = $commune === null ? new Commune() : $commune;
        $form = $this->createForm(CommuneType::class, $commune);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $eglise = $this->getUser()->getEglise();

            //Verification doublon
            $existingCommune = $entityManager->getRepository(Commune::class)->findOneBy([
            'nom' => $commune->getNom(),
            'eglise' => $eglise,
            'deletedAt' => null
                ]);

                if ($existingCommune && ($type === 'add' || $existingCommune->getId() !== $commune->getId())) {
                    $this->addFlash('warning', 'Une commune avec ce nom existe déjà.');
                    return $this->redirectToRoute('commune_add1');
                }
            //Fin doublon

            if ($type === 'add') {
                $commune->setCreatedFromIp($this->GetIp()) // remplacement de la function par le trait
                ->setEglise($user->getEglise())
               // ->setIdeglise($eglise->getId())
                ->setCreatedBy($user)

                ;
            } else {
                $commune->setUpdatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setUpdatedBy($user)


                ;
            }
            $entityManager->persist($commune);
            $entityManager->flush(); 

            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'commune_add' : 'commune';
            if ($nextAction) {
                $this->addFlash('success', 'Action effectuée avec succès.');
            }

            return $this->redirectToRoute($nextAction);
        }

        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('commune/add.html.twig', [
                    'commune' => $commune,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    #[Route('/add1', name: 'commune_add1', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $commune = new Commune();
        $form = $this->createForm(CommuneType::class, $commune);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
         $user = $this->getUser();
            $eglise = $this->getUser()->getEglise();
            $commune->setCreatedFromIp($this->GetIp());
               //Verification doublon
            $existingCommune = $entityManager->getRepository(Commune::class)->findOneBy([
            'nom' => $commune->getNom(),
            'eglise' => $eglise,
            'deletedAt' => null
                ]);

                if ($existingCommune && ( $existingCommune->getId() !== $commune->getId())) {
                    $this->addFlash('danger', 'Une commune avec ce nom existe déjà.');
                    return $this->redirectToRoute('commune_add1');
                }
            //Fin doublon
        
            $commune->setCreatedBy($user);
            $commune->setEglise($eglise);
           // $commune->setIdeglise($eglise->getId());
            $entityManager->persist($commune);
            $entityManager->flush();
            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'commune_add1' : 'commune';
            if ($nextAction) {
                $this->addFlash('success', 'Action effectuée avec succès.');
            }

            return $this->redirectToRoute($nextAction);
        }

        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('commune/add1.html.twig', [
                    'commune' => $commune,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    #[Route('/update/datatable/{id}', name: 'commune_update_datatable', methods: ['POST'])]
    public function communeUpdateDatatable(EntityManagerInterface $entityManager, Request $request, ?Commune $commune = null): JsonResponse {

        $return = [
            'update' => false,
            'notification' => false,
        ];
        $new_commune = \strip_tags($request->request->get('commune'));

        // Si l'entité existe et que le nouveau nom de la commune apre le strip_tags comporte plus que 0 caractères
        if ($commune && strlen($new_commune) > 0) {
            // strip_tags pour enlever tout code html
            // évite d'envoyer des balise <script>
            // ref: https://www.php.net/manual/fr/function.strip-tags.php
            $commune->setNom($new_commune);
            $user = $this->getUser();
            $commune->setUpdatedBy($user)
                    ->setUpdatedFromIp($this->GetIp());
            $entityManager->persist($commune);
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

    
    #[Route('/print', name: 'commune_print', methods: ['GET', 'POST'])]
    public function printcommune(CommuneRepository $communeRepository): Response {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
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

        $commune = $communeRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('commune/print.html.twig', [
            'commune' => $commune,
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

    #[Route('/printmembre/', name: 'commune_printmembre', methods: ['GET', 'POST'])]
    public function printMembre(CommuneRepository $communeRepository, FideleRepository $fideleRepository, Request $request): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $id = $request->query->get('id');
        $this->domPdf = new Dompdf();

        $pdfOptions = new Options();
        $pdfOptions->setIsRemoteEnabled(true);
        $pdfOptions->setIsHtml5ParserEnabled(true);
        $pdfOptions->setTempDir('temp'); // temp folder with write permission

        $this->domPdf->setOptions($pdfOptions);
        $dompdf = new Dompdf($pdfOptions);
        $contxt = stream_context_create([
            'ssl' => [
                'verify_peer' => FALSE,
                'verify_peer_name' => FALSE,
                'allow_self_signed' => TRUE
            ]
        ]);
        $dompdf->setHttpContext($contxt);
        // Instantiate Dompdf with our options
        //  $dompdf = new Dompdf($pdfOptions);
        //Recuperation de la liste des fidele par commune

        $listeMembre = $fideleRepository->findBy(['commune' => $id]);
        $lignecommune = $communeRepository->find($id);
        $nomcommune = $lignecommune->getNom();
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('commune/printmembre.html.twig', [
            'fideles' => $listeMembre,
            'nomcommune' => $nomcommune,
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

    #[Route('/printquartier/', name: 'commune_printquartier', methods: ['GET', 'POST'])]
    public function printquartier(QuartierRepository $quartierRepository, CommuneRepository $communeRepository, Request $request): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $id = $request->query->get('id');

        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        //Recuperation de la liste des fidele par quartier

        $listeQuartier = $quartierRepository->findBy(['commune' => $id]);
        $lignecommune = $communeRepository->find($id);
        $nomcommune = $lignecommune->getNom();
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('commune/printquartier.html.twig', [
            'quartier' => $listeQuartier,
            'nomcommune' => $nomcommune,
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

    #[Route('/commune/delete/{id}', name: 'commune_delete', methods: ['POST'])]
    public function delete(Request $request, Commune $commune): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if ($this->isCsrfTokenValid('delete' . $commune->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();
            if (!$this->isGranted('ROLE_SECRETAIRE')) {
                throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
            }


            $commune->setDeletedFromIp($this->GetIp());
            $commune->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $commune->setDeletedBy($user);
            $this->addFlash('danger', 'Suppression avec succès.');

            $entityManager->flush();
        }

        return $this->redirectToRoute('commune');
    }

    // Enregistrement multiple lignes
 #[Route('/multiple/add', name: 'communes_multiple_add', methods: ['GET', 'POST'])]
public function addMultiple(EntityManagerInterface $entityManager, Request $request): Response
{
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    if (!$this->isGranted('ROLE_SECRETAIRE')) {
        throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
    }
    
    $user = $this->getUser();
    $eglise = $user->getEglise();
    
    // Récupérer toutes les communes existantes pour cette église
    $existingCommunes = $entityManager->getRepository(Commune::class)->findBy([
        'eglise' => $eglise,
        'deletedAt' => null
    ]);
    
    // Créer un tableau associatif des noms existants
    $existingNames = [];
    foreach ($existingCommunes as $existing) {
        $existingNames[strtolower(trim($existing->getNom()))] = [
            'id' => $existing->getId(),
            'nom' => $existing->getNom()
        ];
    }
    
    // Créer le formulaire multiple
    $communesData = ['communes' => []];
    $form = $this->createForm(CommuneMultipleType::class, $communesData);
    $form->handleRequest($request);
    
    if ($form->isSubmitted() && $form->isValid()) {
        $data = $form->getData();
        $communes = $data['communes'];
        $savedCount = 0;
        $errors = [];
        $submittedNames = [];
        
        // Validation de toutes les communes
        foreach ($communes as $index => $commune) {
            $nom = trim($commune->getNom());
            $nomLower = strtolower($nom);
            $lineNumber = $index + 1;
            
            // Vérification champ vide
            if (empty($nom)) {
                $errors[] = [
                    'line' => $lineNumber,
                    'message' => "Ligne {$lineNumber}: Le nom de la commune ne peut pas être vide."
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
                    'message' => "Ligne {$lineNumber}: La commune '{$nom}' existe déjà dans votre église."
                ];
                continue;
            }
            
            // Vérification doublon dans la soumission
            if (in_array($nomLower, $submittedNames)) {
                $errors[] = [
                    'line' => $lineNumber,
                    'message' => "Ligne {$lineNumber}: La commune '{$nom}' est en double dans la liste."
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
            return $this->redirectToRoute('communes_multiple_add');
        }
        
        // Enregistrement des communes
        foreach ($communes as $commune) {
            $nom = trim($commune->getNom());
            
            if (!empty($nom)) {
                $commune
                    ->setNom($nom)
                    ->setCreatedFromIp($this->GetIp())
                    ->setEglise($eglise)
                  //  ->setIdeglise($eglise->getId())
                    ->setCreatedBy($user)
                    ->setCreateAt(new \DateTime());
                
                $entityManager->persist($commune);
                $savedCount++;
            }
        }
        
        if ($savedCount > 0) {
            $entityManager->flush();
            $this->addFlash('success', sprintf(
                '%d commune(s) ont été enregistrée(s) avec succès.',
                $savedCount
            ));
        }
        
        return $this->redirectToRoute('commune');
    }
    
    $response = new Response(null, $form->isSubmitted() ? 422 : 200);
    return $this->render('commune/multiple_add.html.twig', [
        'form' => $form->createView(),
        'response' => $response,
    ], $response);
}
    //Fin multiple lignes

}
