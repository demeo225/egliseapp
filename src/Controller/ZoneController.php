<?php

namespace App\Controller;

use App\Entity\Zone;
use App\Form\UserzoneType;
use App\Form\ZoneType;
use App\Repository\CelluleRepository;
use App\Repository\CotisationzoneRepository;
use App\Repository\FamilleRepository;
use App\Repository\FideleRepository;
use App\Repository\PresencezoneRepository;
use App\Repository\SeancezoneRepository;
use App\Repository\UserRepository;
use App\Repository\ZoneRepository;
use App\Traits\ClientIp;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/zone')]
class ZoneController extends AbstractController {

    use ClientIp;

    #[Route('/', name: 'zone')]
    public function index(ZoneRepository $zoneRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();

        $user = $this->getUser();
        $zone = $zoneRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        return $this->render('zone/index.html.twig', [
                    'zones' => $zone,
        ]);
    }

//    #[Route('/details/{id}', name: 'zone_detail', methods: ['GET'])]
//    public function detailfamille(Request $request, CelluleRepository $celluleRepository, ZoneRepository $zoneRepo) {
//        //Recuperation id famille
//        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
//        if (!$this->isGranted('ROLE_SECRETAIRE')) {
//            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
//        }
//        $eglise = $this->getUser()->getEglise();
//
//        $user = $this->getUser();
//        $idzone = $request->query->get('id');
//
//        //Recuperation de la liste des fidele par zone
//        $listeCellule = $celluleRepository->findBy(['zone' => $idzone, 'deletedAt' => NULL]);
//        $ligneZone = $zoneRepo->find($idzone);
//        $nomZone = $ligneZone->getNom();
//        return $this->render('zone/detail.html.twig', [
//                    'cellule' => $listeCellule,
//                    'id' => $idzone,
//                    'nomzone' => $nomZone,
//                    'eglise' => $eglise,
//        ]);
//    }

    #[Route('/activite/{id}', name: 'zone_activite', methods: ['GET'])]
    public function activiteZone(Request $request, SeancezoneRepository $activiteRepository, ZoneRepository $zoneRepo) {
        //Recuperation id zone
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $idzone = $request->query->get('id');
        //Recuperation de la liste des fidele par zone
        $listeActivite = $activiteRepository->findBy(['zone' => $idzone, "deletedAt" => NULL]);
        $ligneZone = $zoneRepo->find($idzone);
        $nomzone = $ligneZone->getNom();
        return $this->render('zone/activite.html.twig', [
                    'activitezones' => $listeActivite,
                    'nomzone' => $nomzone,
                    'eglise' => $eglise,
        ]);
    }

//    #[Route('/famille/{id}', name: 'zone_famille', methods: ['GET'])]
//    public function familleZone(Request $request, FamilleRepository $familleRepository, ZoneRepository $zoneRepo) {
//        //Recuperation id zone
//        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
//        $eglise = $this->getUser()->getEglise();
//        $user = $this->getUser();
//        $idzone = $request->query->get('id');
//        //Recuperation de la liste des fidele par zone
//        $listeFamille = $familleRepository->findBy(['zone' => $idzone, "deletedAt" => NULL]);
//        $ligneZone = $zoneRepo->find($idzone);
//        $nomzone = $ligneZone->getNom();
//        return $this->render('zone/famille.html.twig', [
//                    'familles' => $listeFamille,
//                    'nomzone' => $nomzone,
//                    'eglise' => $eglise,
//        ]);
//    }

    #[Route('/cotisation/{id}', name: 'zone_cotisation', methods: ['GET'])]
    public function cotisationZone(Request $request, CotisationzoneRepository $cotisationRepository, ZoneRepository $zoneRepo) {
        //Recuperation id zone
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $idzone = $request->query->get('id');
        //Recuperation de la liste des fidele par zone
        $listeCotisation = $cotisationRepository->findBy(['zone' => $idzone, "deletedAt" => NULL]);
        $ligneZone = $zoneRepo->find($idzone);
        $nomzone = $ligneZone->getNom();
        return $this->render('zone/cotisation.html.twig', [
                    'cotisationzones' => $listeCotisation,
                    'nomzone' => $nomzone,
                    'eglise' => $eglise,
        ]);
    }

//    #[Route('/enfant/{id}', name: 'zone_enfant', methods: ['GET'])]
//    public function listeEnfant(Request $request, EnfantRepository $enfantRepository, ZoneRepository $zoneRepo) {
//        //Recuperation id zone
//        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
//        if (!$this->isGranted('ROLE_SECRETAIRE')) {
//            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
//        }
//        $user = $this->getUser();
//        $idzone = $request->query->get('id');
//        //Recuperation de la liste des fidele par zone
//        $listeEnfant = $enfantRepository->findBy(['zone' => $idzone, 'deletedAt' => NULL]);
//        $ligneZone = $zoneRepo->find($idzone);
//        $nomZone = $ligneZone->getNom();
//        return $this->render('zone/enfant.html.twig', [
//                    'enfants' => $listeEnfant,
//                    'id' => $idzone,
//                    'nomzone' => $nomZone,
//        ]);
//    }

