<?php

namespace App\Controller;

use App\Entity\Famille;
use App\Form\FamilleType;
use App\Form\FamilleMultipleType;
use App\Form\UserfamilleType;
use App\Repository\CotisationfamilleRepository;
use App\Repository\FamilleRepository;
use App\Repository\FideleRepository;
use App\Repository\PresencefamilleRepository;
use App\Repository\SeancefamilleRepository;
use App\Repository\UserRepository;
use App\Repository\ZoneRepository;
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

#[Route('/famille')]
class FamilleController extends AbstractController {
use ClientIp;
    
    #[Route('/', name: 'famille')]
    public function index(FamilleRepository $familleRepository): Response {
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $famille = $familleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        return $this->render('famille/index.html.twig', [
                    'famille' => $famille,
        ]);
    }

    #[Route('/detail/{id}', name: 'famille_detail', methods: ['GET'])]
    public function detailfamille(Request $request, FideleRepository $fideleRepository) {
        //Recuperation id famille
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $idfamille = $request->query->get('id');
        //Recuperation de la liste des fidele par famille
        $listeFidele = $fideleRepository->findBy(['famille' => $idfamille, 'deletedAt' => NULL, 'etatfidele' => 1]);
        return $this->render('famille/detail.html.twig', [
                    'fidele' => $listeFidele,
                    'id' => $idfamille,
        ]);
    }

    #[Route('/activite/{id}', name: 'famille_activite', methods: ['GET'])]
    public function activiteFamille(Request $request, SeancefamilleRepository $activiteRepository, FamilleRepository $familleRepo) {
        //Recuperation id famille
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $idfamille = $request->query->get('id');
        //Recuperation de la liste des fidele par famille
        $listeActivite = $activiteRepository->findBy(['famille' => $idfamille, "deletedAt" => NULL]);
        $ligneFamille = $familleRepo->find($idfamille);
        $nomfamille = $ligneFamille->getNom();
        return $this->render('famille/activite.html.twig', [
                    'activitefamilles' => $listeActivite,
                    'nomfamille' => $nomfamille,
                    'eglise' => $eglise,
        ]);
    }

    #[Route('/cotisation/{id}', name: 'famille_cotisation', methods: ['GET'])]
    public function cotisationFamille(Request $request, CotisationfamilleRepository $cotisationRepository, FamilleRepository $familleRepo) {
        //Recuperation id famille
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $idfamille = $request->query->get('id');
        //Recuperation de la liste des fidele par famille
        $listeCotisation = $cotisationRepository->findBy(['famille' => $idfamille, "deletedAt" => NULL]);
        $ligneFamille = $familleRepo->find($idfamille);
        $nomfamille = $ligneFamille->getNom();
        return $this->render('famille/cotisation.html.twig', [
                    'cotisationfamilles' => $listeCotisation,
                    'nomfamille' => $nomfamille,
                    'eglise' => $eglise,
        ]);
    }

