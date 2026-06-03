<?php

namespace App\Controller;

use App\Entity\Enfant;
use App\Form\Enfant2Type;
use App\Form\EnfantType;
use App\Repository\CelluleRepository;
use App\Repository\CommuneRepository;
use App\Repository\EnfantRepository;
use App\Repository\EthnieRepository;
use App\Repository\FamilleRepository;
use App\Repository\FideleRepository;
use App\Repository\QuartierRepository;
use App\Repository\ZoneRepository;
use App\Service\FileUploader;
use App\Traits\ClientIp;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use function Sodium\bin2hex;

#[Route('/enfant')]
class EnfantController extends AbstractController {

    use ClientIp;

    #[Route('/', name: 'enfant')]
    public function index(EnfantRepository $enfantRepository, Request $request): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_ECODIM')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $enfant = $enfantRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL, "etatenfant" => 1, "editable" => 1]);
        return $this->render('enfant/index.html.twig', [
                    'enfant' => $enfant,
        ]);
    }

    #[Route('/add', name: 'enfant_add', methods: ['GET', 'POST'])]
    public function add(Request $request, SluggerInterface $slugger, FideleRepository $fideleRepository, FileUploader $fileUploader,  EnfantRepository $enfantRepository, QuartierRepository $quartierRepository,
            ZoneRepository $zoneRepository, FamilleRepository $familleRepository,
            CelluleRepository $celluleRepository, EthnieRepository $ethineRepository, CommuneRepository $communeRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_ECODIM')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $enfant = new Enfant();
        $entityManager = $this->getDoctrine()->getManager();
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $quartier = $quartierRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $peremembre = $fideleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL, "sexe" => 'Homme']);
        $merembre = $fideleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL, "sexe" => 'Femme']);
        $cellule = $celluleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $zone = $zoneRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $ethnie = $ethineRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $famille = $familleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $commune = $communeRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $form = $this->createForm(EnfantType::class, $enfant, ['quartier' => $quartier, 'peremembre' => $peremembre, 'merembre' => $merembre, 'cellule' => $cellule, 'zone' => $zone, 'ethnie' => $ethnie, 'famille' => $famille,'commune' => $commune]
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {


            $enfant->setCreatedFromIp($this->GetIp());

            //Calcul d'age en fonction de la date de naissance et la date d'ohjodui
            $naiss = $form['datenaiss']->getData();
            $formatyear = $naiss->format('Y');
            $aujourdhui = new DateTime("now");
            $formatojodui = $aujourdhui->format('Y');
            $age1 = ($formatojodui - $formatyear);
            if ($age1 <= 0) {
                $this->addFlash('warning', 'Veuillez verifier la date de naissance.');
                return $this->redirect('add');
            }

            $enfant->setAge($age1);

            //            Insertion image de profile
        $brochureFile = $form->get('photo')->getData();
            if ($brochureFile) {
                $brochureFileName = $fileUploader->upload($brochureFile);
                $enfant->setPhotoFile($brochureFileName);
            }

            $enfant = $form->getData();
            //Géneration automatique du code enfant qui est la combinaisaon d'année de naissance+2 lettres du nom+id
            $lesenfants = $enfantRepository->findBy(array(), array('id' => 'desc'), 1, 0);
            $id = 0;
            foreach ($lesenfants as $value) {
                $id = $value->getId();
            }
            $val = $id + 1;
            $idEnfant = substr($val, 0, 4);

            $naissance = $form['datenaiss']->getData();
            $child = $form['nom']->getData();
            $conversion = $naissance->format('Y-m-d');
            $an = explode('-', $conversion);
            $nomenfant = substr($child, 0, 1);
            $code = $an[0] . $nomenfant . $idEnfant;
            $enfant->setCode($code);
            $enfant->setEtatenfant("1");
            $eglise = $this->getUser()->getEglise();
            $user = $this->getUser();
            $enfant->setCreatedBy($user);
            $enfant->setEglise($eglise);
            $entityManager->persist($enfant);
            $entityManager->flush();
//            $this->addFlash('success', 'Enregistrement effectué avec succès.');

            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'enfant_add' : 'enfant';
            if ($nextAction) {
                $this->addFlash('success', 'Enregistrement avec succès.');
            }

            return $this->redirectToRoute($nextAction);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('enfant/add.html.twig', [
                    'enfant' => $enfant,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    #[Route('/{id}/update', name: 'enfant_update', methods: ['GET', 'POST'])]
    public function update(Request $request, Enfant $enfant, FideleRepository $fideleRepository, FileUploader $fileUploader,  QuartierRepository $quartierRepository,
            ZoneRepository $zoneRepository, FamilleRepository $familleRepository,
            CelluleRepository $celluleRepository, EthnieRepository $ethineRepository,  CommuneRepository $communeRepository) {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_ECODIM')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $data = json_decode($request->getContent(), true);
        $user = $this->getUser();

        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $peremembre = $fideleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL, "sexe" => 'Homme']);
        $merembre = $fideleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL, "sexe" => 'Femme']);
        $quartier = $quartierRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $cellule = $celluleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $zone = $zoneRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $ethnie = $ethineRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $famille = $familleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $commune = $communeRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $form = $this->createForm(EnfantType::class, $enfant, ['quartier' => $quartier, 'peremembre' => $peremembre, 'merembre' => $merembre, 'cellule' => $cellule, 'zone' => $zone, 'ethnie' => $ethnie, 'famille' => $famille, 'commune' => $commune]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //Adresse ip de l'utilisateur

            $enfant->setUpdatedFromIp($this->GetIp());
            //Calcul d'age en fonction de la date de naissance et la date d'ohjodui
            $naiss = $form['datenaiss']->getData();
            $formatyear = $naiss->format('Y');
            $aujourdhui = new DateTime("now");
            $formatojodui = $aujourdhui->format('Y');
            $age1 = ($formatojodui - $formatyear);
            if ($age1 <= 0) {
                $this->addFlash('warning', 'Veuillez verifier la date de naissance.');
                return $this->redirect('update');
            }



            $enfant->setAge($age1);
            //            Insertion image de profile
        $brochureFile = $form->get('photo')->getData();
            if ($brochureFile) {
                $brochureFileName = $fileUploader->upload($brochureFile);
                $enfant->setPhotoFile($brochureFileName);
            }
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Modification avec succès.');
            return $this->redirectToRoute('enfant');
        }

        return $this->render('enfant/update.html.twig', [
                    'enfant' => $enfant,
                    'form' => $form->createView(),
        ]);
    }

    #[Route('/print', name: 'enfant_print', methods: ['GET', 'POST'])]
    public function printenfant(EnfantRepository $enfantRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_ECODIM')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise()->getId();
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
        $enfant = $enfantRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL, "etatenfant" => 1]);

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('enfant/print.html.twig', [
            'enfant' => $enfant,
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

    #[Route('/{id}/printbyid', name: 'enfant_printbyid', methods: ['GET', 'POST'])]
    public function printById(int $id, EntityManagerInterface $enfantManager, EnfantRepository $enfantRepository, Request $request): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_ECODIM')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
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

//        $enfant = $enfantRepository->findAll();
        $enfant = $enfantRepository->findOneById($id);

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('enfant/printbyid.html.twig', [
            'enfant' => $enfant,
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

    #[Route('/archiveenfant', name: 'enfant_archiveenfant')]
    public function listeSupp(EnfantRepository $enfantRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $enfant = $enfantRepository->findBy(['eglise' => $eglise, "etatenfant" => 0, "editable" => 0]);

        return $this->render('enfant/listesupp.html.twig', [
                    'enfant' => $enfant,
        ]);
    }

    /**
     * @Route("/restaure/{id}", name="enfant_restaure")
     */
    public function restaure(Request $request, Enfant $enfant): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if ($this->isCsrfTokenValid('restaureenfant' . $enfant->getId(), $request->request->get('_token'))) {
            $user = $this->getUser();

            $entityManager = $this->getDoctrine()->getManager();
            if (!$this->isGranted('ROLE_SECRETAIRE')) {
                throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
            }
            $enfant->setEtatenfant("1");
            $enfant->setEditable("1");
            $enfant->setDeletedBy(NULL);
            $enfant->setDeletedAt(NULL);
            $this->addFlash('success', 'Restauration avec succès.');

            $entityManager->flush();
        }

        return $this->redirectToRoute('enfant');
    }

    #[Route('/{id}/detail', name: 'enfant_detail', methods: ['GET', 'POST'])]
    public function detail(Enfant $enfant): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_ECODIM')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        return $this->render('enfant/detail.html.twig', [
                    'enfant' => $enfant,
        ]);
    }

    #[Route('/{id}', name: 'enfant_delete', methods: ['POST'])]
    public function delete(Request $request, Enfant $enfant): Response {
        if ($this->isCsrfTokenValid('delete' . $enfant->getId(), $request->request->get('_token'))) {
            if (!$this->isGranted('ROLE_ADMIN')) {
                throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
            }
            $entityManager = $this->getDoctrine()->getManager();

            $enfant->setDeletedFromIp($this->GetIp());
            $enfant->setDeletedAt(new DateTime("now"));
            $enfant->setEtatenfant("0");
            $enfant->setEditable("0");
            $user = $this->getUser();
            $enfant->setDeletedBy($user);
            $this->addFlash('danger', 'Suppression avec succès.');

            $entityManager->flush();
        }

        return $this->redirectToRoute('enfant');
    }

    
    
    
        #[Route('/anniversaire', name: 'enfant_anniversaire')]
    public function enfantAnnivesaire(EnfantRepository $enfantRepository, Request $request): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_ECODIM')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $enfant = $enfantRepository->findBirthdayAVenir(['eglise' => $eglise, "deletedAt" => NULL, "etatenfant" => 1, "editable" => 1]);
        return $this->render('bilan/anniversaire.html.twig', [
                    'enfants' => $enfant,
        ]);
    }

    // #[Route('/quartiers/{communesId}', name: 'get_quartiers', methods: ['GET'])]
    //     public function getQuartiers(int $communesId, QuartierRepository $quartierRepo, CommuneRepository $communeRepository): JsonResponse

    //     {
    //         $commune = $communeRepository->find($communesId);
    //         $quartiers = $quartierRepo->findBy(['commune' => $commune]);

    //         $data = array_map(fn($quartier)=>[
    //             'id' => $quartier->getId(),
    //             'libelle' => $quartier->getLibelle()
    //         ], $quartiers);

    //         return $this->json($data);
    //     } 
}
 