    /**
     * @Route("/search/enfants/{id}", name="zone_search_enfants", requirements={"id"="\d+"}, methods={"POST"})
     *
     * @return Response
     */
    public function zoneSearchEnfants(SerializerInterface $serializer, Zone $zone): Response {
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($zone) {
            $enfants = (array) json_decode($serializer->serialize($zone->getEnfants()->toArray(), 'json', ['groups' => ['public']]));
        } else {
            $enfants = [];
        }

        return new Response($this->renderView('zone/enfant.html.twig', [
                    'enfants' => $enfants
        ]));
    }

    /**
     * @Route("/search/fideles/{id}", name="zone_search_fideles", requirements={"id"="\d+"}, methods={"POST"})
     *
     * @return Response
     */
    public function zoneSearchFideles(SerializerInterface $serializer, Zone $zone): Response {
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($zone) {
            $fideles = (array) json_decode($serializer->serialize($zone->getFideles()->toArray(), 'json', ['groups' => ['public']]));
        } else {
            $fideles = [];
        }

        return new Response($this->renderView('zone/listefidele.html.twig', [
                    'fideles' => $fideles
        ]));
    }

    /**
     * @Route("/search/cellules/{id}", name="zone_search_cellules", requirements={"id"="\d+"}, methods={"POST"})
     *
     * @return Response
     */
    public function zoneSearchcellule(SerializerInterface $serializer, Zone $zone): Response {
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($zone) {
            $cellules = (array) json_decode($serializer->serialize($zone->getCellules()->toArray(), 'json', ['groups' => ['public']]));
        } else {
            $cellules = [];
            dd($cellules);
        }

        return new Response($this->renderView('zone/detail.html.twig', [
                    'cellules' => $cellules
        ]));
    }

    /**
     * @Route("/search/familles/{id}", name="zone_search_familles", requirements={"id"="\d+"}, methods={"POST"})
     *
     * @return Response
     */
    public function zoneSearchfamille(SerializerInterface $serializer, Zone $zone): Response {
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($zone) {
            $familles = (array) json_decode($serializer->serialize($zone->getFamilles()->toArray(), 'json', ['groups' => ['public']]));
        } else {
            $familles = [];
            dd($familles);
        }

        return new Response($this->renderView('zone/famille.html.twig', [
                    'familles' => $familles
        ]));
    }

    #[Route('/fidele/{id}', name: 'zone_fidele', methods: ['GET'])]
    public function listeFidele(Request $request, FideleRepository $fideleRepository, ZoneRepository $zoneRepo) {
        //Recuperation id zone
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $user = $this->getUser();
        $idzone = $request->query->get('id');
        //Recuperation de la liste des fidele par zone
        $listeFidele = $fideleRepository->findBy(['zone' => $idzone, 'deletedAt' => NULL]);
        $ligneZone = $zoneRepo->find($idzone);
        $nomZone = $ligneZone->getNom();
        return $this->render('zone/fidele.html.twig', [
                    'fideles' => $listeFidele,
                    'id' => $idzone,
                    'nomzone' => $nomZone,
        ]);
    }

