<?php

namespace App\Controller;

use App\Entity\Culte;
use App\Entity\Presenceculte;
use App\Form\CulteType;
use App\Repository\CulteRepository;
use App\Repository\FideleRepository;
use App\Repository\PresenceculteRepository;
use App\Repository\TypeculteRepository;
use App\Service\FileUploader;
use App\Traits\ClientIp;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Uid\Uuid;
use App\Service\QrCodeGenerator;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/culte')]
class CulteController extends AbstractController {

    use ClientIp;

    #[Route('/', name: 'culte')]
    public function index(CulteRepository $culteRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $culte = $culteRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL], ['id' => 'DESC'] );
        return $this->render('culte/index.html.twig', [
                    'cultes' => $culte,
        ]);
    }

    #[Route('/{id}/detail', name: 'culte_detail', methods: ['GET', 'POST'])]
    public function detail(Culte $culte): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        return $this->render('culte/detail.html.twig', [
                    'culte' => $culte,
        ]);
    }

    
//Doublon

#[Route('/get-presencecultes-by-seance', name: 'culte_get_presences', methods: ['POST'])]
public function getPresencesBySeance(Request $request, PresenceculteRepository $presenceculteRepository, CulteRepository $culteRepository): JsonResponse
{
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    
    if (!$this->isGranted('ROLE_SECRETAIRE')) {
        return $this->json(['error' => 'Accès refusé'], 403);
    }
    
    $seanceId = $request->request->get('seanceId');
    
    if (!$seanceId) {
        return $this->json(['error' => 'Séance non spécifiée'], 400);
    }
    
    $seance = $culteRepository->find($seanceId);
    
    if (!$seance) {
        return $this->json(['error' => 'Séance non trouvée'], 404);
    }
    
    // Récupérer toutes les présences pour cette séance
    $presences = $presenceculteRepository->findBy(['culte' => $seance]);
    
    // Extraire les IDs des fidèles présents
    $presencesIds = [];
    foreach ($presences as $presence) {
        $presencesIds[] = $presence->getFidele()->getId();
    }
    
    // Compter le nombre de présences par fidèle (au cas où)
    $presencesCount = [];
    foreach ($presences as $presence) {
        $fideleId = $presence->getFidele()->getId();
        if (!isset($presencesCount[$fideleId])) {
            $presencesCount[$fideleId] = 0;
        }
        $presencesCount[$fideleId]++;
    }
    
    return $this->json([
        'success' => true,
        'presences' => $presencesIds,
        'presencesCount' => $presencesCount,
        'seanceId' => $seanceId,
        'seanceDate' => $seance->getDatesuper()->format('d-m-Y'),
        'seanceTheme' => $seance->getTheme()
    ]);
}

//Fin doublon presence


       #[Route('/presence', name: 'culte_presence', methods: ['POST', 'GET'])]
public function presenceCulte(FideleRepository $fideleRepository, Request $request, CulteRepository $culteRepository, PresenceculteRepository $presenceRepo): Response {
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    if (!$this->isGranted('ROLE_SECRETAIRE')) {
        throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
    }
    
    $eglise = $this->getUser()->getEglise();
    $user = $this->getUser();
    
    if ($request->isMethod('POST')) {
        $culte = $request->request->get('culte');
        $tabpost = $request->request->get('tab');
        
        if (empty($tabpost)) {
            $this->addFlash('warning', 'Veuillez sélectionner au moins un fidèle.');
            return $this->redirectToRoute('culte_presence');
        }
        
        $em = $this->getDoctrine()->getManager();
        $idculte = $culteRepository->find($culte);
        
        foreach ($tabpost as $value) {
            $idfidele = $fideleRepository->find($value);
            
            // Vérifier si la présence existe déjà
            $existingPresence = $presenceRepo->findOneBy([
                'fidele' => $idfidele, 
                'culte' => $idculte
            ]);
            
            if (!$existingPresence) {
                $presenceculte = new Presenceculte();
                $presenceculte->setFidele($idfidele);
                $presenceculte->setCulte($idculte);
                $presenceculte->setEglise($eglise);
                $presenceculte->setCreatedBy($this->getUser());
                $em->persist($presenceculte);
            }
        }
        
        $em->flush();
        $this->addFlash('message', 'Enregistrement effectué avec succès');
        
        return $this->redirectToRoute('culte_listepresence');
    } else {
        $fidele = $fideleRepository->findBy([
            'eglise' => $eglise, 
            "deletedAt" => NULL, 
            "etatfidele" => 1
        ]);
        
        // Trier les cultes par date décroissante
        $cultes = $culteRepository->findBy(
            ['eglise' => $eglise], 
            ['dateculte' => 'DESC']
        );
        
        // Récupérer toutes les présences existantes pour chaque culte
        $presencesParCulte = [];
        foreach ($cultes as $culte) {
            $presences = $presenceRepo->findBy(['culte' => $culte]);
            $presencesParCulte[$culte->getId()] = array_map(function($presence) {
                return $presence->getFidele()->getId();
            }, $presences);
        }
        
        return $this->render('culte/presence.html.twig', [
            'fideles' => $fidele,
            'cultes' => $cultes,
            'presences_par_culte' => $presencesParCulte
        ]);
    }
}

        #[Route('/listepresence', name: 'culte_listepresence', methods: ['GET'])]
    public function listePresence(PresenceculteRepository $presenceRepository, Request $request, CulteRepository $culteRepo): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
         $user = $this->getUser();
        $eglise = $this->getUser()->getEglise();
       
        $presenceculte = $presenceRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $difference = $culteRepo->getCultesByDates();
        return $this->render('culte/listepresence.html.twig', [
                    'presencecultes' => $presenceculte,
                    'differences' => $difference,
        ]);
    }

