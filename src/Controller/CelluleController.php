<?php

namespace App\Controller;

use App\Entity\Cellule;
use App\Form\CelluleType;
use App\Form\UsercelluleType;
use App\Repository\CelluleRepository;
use App\Repository\CotisationcelluleRepository;
use App\Repository\FideleRepository;
use App\Repository\PresencecelluleRepository;
use App\Repository\QuartierRepository;
use App\Repository\SeancecelluleRepository;
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

#[Route('/cellule')]
class CelluleController extends AbstractController {

    use ClientIp;

    #[Route('/', name: 'cellule')]
    public function index(CelluleRepository $celluleRepository): Response {
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $cellule = $celluleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        return $this->render('cellule/index.html.twig', [
                    'cellule' => $cellule,
        ]);
    }

    #[Route('/{id}/detail', name: 'cellule_detail', methods: ['GET', 'POST'])]
    public function detail(Cellule $cellule): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        return $this->render('cellule/detail.html.twig', [
                    'cellule' => $cellule,
        ]);
    }

    #[Route('/membre/{id}', name: 'cellule_membrecellule', methods: ['GET', 'POST'])]
    public function membrecellule(Request $request, FideleRepository $fideleRepository, CelluleRepository $celluleRepository) {
        //Recuperation id cellule
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $idcellule = $request->query->get('id');
        //Recuperation de la liste des fidele par quartier
        $listeFidele = $fideleRepository->findBy(['cellule' => $idcellule, 'deletedAt' => NULL, 'etatfidele' => 1]);
        $lignecellule = $celluleRepository->find($idcellule);
        $nomcellule = $lignecellule->getNom();
        return $this->render('cellule/membrecellule.html.twig', [
                    'fidele' => $listeFidele,
                    'id' => $idcellule,
                    'nomcellule' => $nomcellule,
        ]);
    }

    #[Route('/activite/{id}', name: 'cellule_activite', methods: ['GET'])]
    public function activiteCellule(Request $request, SeancecelluleRepository $activiteRepository, CelluleRepository $celluleRepo) {
        //Recuperation id cellule
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $idcellule = $request->query->get('id');
        //Recuperation de la liste des fidele par cellule
        $listeActivite = $activiteRepository->findBy(['cellule' => $idcellule, "deletedAt" => NULL]);
        $ligneCellule = $celluleRepo->find($idcellule);
        $nomcellule = $ligneCellule->getNom();
        return $this->render('cellule/activite.html.twig', [
                    'activitecellules' => $listeActivite,
                    'nomcellule' => $nomcellule,
                    'eglise' => $eglise,
        ]);
    }

    #[Route('/cotisation/{id}', name: 'cellule_cotisation', methods: ['GET'])]
    public function cotisationCellule(Request $request, CotisationcelluleRepository $cotisationRepository, CelluleRepository $celluleRepo) {
        //Recuperation id cellule
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $idcellule = $request->query->get('id');
        //Recuperation de la liste des fidele par cellule
        $listeCotisation = $cotisationRepository->findBy(['cellule' => $idcellule, "deletedAt" => NULL]);
        $ligneCellule = $celluleRepo->find($idcellule);
        $nomcellule = $ligneCellule->getNom();
        return $this->render('cellule/cotisation.html.twig', [
                    'cotisationcellules' => $listeCotisation,
                    'nomcellule' => $nomcellule,
                    'eglise' => $eglise,
        ]);
    }

    #[Route('/presence/{id}', name: 'cellule_presence', methods: ['GET'])]
    public function presenceCellule(Request $request, PresencecelluleRepository $presencecelluleRepository, CelluleRepository $celluleRepo) {
        //Recuperation id cellule
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $idcellule = $request->query->get('id');
        //Recuperation de la liste des fidele par cellule
        $listePresence = $presencecelluleRepository->findBy(['cellule' => $idcellule, "deletedAt" => NULL]);
        $ligneCellule = $celluleRepo->find($idcellule);
        $nomcellule = $ligneCellule->getNom();
        return $this->render('cellule/presence.html.twig', [
                    'presencecellules' => $listePresence,
                    'nomcellule' => $nomcellule,
                    'eglise' => $eglise,
        ]);
    }

    // #[Route('/{id}/update', name: 'cellule_update', methods: ['GET', 'POST'])]
    #[Route('/add', name: 'cellule_add', methods: ['GET', 'POST'])]
    public function add(EntityManagerInterface $entityManager, Request $request, ZoneRepository $zoneRepository, QuartierRepository $quartierRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }

        $user = $this->getUser();

        $cellule = new Cellule();
        $eglise = $this->getUser()->getEglise();
        $zone = $zoneRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $quartier = $quartierRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $form = $this->createForm(CelluleType::class, $cellule, ['quartier' => $quartier, 'zone' => $zone,]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {


            $cellule->setCreatedFromIp($this->GetIp()) // remplacement de la function par le trait
                    ->setEglise($user->getEglise())
                    ->setCreatedBy($user);

            $entityManager->persist($cellule);
            $entityManager->flush();
            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'cellule_add' : 'cellule';
            if ($nextAction) {
                $this->addFlash('success', 'Enregistrement avec succès.');
            }
            return $this->redirectToRoute($nextAction);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);

        return $this->render('cellule/add.html.twig', [
                    'cellule' => $cellule,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    #[Route('/print', name: 'cellule_print', methods: ['GET', 'POST'])]
    public function printcellule(CelluleRepository $celluleRepository): Response {

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

        $cellule = $celluleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('cellule/print.html.twig', [
            'cellule' => $cellule,
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

//    #[Route('/enfant/{id}', name: 'cellule_enfant', methods: ['GET'])]
//    public function listeEnfant(Request $request, EnfantRepository $enfantRepository, CelluleRepository $celluleRepo) {
//        //Recuperation id cellule
//        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
//        if (!$this->isGranted('ROLE_SECRETAIRE')) {
//            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
//        }
//        $user = $this->getUser();
//        $idcellule = $request->query->get('id');
//        //Recuperation de la liste des fidele par cellule
//        $listeEnfant = $enfantRepository->findBy(['cellule' => $idcellule, 'deletedAt' => NULL]);
//        $ligneCellule = $celluleRepo->find($idcellule);
//        $nomCellule = $ligneCellule->getNom();
//        return $this->render('cellule/enfant.html.twig', [
//                    'enfants' => $listeEnfant,
//                    'id' => $idcellule,
//                    'nomcellule' => $nomCellule,
//        ]);
//    }

    /**
     * @Route("/search/fideles/{id}", name="cellule_search_fideles", requirements={"id"="\d+"}, methods={"POST"})
     *
     * @return Response
     */
    public function celluleSearchFideles(SerializerInterface $serializer, Cellule $cellule): Response {
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($cellule) {
            $fideles = (array) json_decode($serializer->serialize($cellule->getFideles()->toArray(), 'json', ['groups' => ['public']]));
        } else {
            $fideles = [];
        }

        return new Response($this->renderView('cellule/listefidele.html.twig', [
                    'fideles' => $fideles
        ]));
    }

    /**
     * @Route("/search/enfants/{id}", name="cellule_search_enfants", requirements={"id"="\d+"}, methods={"POST"})
     *
     * @return Response
     */
    public function celluleSearchEnfants(SerializerInterface $serializer, Cellule $cellule): Response {
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($cellule) {
            $enfants = (array) json_decode($serializer->serialize($cellule->getEnfant()->toArray(), 'json', ['groups' => ['public']]));
        } else {
            $enfants = [];
        }

        return new Response($this->renderView('cellule/enfant.html.twig', [
                    'enfants' => $enfants
        ]));
    }

    #[Route('/update/datatable/{id}', name: 'cellule_update_datatable', methods: ['POST'])]
    public function celluleUpdateDatatable(EntityManagerInterface $entityManager, Request $request, ?Cellule $cellule = null): JsonResponse {

        $return = [
            'update' => false,
            'notification' => false,
        ];
        $new_cellule = \strip_tags($request->request->get('cellule'));

        // Si l'entité existe et que le nouveau nom de la cellule apre le strip_tags comporte plus que 0 caractères
        if ($cellule && strlen($new_cellule) > 0) {
            // strip_tags pour enlever tout code html
            // évite d'envoyer des balise <script>
            // ref: https://www.php.net/manual/fr/function.strip-tags.php
            $cellule->setNom($new_cellule);
            $user = $this->getUser();
            $cellule->setUpdatedBy($user)
                    ->setUpdatedFromIp($this->GetIp());
            $entityManager->persist($cellule);
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

    #[Route('/add1', name: 'cellule_add1', methods: ['GET', 'POST'])]
    public function newCellule(Request $request, EntityManagerInterface $entityManager, ZoneRepository $zoneRepository, QuartierRepository $quartierRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $cellule = new Cellule();
        $eglise = $this->getUser()->getEglise();
        $zone = $zoneRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $quartier = $quartierRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $form = $this->createForm(CelluleType::class, $cellule, ['quartier' => $quartier, 'zone' => $zone,]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cellule->setCreatedFromIp($this->GetIp());
            $user = $this->getUser();
            $cellule->setEglise($eglise);
            $entityManager->persist($cellule);
            $entityManager->flush();
            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'cellule_add1' : 'cellule';
            if ($nextAction) {
                $this->addFlash('success', 'Action effectuée avec succès.');
            }

            return $this->redirectToRoute($nextAction);
        }

        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('cellule/add1.html.twig', [
                    'cellule' => $cellule,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    #[Route('/{id}/update', name: 'cellule_update', methods: ['GET', 'POST'])]
    public function updateCellule(Request $request, Cellule $cellule, ZoneRepository $zoneRepository, QuartierRepository $quartierRepository): Response {
        $data = json_decode($request->getContent(), true);
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $user = $this->getUser();
        $eglise = $this->getUser()->getEglise();
        $zone = $zoneRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $quartier = $quartierRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $form = $this->createForm(CelluleType::class, $cellule, ['quartier' => $quartier, 'zone' => $zone,]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //Adresse ip de l'utilisateur

            $cellule->setUpdatedFromIp($this->GetIp());
            $cellule->setUpdatedBy($user);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Modification effectué avec succès.');

            return $this->redirectToRoute('cellule');
        }
        return $this->render('cellule/update.html.twig', [
                    'cellule' => $cellule,
                    'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/usercellule', name: 'cellule_usercellule', methods: ['GET', 'POST'])]
    public function usercellule(Request $request, Cellule $cellule, UserRepository $userRepository): Response {
        $data = json_decode($request->getContent(), true);
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise()->getId();
        $user1 = $this->getUser();
        $user = $userRepository->findBy(['eglise' => $eglise, 'etat' => 1]);
        $form = $this->createForm(UsercelluleType::class, $cellule, ['user' => $user,]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

              //Adresse ip de l'utilisateur
                /** @var User[] $selectedUsers */
            $selectedUsers = $form->get('users')->getData();

            foreach ($selectedUsers as $user) {
                
                $user->setCellule($cellule);
            }


            $cellule->setUpdatedFromIp($this->GetIp());
            $cellule->setUpdatedBy($user1);
              $this->addFlash('success', 'Administrateur de cellule créé avec succès.');
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('cellule');
        }

        return $this->render('cellule/usercellule.html.twig', [
                    'cellule' => $cellule,
                    'form' => $form->createView(),
        ]);
    }

    #[Route('/printmembre/', name: 'cellule_printmembre', methods: ['GET', 'POST'])]
    public function printMembre(CelluleRepository $celluleRepository, FideleRepository $fideleRepository, Request $request): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $id = $request->query->get('id');

        // Configure Dompdf according to your needs
        $pdfOptions = new Options(array('enable_remote' => true));
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->set('isHtml5ParserEnabled', true);

        $dompdf = new Dompdf($pdfOptions);

        //Recuperation de la liste des fidele par cellule

        $listeMembre = $fideleRepository->findBy(['cellule' => $id]);
        $lignecellule = $celluleRepository->find($id);
        $nomcellule = $lignecellule->getNom();
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('cellule/printmembre.html.twig', [
            'fideles' => $listeMembre,
            'nomcellule' => $nomcellule,
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
                ],
        );
    }

    #[Route('/{id}', name: 'cellule_delete', methods: ['POST'])]
    public function delete(Request $request, Cellule $cellule, CelluleRepository $celluleRepository): Response {
        if ($this->isCsrfTokenValid('delete' . $cellule->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();

            if (!$this->isGranted('ROLE_SECRETAIRE')) {
                throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
            }


            $cellule->setDeletedFromIp($this->GetIp());
            $cellule->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $cellule->setDeletedBy($user);
            $this->addFlash('danger', 'Suppression avec succès.');

            $entityManager->flush();
        }

        return $this->redirectToRoute('cellule');
    }

}