    #[Route('/presence/{id}', name: 'zone_presence', methods: ['GET'])]
    public function presenceZone(Request $request, PresencezoneRepository $presencezoneRepository, ZoneRepository $zoneRepo) {
        //Recuperation id zone
        if (!$this->isGranted('ROLE_RESPONSABLE_CELLULE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $idzone = $request->query->get('id');
        //Recuperation de la liste des fidele par zone
        $listePresence = $presencezoneRepository->findBy(['zone' => $idzone, "deletedAt" => NULL]);
        $ligneZone = $zoneRepo->find($idzone);
        $nomzone = $ligneZone->getNom();
        return $this->render('zone/presence.html.twig', [
                    'presencezones' => $listePresence,
                    'nomzone' => $nomzone,
                    'eglise' => $eglise,
        ]);
    }

    // #[Route('/{id}/update', name: 'zone_update', methods: ['GET', 'POST'])]
    #[Route('/add', name: 'zone_add', methods: ['GET', 'POST'])]
    public function add(EntityManagerInterface $entityManager, Request $request, ?Zone $zone = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $user = $this->getUser();
        $type = $zone === null ? 'add' : 'update';
        $zone = $zone === null ? new Zone() : $zone;
        $form = $this->createForm(ZoneType::class, $zone);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $eglise = $this->getUser()->getEglise();

            if ($type === 'add') {
                $zone->setCreatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setEglise($user->getEglise())
                        ->setCreatedBy($user)
                ;
            } else {
                $zone->setUpdatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setUpdatedBy($user)
                ;
            }
            $entityManager->persist($zone);
            $entityManager->flush();
            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'zone_add' : 'zone';
            if ($nextAction) {
                $this->addFlash('success', 'Action effectuée avec succès.');
            }
            return $this->redirectToRoute($nextAction);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('zone/add.html.twig', [
                    'zone' => $zone,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    #[Route('/update/datatable/{id}', name: 'zone_update_datatable', methods: ['POST'])]
    public function zoneUpdateDatatable(EntityManagerInterface $entityManager, Request $request, ?Zone $zone = null): JsonResponse {

        $return = [
            'update' => false,
            'notification' => false,
        ];
        $new_zone = \strip_tags($request->request->get('zone'));

        // Si l'entité existe et que le nouveau nom de la zone apre le strip_tags comporte plus que 0 caractères
        if ($zone && strlen($new_zone) > 0) {
            // strip_tags pour enlever tout code html
            // évite d'envoyer des balise <script>
            // ref: https://www.php.net/manual/fr/function.strip-tags.php
            $zone->setNom($new_zone);
            $user = $this->getUser();
            $zone->setUpdatedBy($user)
                    ->setUpdatedFromIp($this->GetIp());
            $entityManager->persist($zone);
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

    #[Route('/add1', name: 'zone_add1', methods: ['GET', 'POST'])]
    public function newCommune(Request $request, EntityManagerInterface $entityManager): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $zone = new Zone();
        $form = $this->createForm(ZoneType::class, $zone);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $zone->setCreatedFromIp($this->GetIp());
            $user = $this->getUser();
            $eglise = $this->getUser()->getEglise();
            $zone->setCreatedBy($user);
            $zone->setEglise($eglise);
            $entityManager->persist($zone);
            $entityManager->flush();
            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'zone_add1' : 'zone';
            if ($nextAction) {
                $this->addFlash('success', 'Action effectuée avec succès.');
            }

            return $this->redirectToRoute($nextAction);
        }

        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('zone/add1.html.twig', [
                    'zone' => '$zone',
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    #[Route('zone/printzone', name: 'zone_printzone', methods: ['GET', 'POST'])]
    public function printzone(ZoneRepository $zoneRepository): Response {

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

        $zone = $zoneRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('zone/printzone.html.twig', [
            'zone' => $zone,
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

    #[Route('/printmembre', name: 'zone_printmembre', methods: ['GET', 'POST'])]
    public function printmembre(ZoneRepository $zoneRepository, FideleRepository $fideleRepository, Request $request): Response {
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
        $zone = $zoneRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $lignezone = $zoneRepository->find($id);
        $nomzone = $lignezone->getNom();
        $listeFidele = $fideleRepository->findBy(['eglise' => $eglise, 'zone' => $zone]);

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('zone/printmembre.html.twig', [
            'fideles' => $listeFidele,
            'nomzone' => $nomzone,
            'eglise' => $eglise,
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();
        ob_get_clean();
        // Parameters
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
        // Output the generated PDF to Browser (force download)
        $dompdf->stream("mypdf.pdf", [
            "Attachment" => false
        ]);
    }

    #[Route('/{id}/update', name: 'zone_update', methods: ['GET', 'POST'])]
    public function updateZone(Request $request, Zone $zone): Response {
        $data = json_decode($request->getContent(), true);
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $user = $this->getUser();
        $form = $this->createForm(ZoneType::class, $zone);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //Adresse ip de l'utilisateur
            $user = $this->getUser();

            $zone->setUpdatedFromIp($this->GetIp());
            $zone->setUpdatedBy($user);
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Modification avec succès');

            return $this->redirectToRoute('zone');
        }
        return $this->render('zone/update.html.twig', [
                    'zone' => $zone,
                    'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/userzone', name: 'zone_userzone', methods: ['GET', 'POST'])]
    public function userzone(Request $request, Zone $zone, UserRepository $userRepository): Response {
        $data = json_decode($request->getContent(), true);
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user1 = $this->getUser();
        $user = $userRepository->findBy(['eglise' => $eglise, 'etat' => 1]);
        $form = $this->createForm(UserzoneType::class, $zone, ['user' => $user,]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //Adresse ip de l'utilisateur
                /** @var User[] $selectedUsers */
            $selectedUsers = $form->get('users')->getData();

            foreach ($selectedUsers as $user) {
                $user->setZone($zone);
            }


            $zone->setUpdatedFromIp($this->GetIp());
            $zone->setUpdatedBy($user1);
            $this->getDoctrine()->getManager()->flush();
        $this->addFlash('success', 'Enregistrement avec succès.');
            return $this->redirectToRoute('zone');
        }

        return $this->render('zone/userzone.html.twig', [
                    'zone' => $zone,
                    'form' => $form->createView(),
        ]);
    }

    #[Route('deletezone/{id}', name: 'zone_delete', methods: ['POST'])]
    #[IsGranted("ROLE_SECRETAIRE", message: 'Action non autorisée')]
    public function delete(Request $request, Zone $zone): Response {
        if ($this->isCsrfTokenValid('delete' . $zone->getId(), $request->request->get('_token'))) {
            if (!$this->isGranted('ROLE_SECRETAIRE')) {
                throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
            }
            $entityManager = $this->getDoctrine()->getManager();

            $zone->setDeletedFromIp($this->GetIp());
            $zone->setDeletedAt(new DateTime("now"));
            $this->addFlash('danger', 'Suppression avec succès.');
            $user = $this->getUser();
            $zone->setDeletedBy($user);

            $entityManager->flush();
        }

        return $this->redirectToRoute('zone');
    }

}