#[Route('/{id}/update', name: 'culte_update', methods: ['GET', 'POST'])]
#[Route('/add', name: 'culte_add', methods: ['GET', 'POST'])]
public function add(EntityManagerInterface $entityManager, Request $request, QrCodeGenerator $qrGenerator, FileUploader $fileUploader, TypeculteRepository $typeculteRepository, FideleRepository $fideleRepository, CulteRepository $culteRepository, ?Culte $culte = null): Response 
{
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    if (!$this->isGranted('ROLE_SECRETAIRE')) {
        throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
    }
    
    $user = $this->getUser();
    $type = $culte === null ? 'add' : 'update';
    $culte = $culte === null ? new Culte() : $culte;
    $eglise = $this->getUser()->getEglise();
    $fidele = $fideleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
    $typeculte = $typeculteRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);

    $form = $this->createForm(CulteType::class, $culte, ['fidele' => $fidele, 'typeculte' => $typeculte,]);
    $form->handleRequest($request);
    
    if ($form->isSubmitted() && $form->isValid()) {
        $brochureFile = $form->get('photo')->getData();
        if ($brochureFile) {
            $brochureFileName = $fileUploader->upload($brochureFile);
            $culte->setPhoto($brochureFileName);
        }

        // Vérification de la date
        $naiss = $form['dateculte']->getData();
        $aujourdhui = new DateTime("now");

        if ($aujourdhui < $naiss) {
            $this->addFlash('warning', 'Date éronnée.');
            return $this->redirectToRoute('culte_add');
        }

        if ($type === 'add') {
            // Désactiver tous les autres cultes
            $allCultes = $culteRepository->findBy(['eglise' => $eglise]);
            foreach ($allCultes as $existingCulte) {
                $existingCulte->setEtat(false);
            }
            
            // Configurer le nouveau culte
            $culte->setEtat(true);
            $culte->setCreatedFromIp($this->GetIp())
                  ->setEglise($user->getEglise())
                  ->setCreatedBy($user);
            
            // Persister sans flush
                    $culte->setTokenPresence(
                Uuid::v4()->toRfc4122()
            );

                $culte->setDateExpirationQr(
                    (new \DateTime())->modify('+1 day')
                );
            $entityManager->persist($culte);
            $entityManager->flush();
                    $url = $this->generateUrl(
                    'presence_scan',
                    [
                        'token' => $culte->getTokenPresence()
                    ],
                    UrlGeneratorInterface::ABSOLUTE_URL
                );

         $qrGenerator->generate(
            $url,
            $this->getParameter('kernel.project_dir')
            . '/public/qrcode/culte_'.$culte->getId().'.png'
        );
            
        //   $this->generateQrCodeDirect($culte, $entityManager);
            }
        else {
            $culte->setUpdatedFromIp($this->GetIp())
                  ->setUpdatedBy($user);
            $entityManager->persist($culte);
            $entityManager->flush();
            $this->addFlash('success', 'Modification effectuée avec succès.');
        }
        
        $nextAction = $form->get('saveAndAdd')->isClicked() ? 'culte_add' : 'culte';
        return $this->redirectToRoute($nextAction);
    }
    
    $response = new Response(null, $form->isSubmitted() ? 422 : 200);
    return $this->render('culte/add.html.twig', [
        'culte' => $culte,
        'form' => $form->createView(),
        'response' => $response,
    ], $response);
}



    #[Route('/qrcode/print/{id}', name: 'culte_qrcode_print', methods: ['GET'])]