   // #[Route('/{id}/update', name: 'famille_update', methods: ['GET', 'POST'])]
    #[Route('/add', name: 'famille_add', methods: ['GET', 'POST'])]
    public function add(EntityManagerInterface $entityManager, Request $request, ZoneRepository $zoneRepository): Response {
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
      
        $user = $this->getUser();

        $famille = new Famille() ;
        $eglise = $this->getUser()->getEglise();
        $zone = $zoneRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $form = $this->createForm(FamilleType::class, $famille, ['zone' => $zone,]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

             
                $famille->setCreatedFromIp($this->GetIp()) // remplacement de la function par le trait
                    ->setEglise($user->getEglise())
                    ->setCreatedBy($user)
                ;
            $entityManager->persist($famille);
            $entityManager->flush();
            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'famille_add' : 'famille';
            if ($nextAction) {
                $this->addFlash('success', 'Action effectuée avec succès.');
            }
            return $this->redirectToRoute($nextAction);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('famille/add.html.twig', [
                    'famille' => $famille,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    
        #[Route('/{id}/update', name: 'famille_update', methods: ['GET', 'POST'])]
    public function updateFamille(EntityManagerInterface $entityManager, Request $request,Famille $famille, ZoneRepository $zoneRepository): Response {
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
      
        $user = $this->getUser();
        $eglise = $this->getUser()->getEglise();
        $zone = $zoneRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $form = $this->createForm(FamilleType::class, $famille, ['zone' => $zone,]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

             
                $famille->setCreatedFromIp($this->GetIp()) // remplacement de la function par le trait
                    ->setEglise($user->getEglise())
                    ->setCreatedBy($user)
                ;
            $entityManager->persist($famille);
            $entityManager->flush();
            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'famille_add1' : 'famille';
            if ($nextAction) {
                $this->addFlash('success', 'Modification effectuée avec succès.');
            }
            return $this->redirectToRoute($nextAction);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('famille/update.html.twig', [
                    'famille' => $famille,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    
    
    
    #[Route('/add1', name: 'famille_add1', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, ZoneRepository $zoneRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $famille = new Famille();
              $user = $this->getUser();
        $eglise = $this->getUser()->getEglise();
        $zone = $zoneRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $form = $this->createForm(FamilleType::class, $famille, ['zone' => $zone,]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $famille->setCreatedFromIp($this->GetIp());
            $user = $this->getUser();
            $eglise = $this->getUser()->getEglise();
            $famille->setCreatedBy($user);
            $famille->setEglise($eglise);
            $entityManager->persist($famille);
            $entityManager->flush();
            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'famille_add1' : 'famille';
            if ($nextAction) {
                $this->addFlash('success', 'Action effectuée avec succès.');
            }

            return $this->redirectToRoute($nextAction);
        }

        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('famille/add1.html.twig', [
                    'famille' => $famille,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    #[Route('/update/datatable/{id}', name: 'famille_update_datatable', methods: ['POST'])]
    public function familleUpdateDatatable(EntityManagerInterface $entityManager, Request $request, ?Famille $famille = null): JsonResponse {

        $return = [
            'update' => false,
            'notification' => false,
        ];
        $new_famille = \strip_tags($request->request->get('famille'));

        // Si l'entité existe et que le nouveau nom de la famille apre le strip_tags comporte plus que 0 caractères
        if ($famille && strlen($new_famille) > 0) {
            // strip_tags pour enlever tout code html
            // évite d'envoyer des balise <script>
            // ref: https://www.php.net/manual/fr/function.strip-tags.php
            $famille->setNom($new_famille);
            $user = $this->getUser();
            $famille->setUpdatedBy($user)
                    ->setUpdatedFromIp($this->GetIp());
            $entityManager->persist($famille);
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


    
    
    #[Route('/print', name: 'famille_print', methods: ['GET', 'POST'])]
    public function printfamille(FamilleRepository $familleRepository): Response {
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

        $famille = $familleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('famille/print.html.twig', [
            'famille' => $famille,
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
            "Attachment" => false,
        ]);
    }

    #[Route('/printmembre/', name: 'famille_printmembre', methods: ['GET', 'POST'])]
    public function printFidele(FamilleRepository $familleRepository, FideleRepository $fideleRepository, Request $request): Response {
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
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

        //Recuperation de la liste des fidele par famille

        $listeMembre = $fideleRepository->findBy(['famille' => $id, "deletedAt" => NULL, "etatfidele" => 1]);
        $lignefamille = $familleRepository->find($id);
        $nomfamille = $lignefamille->getNom();
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('famille/printmembre.html.twig', [
            'fideles' => $listeMembre,
            'nomfamille' => $nomfamille,
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

    #[Route('/presence/{id}', name: 'famille_presence', methods: ['GET'])]
    public function presenceFamille(Request $request, PresencefamilleRepository $presencefamilleRepository, FamilleRepository $familleRepo) {
        //Recuperation id famille
        if (!$this->isGranted('ROLE_RESPONSABLE_CELLULE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $idfamille = $request->query->get('id');
        //Recuperation de la liste des fidele par famille
        $listePresence = $presencefamilleRepository->findBy(['famille' => $idfamille, "deletedAt" => NULL]);
        $ligneFamille = $familleRepo->find($idfamille);
        $nomfamille = $ligneFamille->getNom();
        return $this->render('famille/presence.html.twig', [
                    'presencefamilles' => $listePresence,
                    'nomfamille' => $nomfamille,
                    'eglise' => $eglise,
        ]);
    }


//    #[Route('/enfant/{id}', name: 'famille_enfant', methods: ['GET'])]
//    public function listeEnfant(Request $request, EnfantRepository $enfantRepository, FamilleRepository $familleRepo) {
//        //Recuperation id famille
//        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
//        if (!$this->isGranted('ROLE_SECRETAIRE')) {
//            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
//        }
//        $user = $this->getUser();
//        $idfamille = $request->query->get('id');
//        //Recuperation de la liste des fidele par famille
//        $listeEnfant = $enfantRepository->findBy(['famille' => $idfamille, 'deletedAt' => NULL]);
//        $ligneFamille = $familleRepo->find($idfamille);
//        $nomFamille = $ligneFamille->getNom();
//        return $this->render('famille/enfant.html.twig', [
//                    'enfants' => $listeEnfant,
//                    'id' => $idfamille,
//                    'nomfamille' => $nomFamille,
//        ]);
//    }

    
         /**
     * @Route("/search/fideles/{id}", name="famille_search_fideles", requirements={"id"="\d+"}, methods={"POST"})
     *
     * @return Response
     */
    public function familleSearchFideles(SerializerInterface $serializer, Famille $famille): Response {
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($famille) {
            $fideles = (array) json_decode($serializer->serialize($famille->getFidele()->toArray(), 'json', ['groups' => ['public']]));
        } else {
            $fideles = [];
        }

        return new Response($this->renderView('famille/listefidele.html.twig', [
                    'fideles' => $fideles
        ]));
    }

    /**
     * @Route("/search/enfants/{id}", name="famille_search_enfants", requirements={"id"="\d+"}, methods={"POST"})
     *
     * @return Response
     */
    public function familleSearchEnfants(SerializerInterface $serializer, Famille $famille): Response {
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($famille) {
            $enfants = (array) json_decode($serializer->serialize($famille->getEnfant()->toArray(), 'json', ['groups' => ['public']]));
        } else {
            $enfants = [];
        }

        return new Response($this->renderView('famille/enfant.html.twig', [
                    'enfants' => $enfants
        ]));
    }
    
    #[Route('/{id}/userfamille', name: 'famille_userfamille', methods: ['GET', 'POST'])]
    public function userfamille(Request $request, Famille $famille, UserRepository $userRepository): Response {
        $data = json_decode($request->getContent(), true);
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user1 = $this->getUser();
        $user = $userRepository->findBy(['eglise' => $eglise, 'etat' => 1]);
        $form = $this->createForm(UserfamilleType::class, $famille, ['user' => $user,]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

                //Adresse ip de l'utilisateur
                /** @var User[] $selectedUsers */
            $selectedUsers = $form->get('users')->getData();

            foreach ($selectedUsers as $user) {
                $user->setFamille($famille);
            }

      
            $famille->setUpdatedFromIp($this->GetIp());
            $famille->setUpdatedBy($user1);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('famille');
        }

        return $this->render('famille/userfamille.html.twig', [
                    'famille' => $famille,
                    'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'famille_delete', methods: ['POST'])]
    public function delete(Request $request, Famille $famille): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($this->isCsrfTokenValid('delete' . $famille->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();


            $famille->setDeletedFromIp($this->GetIp());
            $famille->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $famille->setDeletedBy($user);
            $this->addFlash('danger', 'Suppression avec succès');
            $entityManager->flush();
        }

        return $this->redirectToRoute('famille');
    }

      // Nouvelle méthode pour l'enregistrement multiple
    #[Route('/multiple/add', name: 'famille_multiple_add', methods: ['GET', 'POST'])]
    public function addMultiple(
        EntityManagerInterface $entityManager, 
        Request $request, 
        ZoneRepository $zoneRepository
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }

        $user = $this->getUser();
        $eglise = $user->getEglise();

        // Récupérer les départements disponibles
        $zones = $zoneRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        
        // Récupérer les familles existants pour vérifier les doublons
        $existingFamilles = $entityManager->getRepository(Famille::class)->findBy([
            'eglise' => $eglise,
            'deletedAt' => null
        ]);
        
        // Créer un tableau des noms existants
        $existingNames = [];
        foreach ($existingFamilles as $existing) {
            $key = strtolower(trim($existing->getNom()));
            $existingNames[$key] = $existing->getNom();
        }
        
        // Créer le formulaire multiple
        $famillesData = ['familles' => []];
        $form = $this->createForm(FamilleMultipleType::class, $famillesData, [
            'zone' => $zones
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $familles = $data['familles'];
            $savedCount = 0;
            $errors = [];
            $submittedNames = [];
            
            // Validation de tous les familles
            foreach ($familles as $index => $famille) {
                $nom = trim($famille->getNom());
                $nomLower = strtolower($nom);
                $lineNumber = $index + 1;
                
                // Vérification champ vide
                if (empty($nom)) {
                    $errors[] = "Ligne {$lineNumber}: Le nom du famille ne peut pas être vide.";
                    continue;
                }
                
                // Vérification doublon avec base de données
                if (isset($existingNames[$nomLower])) {
                    $errors[] = "Ligne {$lineNumber}: Le famille '{$nom}' existe déjà.";
                    continue;
                }
                
                // Vérification doublon dans la soumission
                if (in_array($nomLower, $submittedNames)) {
                    $errors[] = "Ligne {$lineNumber}: Le famille '{$nom}' est en double dans la liste.";
                    continue;
                }
                
                $submittedNames[] = $nomLower;
            }
            
            // Affichage des erreurs
            if (!empty($errors)) {
                foreach ($errors as $error) {
                    $this->addFlash('danger', $error);
                }
                return $this->redirectToRoute('famille_multiple_add');
            }
            
            // Enregistrement des familles
            foreach ($familles as $famille) {
                $nom = trim($famille->getNom());
                
                if (!empty($nom)) {
                    $famille
                        ->setNom($nom)
                        ->setDescription($famille->getDescription())
                        ->setResponsable1($famille->getResponsable1())
                        ->setResponsable2($famille->getResponsable2())
                        ->setZone($famille->getZone())
                        ->setCreatedFromIp($this->getIp())
                        ->setEglise($eglise)
                       // ->setIdeglise($eglise->getId())
                        ->setCreatedBy($user)
                        ->setCreateAt(new \DateTime());
                    
                    $entityManager->persist($famille);
                    $savedCount++;
                }
            }
            
            if ($savedCount > 0) {
                $entityManager->flush();
                $this->addFlash('success', $savedCount . ' famille(s) ont été enregistré(s) avec succès.');
            }
            
            return $this->redirectToRoute('famille');
        }
        
        return $this->render('famille/multiple_add.html.twig', [
            'form' => $form->createView(),
            'zones' => $zones,
        ]);
    }
    
    private function getIp(): string
    {
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }

}
