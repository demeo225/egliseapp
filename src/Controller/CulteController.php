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

//    private function generateQrCodeDirect(Culte $culte, EntityManagerInterface $entityManager): void
// {
//     try {
//         if (!$culte->getId()) {
//             return;
//         }
        
//         $url = $this->generateUrl('culte_scan_qr', ['id' => $culte->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        
//         $qrCode = new QrCode($url);
//         $qrCode->setSize(300);
//         $qrCode->setMargin(10);
        
//         $writer = new PngWriter();
//         $result = $writer->write($qrCode);
        
//         $culte->setQrCode($result->getDataUri());
        
//         // Ne pas faire de flush ici, le flush sera fait après
//         // $entityManager->flush();
        
//     } catch (\Exception $e) {
//         error_log('Erreur génération QR: ' . $e->getMessage());
//         $culte->setQrCode(null);
//     }
// }

// #[Route('/scan/{id}', name: 'culte_scan_qr', methods: ['GET'])]
// public function scanQrCode(Culte $culte, Request $request): Response
// {
//     // Vérifier si le culte est actif (etat == 1)
//     if ($culte->getEtat() != 1) {
//         $this->addFlash('warning', 'Ce culte n\'est pas actif ou est terminé.');
//         return $this->render('culte/scan_error.html.twig', [
//             'message' => 'Ce culte n\'est pas disponible pour l\'enregistrement.'
//         ]);
//     }
    
//     // Vérifier si la date du culte n'est pas dépassée
//     $now = new \DateTime();
//     if ($culte->getDateculte() < $now) {
//         $this->addFlash('warning', 'Ce culte est déjà passé.');
//         return $this->render('culte/scan_error.html.twig', [
//             'message' => 'Ce culte est déjà terminé.'
//         ]);
//     }
    
//     // Stocker l'ID du culte en session
//     $session = $request->getSession();
//     $session->set('current_culte_id', $culte->getId());
    
//     return $this->render('culte/scan_form.html.twig', [
//         'culte' => $culte,
//         'csrf_token' => $this->generateCsrfToken('scan_culte')
//     ]);
// }

// // Dans CulteController.php


// #[Route('/scan/register', name: 'culte_scan_register', methods: ['POST'])]
// public function registerFromScan(Request $request, FideleRepository $fideleRepository, CulteRepository $culteRepository, PresenceculteRepository $presenceRepo, EntityManagerInterface $entityManager): Response
// {
//     // Vérifier le token CSRF
//     $submittedToken = $request->request->get('_token');
//     if (!$this->isCsrfTokenValid('scan_culte', $submittedToken)) {
//         return $this->json(['error' => 'Token invalide'], 400);
//     }
    
//     $session = $request->getSession();
//     $culteId = $session->get('current_culte_id');
    
//     if (!$culteId) {
//         return $this->json(['error' => 'Session expirée, veuillez scanner à nouveau'], 400);
//     }
    
//     $culte = $culteRepository->find($culteId);
//     if (!$culte || $culte->getEtat() != 1) {
//         return $this->json(['error' => 'Culte non disponible'], 400);
//     }
    
//     $contact1 = $request->request->get('contact1');
//     $nomfidele = $request->request->get('nomfidele');
    
//     // Validation
//     if (empty($contact1)) {
//         return $this->json(['error' => 'Le numéro de téléphone est requis'], 400);
//     }
    
//     if (empty($nomfidele)) {
//         return $this->json(['error' => 'Le nom est requis'], 400);
//     }
    
//     $eglise = $culte->getEglise();
    
//     // Chercher si le fidèle existe déjà
//     $fidele = $fideleRepository->findOneBy([
//         'contact1' => $contact1,
//         'eglise' => $eglise,
//         'deletedAt' => null
//     ]);
    
//     // Si le fidèle n'existe pas, le créer
//     if (!$fidele) {
//         $fidele = new Fidele();
//         $fidele->setNomfidele($nomfidele)
//                ->setContact1($contact1)
//                ->setEglise($eglise)
//                ->setEtatfidele(1)
//                ->setCreatedBy($this->getUser() ?? 'system')
//                ->setCreatedFromIp($request->getClientIp());
        
//         $entityManager->persist($fidele);
//         $entityManager->flush();
        
//         $this->addFlash('success', 'Nouveau fidèle enregistré avec succès !');
//     } else {
//         // Mettre à jour le nom si différent
//         if ($fidele->getNomfidele() !== $nomfidele) {
//             $fidele->setNomfidele($nomfidele);
//             $entityManager->flush();
//         }
//         $this->addFlash('success', 'Bienvenue ' . $fidele->getNomfidele() . ' !');
//     }
    
//     // Vérifier si la présence existe déjà
//     $existingPresence = $presenceRepo->findOneBy([
//         'fidele' => $fidele,
//         'culte' => $culte
//     ]);
    
//     if ($existingPresence) {
//         return $this->json(['error' => 'Vous avez déjà enregistré votre présence pour ce culte'], 400);
//     }
    
//     // Créer la présence
//     $presence = new Presenceculte();
//     $presence->setFidele($fidele)
//              ->setCulte($culte)
//              ->setEglise($eglise)
//              ->setCreatedBy($fidele->getNomfidele())
//              ->setCreatedFromIp($request->getClientIp());
    
//     $entityManager->persist($presence);
//     $entityManager->flush();
    
//     // Nettoyer la session
//     $session->remove('current_culte_id');
    
//     return $this->json([
//         'success' => true,
//         'message' => 'Présence enregistrée avec succès !',
//         'fidele' => $fidele->getNomfidele()
//     ], 200);
// }

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