public function printQrCode(Culte $culte): Response
{
    if (!$culte->getTokenPresence() || $culte->getEtat() != 1) {
        $this->addFlash('warning', 'QR code non disponible pour ce culte');
        return $this->redirectToRoute('culte');
    }
    
    return $this->render('culte/qrcode_print.html.twig', [
        'culte' => $culte,
    ]);
}

    #[Route('/print', name: 'culte_print', methods: ['GET', 'POST'])]
    public function printculte(CulteRepository $culteRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        $culte = $culteRepository->findAll();

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('culte/print.html.twig', [
            'culte' => $culte,
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

    #[Route('culte/{id}', name: 'culte_delete', methods: ['POST'])]
    public function delete(Request $request, Culte $culte, CulteRepository $culteRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($this->isCsrfTokenValid('delete' . $culte->getId(), $request->request->get('_token'))) {
            if (!$this->isGranted('ROLE_SECRETAIRE')) {
                throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
            }
            $entityManager = $this->getDoctrine()->getManager();

            $culte->setDeletedFromIp($this->GetIp());
            $culte->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $culte->setDeletedBy($user);
            $this->addFlash('message', 'Suppression effectuée  avec succès.');

            $entityManager->flush();
        }

        return $this->redirectToRoute('culte');
    }

    #[Route('{id}/presenceculte', name: 'presenceculte_delete', methods: ['POST'])]
    public function deletePresence(Request $request, Presenceculte $presenceculte): Response {
        if ($this->isCsrfTokenValid('delete' . $presenceculte->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();

            $presenceculte->setDeletedFromIp($this->GetIp());
            $presenceculte->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $presenceculte->setDeletedBy($user);
            $entityManager->flush();
        }

        if ($request) {
            $this->addFlash('danger', 'Suppression avec succès.');
        }

        return $this->redirectToRoute('culte_listepresence', [], Response::HTTP_SEE_OTHER);
    }


            /**
             * Liste des présents pour une séance
             */
    #[Route('/presents', name: 'culte_presents', methods: ['POST'])]
    public function getPresents(Request $request, FideleRepository $fideleRepository, CulteRepository $culteRepository): Response
    {
        $culteId = $request->request->get('culte_id');
        
        if (!$culteId) {
            return $this->json(['error' => 'ID du culte manquant'], 400);
        }
        
        // Récupérer le culte
        $culte = $culteRepository->find($culteId);
        
        if (!$culte) {
            return $this->json(['error' => 'Culte non trouvé'], 404);
        }
        
        // Récupérer les présents (les Presenceculte pour ce culte)
        $presents = [];
        if ($culte) {
            foreach ($culte->getPresencecultes() as $presence) {
                $fidele = $presence->getFidele();
                if ($fidele) {
                    $presents[] = [
                        'id' => $fidele->getId(),
                        'nom' => $fidele->getNomfidele(),
                        'contact' => $fidele->getContact1(),
                        'date' => $presence->getCreateAt() ? $presence->getCreateAt()->format('d/m/Y H:i') : null
                    ];
                }
            }
        }
        
        return $this->render('culte/_presents_modal.html.twig', [
            'presents' => $presents,
            'seance' => $culte,
            'total' => count($presents)
        ]);
    }

    /**
     * Liste des absents pour une séance
     */
    #[Route('/absents', name: 'culte_absents', methods: ['POST'])]
    public function getAbsents(Request $request, FideleRepository $fideleRepository, CulteRepository $culteRepository): Response
    {
        $culteId = $request->request->get('culte_id');
        
        if (!$culteId) {
            return $this->json(['error' => 'ID du culte manquant'], 400);
        }
        
        // Récupérer le culte
        $culte = $culteRepository->find($culteId);
        
        if (!$culte) {
            return $this->json(['error' => 'Culte non trouvé'], 404);
        }
        
        // Récupérer les IDs des présents
        $presentIds = [];
        if ($culte) {
            foreach ($culte->getPresencecultes() as $presence) {
                $fidele = $presence->getFidele();
                if ($fidele) {
                    $presentIds[] = $fidele->getId();
                }
            }
        }
        
        // Récupérer tous les membres de la culte
        $membresCellule = $fideleRepository->findBy([
            'eglise' => $culte->getEglise() ? $culte->getEglise()->getId() : null,
            'deletedAt' => null
        ]);
        
        // Filtrer les absents (membres qui ne sont pas dans la liste des présents)
        $absents = [];
        foreach ($membresCellule as $membre) {
            if (!in_array($membre->getId(), $presentIds)) {
                $absents[] = [
                    'id' => $membre->getId(),
                    'nom' => $membre->getNomfidele(),
                    'contact' => $membre->getContact1()
                ];
            }
        }
        
        return $this->render('culte/_absents_modal.html.twig', [
            'absents' => $absents,
            'seance' => $culte,
            'total' => count($absents),
            'totalMembres' => count($membresCellule)
        ]);
    }
    
    
    
        /**
     * @Route("/search/invites/{id}", name="culte_search_invites", requirements={"id"="\d+"}, methods={"POST"})
     *
     * @return Response
     */
    public function culteSearchInvites(SerializerInterface $serializer, Culte $culte): Response {
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($culte) {
            $invites = (array) json_decode($serializer->serialize($culte->getInvites()->toArray(), 'json', ['groups' => ['public']]));
        } else {
            $invites = [];
        }

        return new Response($this->renderView('culte/invite.html.twig', [
                    'invites' => $invites
        ]));
    }
}
