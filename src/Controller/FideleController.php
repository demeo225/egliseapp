<?php

namespace App\Controller;

use App\Entity\Eglise;
use App\Entity\Fidele;
use App\Form\DeletefideleType;
use App\Form\Fidele2Type;
use App\Form\FideleType;
use App\Form\PreinscriptionType;
use App\Repository\CelluleRepository;
use App\Repository\CommuneRepository;
use App\Repository\EgliseRepository;
use App\Repository\EnfantRepository;
use App\Repository\EthnieRepository;
use App\Repository\FamilleRepository;
use App\Repository\FideleRepository;
use App\Repository\FonctionRepository;
use App\Repository\NationaliteRepository;
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
use function Sodium\bin2hex;

#[Route('/fidele')]
class FideleController extends AbstractController {

    use ClientIp;

    #[Route('/', name: 'fidele')]
    public function index(FideleRepository $fideleRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $fidele = $fideleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL, "etatfidele" => 1]);
        return $this->render('fidele/index.html.twig', [
                    'fidele' => $fidele,
        ]);
    }

    #[Route('/add', name: 'fidele_add', methods: ['GET', 'POST'])]
    public function add(Request $request, FileUploader $fileUploader,  FideleRepository $fidelerepository, QuartierRepository $quartierRepository,
            ZoneRepository $zoneRepository, FamilleRepository $familleRepository, FonctionRepository $fonctionRepository,
            CelluleRepository $celluleRepository, EthnieRepository $ethineRepository, CommuneRepository $communeRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $fidele = new Fidele();
        $entityManager = $this->getDoctrine()->getManager();
        //Appel de la fonction matricule automatique
        // Fin calcul d'age
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $quartier = $quartierRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $cellule = $celluleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $zone = $zoneRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $ethnie = $ethineRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $famille = $familleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $commune = $communeRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $fonction = $fonctionRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $form = $this->createForm(FideleType::class, $fidele, ['quartier' => $quartier, 'cellule' => $cellule, 'zone' => $zone, 'ethnie' => $ethnie, 'famille' => $famille, 'fonction' => $fonction, 'commune' => $commune]
        );

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {


            $fidele->setCreatedFromIp($this->GetIp());
            //            Insertion image de profile
            $brochureFile = $form->get('photo')->getData();
            if ($brochureFile) {
                $brochureFileName = $fileUploader->upload($brochureFile);
                $fidele->setPhotoFile($brochureFileName);
            }

            $fidele = $form->getData();

            // $listefidele = $fidelerepository->findBy(array(), array('id' => 'desc'), 1, 0);
            // $id = 0;
            // foreach ($listefidele as $value) {
            //     $id = $value->getId();
            // }
            // $val = $id + 1;

            // $idFidele = substr($val, 0, 4);
            // $conversion = $form['dateconversion']->getData();
            // $fideles = $form['nomfidele']->getData();
            // $year = $conversion->format('Y-m-d');
            // $year1 = explode('-', $year);
            // $nom = substr($fideles, 0, 1);
            // $code = $year1[0] . $nom . $idFidele;
            // $fidele->setCode($code);

           $nom = $form['nomfidele']->getData();

        $code = $this->generateFideleCode($eglise, $nom);
                $fidele->setCode($code);

            $fidele->setEtatfidele("1");
            // On met etat marié à 1 si le fidèle est arié et à 0 dans le cas contraire
            $mariage = $form['statutmatri']->getData();

            if ($mariage != 'Marié(e)') {
                $fidele->setEtatmariage('0');
                $fidele->setDatemariage(NULL);
                $fidele->setPasteurmariage(NULL);
                $fidele->setLieumariage(NULL);
                $fidele->setNommariage(NULL);
            }
            if ($mariage == 'Marié(e)') {
                $fidele->setEtatmariage('1');
            }
            //Comparaison entre de naissance $datenaiss  et date de conversion $dateconversion

            $naissance = $form['datenaiss']->getData();
            $converti = $form['dateconversion']->getData();
            if ($converti < $naissance) {
                $this->addFlash('warning', 'Date de conversion ne peut être inferieure à la date de naissance.');
                return $this->redirect('add');
            }



            //Comparaison entre la date de conversion et la date de baptème

            $datebapteme = $form['datebapteme']->getData();
            if ($datebapteme) {
                $convert1 = $form['dateconversion']->getData();

                if ($datebapteme < $convert1 ) {
                    $this->addFlash('warning', 'Date de baptème ne peut être inferieure à la date de conversion.');
                    return $this->redirect('add');
                }
            }
            //Calcul d'age en fonction de la date de naissance et la date d'ohjodui
            $naiss = $form['datenaiss']->getData();
            $formatyear = $naiss->format('Y-m-d');
            $aujourdhui = new DateTime("now");
            $formatojodui = $aujourdhui->format('Y-m-d');
            if ($formatojodui <= $formatyear) {
                $this->addFlash('warning', 'Veuillez verifier la date de naissance.');
                return $this->redirect('add');
            }

//            $fidele->setAge($age1);
            $eglise = $this->getUser()->getEglise();
            $user = $this->getUser();
            $fidele->setCreatedBy($user);
            $fidele->setEglise($eglise);
            $entityManager->persist($fidele);
            $entityManager->flush();

            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'fidele_add' : 'fidele';
            if ($nextAction) {
                $this->addFlash('success', 'Enregistrement avec succès.');
            }

            return $this->redirectToRoute($nextAction);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('fidele/add.html.twig', [
                    'fidele' => $fidele,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    #[Route('/{id}/detail', name: 'fidele_detail', methods: ['GET', 'POST'])]
    public function detail(Fidele $fidele): Response {
        return $this->render('fidele/detail.html.twig', [
                    'fidele' => $fidele,
        ]);
    }

    #[Route('/{id}/update', name: 'fidele_update')]
    public function update(Request $request, Fidele $fidele, FileUploader $fileUploader,  FideleRepository $fideleRepository, QuartierRepository $quartierRepository,
            ZoneRepository $zoneRepository, FamilleRepository $familleRepository, FonctionRepository $fonctionRepository,
            CelluleRepository $celluleRepository, EthnieRepository $ethineRepository,  CommuneRepository $communeRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $data = json_decode($request->getContent(), true);
        $user = $this->getUser();

        $eglise = $this->getUser()->getEglise()->getId();
        $quartier = $quartierRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $cellule = $celluleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $zone = $zoneRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $ethnie = $ethineRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $famille = $familleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $commune = $communeRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $fonction = $fonctionRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);

        $form = $this->createForm(FideleType::class, $fidele, ['quartier' => $quartier, 'cellule' => $cellule, 'zone' => $zone, 'ethnie' => $ethnie, 'famille' => $famille, 'fonction' => $fonction, 'commune' => $commune]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $fidele->setUpdatedFromIp($this->GetIp());
            $user = $this->getUser();
            $fidele->setUpdatedBy($user);

            // Calcul d'âge à après modification de la date de naissance
            $naiss = $form['datenaiss']->getData();
            $formatyear = $naiss->format('Y-m-d');
            $aujourdhui = new DateTime("now");
            $formatojodui = $aujourdhui->format('Y-m-d');


            if ($formatojodui <= $formatyear) {
                $this->addFlash('warning', 'Veuillez verifier la date de naissance.');
                return $this->redirect('update');
            }

            //$fidele->setAge($age1);

            //Comparaison entre de naissance $datenaiss  et date de conversion $dateconversion

            $naissance = $form['datenaiss']->getData();
            $formatnaiss = $naissance->format('Y');
            $converti = $form['dateconversion']->getData();
            $formatconvertion = $converti->format('Y');
            $compare = ($formatconvertion - $formatnaiss);
            if ($compare < 0) {
                $this->addFlash('warning', 'Date de conversion ne peut être inferieure à la date de naissance.');
                return $this->redirect('update');
            }

            //Comparaison entre la date de conversion et la date de baptème

            $datebapteme = $form['datebapteme']->getData();
            if ($datebapteme) {
                $formatbapteme = $datebapteme->format('Y-m-d');
                $convert1 = $form['dateconversion']->getData();
                $formatconvertion = $convert1->format('Y-m-d');
                if ($formatbapteme < $formatconvertion) {
                    $this->addFlash('warning', 'Date de baptème ne peut être inferieure à la date de conversion.');
                    return $this->redirect('add');
                }
            }

            //            Insertion image de profile
        $brochureFile = $form->get('photo')->getData();
            if ($brochureFile) {
                $brochureFileName = $fileUploader->upload($brochureFile);
                $fidele->setPhotoFile($brochureFileName);
            }
            // On vérifie si le fidèle est baptisé, dans le cas contraire, on met les informations sur le baptême à null
            // On met etat marié à 1 si le fidèle est arié et à 0 dans le cas contraire
            $mariage = $form['statutmatri']->getData();

            if ($mariage != 'Marié(e)') {
                $fidele->setEtatmariage('0');
                $fidele->setDatemariage(NULL);
                $fidele->setPasteurmariage(NULL);
                $fidele->setLieumariage(NULL);
                $fidele->setNommariage(NULL);
            }
            if ($mariage == 'Marié(e)') {
                $fidele->setEtatmariage('1');
            }

            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Enregistrement avec succès.');
            return $this->redirectToRoute('fidele');
        }

        return $this->render('fidele/update.html.twig', [
                    'fidele' => $fidele,
                    'form' => $form->createView(),
        ]);
    }

    #[Route('/print', name: 'fidele_print', methods: ['GET', 'POST'])]
    public function printfidele(FideleRepository $fideleRepository): Response {
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
        $fidele = $fideleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL, "etatfidele" => 1]);

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('fidele/print.html.twig', [
            'fidele' => $fidele,
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

    #[Route('/{id}/printbyid', name: 'fidele_printbyid', methods: ['GET', 'POST'])]
    public function printById(int $id, EntityManagerInterface $entityManager, FideleRepository $fideleRepository, Fidele $fidele, Request $request): Response {
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
        $dompdf->setHttpContext($contxt);
        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
//        $fidele = $fideleRepository->findAll();
        $fidele = $fideleRepository->findOneById($id);

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('fidele/printbyid.html.twig', [
            'fidele' => $fidele,
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

//    #[Route('/{id}', name: 'fidele_delete', methods: ['POST'])]
//
//    public function delete(Request $request, Fidele $fidele): Response {
//        if ($this->isCsrfTokenValid('delete' . $fidele->getId(), $request->request->get('_token'))) {
//            $entityManager = $this->getDoctrine()->getManager();
//            $entityManager->remove($fidele);
//            $entityManager->flush();
//        }
//
//        return $this->redirectToRoute('fidele');
//    }
//    Suppression avec modification du statut du fidèle

    /**
     * @Route("/fidele/supp/{id}", name="fidele_supp")
     */
    public function supp(Request $request, Fidele $fidele): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $data = json_decode($request->getContent(), true);

        $form = $this->createForm(DeletefideleType::class, $fidele);
        $form->handleRequest($request);

        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        if ($form->isSubmitted() && $form->isValid()) {

            $fidele->setDeletedFromIp($this->GetIp());
            $fidele = $form->getData();
            $fidele->setEtatfidele("0");
            $fidele->setEditable("0");
            $fidele->setUpdatedBy($user);
            $fidele->setDeletedBy($user);
            $fidele->setDeletedAt(new DateTime("now"));
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('fidele');
        }

        return $this->render('fidele/supp.html.twig', [
                    'fidele' => $fidele,
                    'form' => $form->createView(),
                    'adjectif' => 'Suppression',
        ]);
    }

    #[Route('/archive', name: 'fidele_archivefidele')]
    public function listeSupp(FideleRepository $fideleRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $fidele = $fideleRepository->findBy(['eglise' => $eglise, "etatfidele" => 0, "editable" => 0]);

        return $this->render('fidele/archivefidele.html.twig', [
                    'fidele' => $fidele,
        ]);
    }

    /**
     * @Route("/restaure/{id}", name="fidele_restaure")
     */
    public function restaure(Request $request, Fidele $fidele): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if ($this->isCsrfTokenValid('restaurefi' . $fidele->getId(), $request->request->get('_token'))) {
            $user = $this->getUser();

            $entityManager = $this->getDoctrine()->getManager();
            if (!$this->isGranted('ROLE_SECRETAIRE')) {
                throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
            }



            $fidele->setEtatfidele("1");
            $fidele->setEditable("1");
            $fidele->setDeletedBy(NULL);
            $fidele->setDeletedAt(NULL);
            $this->addFlash('success', 'Restauration avec succès.');

            $entityManager->flush();
        }

        return $this->redirectToRoute('fidele');
    }
    //Preinscription
    #[Route('/preinscription', name: 'fidele_preinscription', methods: ['GET','POST'])]
public function rechercheEglise2(Request $request, EgliseRepository $egliseRepository): Response
{
    if ($request->isMethod('POST')) {

        $code = $request->request->get('recherchechurch');

        $eglise = $egliseRepository->findOneBy(['code' => $code]);
        

        if (!$eglise) {
            $this->addFlash('error', 'Aucune église trouvée avec ce code');
            return $this->render('fidele/recherche_eglise.html.twig');
        }

        return $this->redirectToRoute('fidele_preinscription_form', [
            'code' => $eglise->getCode()
        ]);
    }

    return $this->render('fidele/recherche_eglise.html.twig');
}
#[Route('/preinscription/{code}', name: 'fidele_preinscription_form', methods: ['GET','POST'])]
public function preinscriptionForm(
    string $code,
    Request $request,
    EgliseRepository $egliseRepository,
    QuartierRepository $quartierRepository,
    ZoneRepository $zoneRepository,
    FamilleRepository $familleRepository,
    FonctionRepository $fonctionRepository,
    CelluleRepository $celluleRepository,
    EthnieRepository $ethnieRepository,
    CommuneRepository $communeRepository,
     FileUploader $fileUploader,
    EntityManagerInterface $em
): Response {

    $eglise = $egliseRepository->findOneBy(['code'=>$code]);

    if(!$eglise){
        throw $this->createNotFoundException("Église introuvable");
    }

    $fidele = new Fidele();

    $form = $this->createForm(PreinscriptionType::class, $fidele, [
        'quartier' => $quartierRepository->findBy(['eglise'=>$eglise]),
        'cellule' => $celluleRepository->findBy(['eglise'=>$eglise]),
        'zone' => $zoneRepository->findBy(['eglise'=>$eglise]),
        'ethnie' => $ethnieRepository->findBy(['eglise'=>$eglise]),
        'famille' => $familleRepository->findBy(['eglise'=>$eglise]),
        'fonction' => $fonctionRepository->findBy(['eglise'=>$eglise]),
        'commune' => $communeRepository->findBy(['eglise'=>$eglise]),
    ]);

    $form->handleRequest($request);

    if($form->isSubmitted() && $form->isValid()){
        $formTime = $form->get('form_time')->getData();

    if (time() - $formTime < 3) {
        $this->addFlash('error', 'Soumission trop rapide');
        return $this->redirectToRoute('fidele_preinscription');
    }

      $nom = $form['nomfidele']->getData();

        $code = $this->generateFideleCode($eglise, $nom);

       $fidele->setCode($code);
        $fidele->setEglise($eglise);
        $fidele->setEtatfidele(0);

          //            Insertion image de profile
            $brochureFile = $form->get('photo')->getData();
            if ($brochureFile) {
                $brochureFileName = $fileUploader->upload($brochureFile);
                $fidele->setPhotoFile($brochureFileName);
            }


        $em->persist($fidele);
        $em->flush();

        return $this->redirectToRoute('fidele_preinscription_success',[
            'id'=>$fidele->getId()
        ]);
    }

    return $this->render('fidele/preinscription.html.twig',[
        'form'=>$form->createView(),
        'eglise'=>$eglise
    ]);
}

/**
 * Génère un code unique pour le fidèle
 */
private function generateFideleCode(Eglise $eglise, string $nom): string
{
    $year = date('Y');

    $initial = strtoupper(substr($nom, 0, 1));

    $numero = $eglise->getLastFideleNumber() + 1;

    $eglise->setLastFideleNumber($numero);

    $numeroFormat = str_pad($numero, 5, '0', STR_PAD_LEFT);

    return $year.'-'.$initial.'-'.$numeroFormat;
}

/**
 * Valide les dates (naissance, conversion, baptême)
 */
private function validateDates($form, $fidele): bool
{
    $naissance = $form['datenaiss']->getData();
    $conversion = $form['dateconversion']->getData();
    
    if ($naissance && $conversion) {
        if ($conversion < $naissance) {
            $this->addFlash('warning', 'La date de conversion ne peut pas être antérieure à la date de naissance.');
            return false;
        }
    }
    
    $bapteme = $form['datebapteme']->getData();
    if ($bapteme && $conversion) {
        if ($bapteme < $conversion) {
            $this->addFlash('warning', 'La date de baptême ne peut pas être antérieure à la date de conversion.');
            return false;
        }
    }
    
    return true;
}

/**
 * Calcule l'âge du fidèle
 */
private function calculateAge($form, $fidele): void
{
    $naissance = $form['datenaiss']->getData();
    if ($naissance) {
        $now = new \DateTime();
        $age = $now->diff($naissance)->y;
        $fidele->setAge($age);
        
        if ($age <= 0) {
            $this->addFlash('warning', 'Vérifiez la date de naissance saisie.');
        }
    }
}

/**
 * Gère le statut matrimonial
 */
private function handleMatrimonialStatus($form, $fidele): void
{
    $statut = $form['statutmatri']->getData();
    
    if ($statut !== 'Marié(e)') {
        $fidele->setEtatmariage(false);
        $fidele->setDatemariage(null);
        $fidele->setPasteurmariage(null);
        $fidele->setLieumariage(null);
        $fidele->setNommariage(null);
    } else {
        $fidele->setEtatmariage(true);
    }
}

#[Route('/preinscription/success/{id}', name: 'fidele_preinscription_success')]
public function preinscriptionSuccess(Fidele $fidele): Response
{
    return $this->render('fidele/preinscription_success.html.twig', [
        'fidele' => $fidele
    ]);
}



    //Fin preinscription

//     #[Route('/preinscription', name: 'fidele_preinscription', methods: ['GET', 'POST'])]
//     public function preinscription(int $id = null, Request $request, string $photoDir = null, EgliseRepository $egliseRepository, FideleRepository $fidelerepository, QuartierRepository $quartierRepository,
//             ZoneRepository $zoneRepository, FamilleRepository $familleRepository, FonctionRepository $fonctionRepository,
//             CelluleRepository $celluleRepository, EntityManagerInterface $entityManager, EthnieRepository $ethineRepository, CommuneRepository $communeRepository): Response {
//         $fidele = new Fidele();

//         //Recherche de l'eglise pour faire preinscription
//         $recherche = $request->request->get('recherchechurch');
//         $eglisecode = $egliseRepository->findByCode($recherche);
// //        $ideglise = $request->query->get('id');
// //         $ligneeglise = $egliseRepository->find($ideglise);
// //        $nomeglise = $ligneeglise->getLibelle();
//         $eglise = $egliseRepository->findByCode($eglisecode);
// //        $ligneeglise = $egliseRepository->findBy($eglisecode);
// //        $nomeglise = $ligneeglise->getDenomination();

//         foreach ($eglisecode as $code) {
//             $eglise = $code;
//         }



// //        // Fin calcul d'age
// //        //   $eglise = $this->getUser()->getEglise();
// ////             $eglise = $egliseRepository->find($ideglise);
// //        $ligneeglise = $egliseRepository->find($eglise);
// //        $nomeglise = $ligneeglise->getDenomination();
//         $quartier = $quartierRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
//         $cellule = $celluleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
//         $zone = $zoneRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
//         $ethnie = $ethineRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
//         $famille = $familleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
//         $commune = $communeRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
//         $fonction = $fonctionRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
//         $form = $this->createForm(PreinscriptionType::class, $fidele, ['quartier' => $quartier, 'cellule' => $cellule, 'zone' => $zone, 'ethnie' => $ethnie, 'famille' => $famille, 'fonction' => $fonction, 'commune' => $commune]
//         );
//         $codeEglise = $request->request->get('codeeglise');

//         // && $form->isValid()
//         $form->handleRequest($request);

//         if ($form->isSubmitted()) {


//             $fidele->setCreatedFromIp($this->GetIp());
//             //            Insertion image de profile

//             if ($photo = $form['photo']->getData()) {
//                 $filename = bin2hex(random_bytes(6)) . '.' . $photo->guessExtension();
//                 try {
//                     $photo->move($photoDir, $filename);
//                 } catch (FileException $e) {
//                     // unable to upload the photo, give up
//                 }
//                 $fidele->setPhotoFile($filename);
//             }

//             $fidele = $form->getData();

//             $listefidele = $fidelerepository->findBy(array(), array('id' => 'desc'), 1, 0);
//             $id = 0;
//             foreach ($listefidele as $value) {
//                 $id = $value->getId();
//             }
//             $val = $id + 1;
//             $conversion = $form['dateconversion']->getData();
//             $fideles = $form['nomfidele']->getData();
//             $year = $conversion->format('Y-m-d');
//             $year1 = explode('-', $year);
//             $nom = substr($fideles, 0, 2);
//             $code1 = $year1[0] . $nom . $val;
//             $fidele->setCode($code1);
//             $fidele->setEtatfidele(0);

//             //Comparaison entre de naissance $datenaiss  et date de conversion $dateconversion

//             $naissance = $form['datenaiss']->getData();
//             $formatnaiss = $naissance->format('Y');
//             $converti = $form['dateconversion']->getData();
//             $formatconvertion = $converti->format('Y');
//             $compare = ($formatconvertion - $formatnaiss);
//             if ($compare < 0) {
//                 $this->addFlash('compare', 'Date de conversion ne peut être inferieure à la date de naissance.');
//                 return $this->redirect('add');
//             }

//             //Comparaison entre la date de conversion et la date de baptème

//             $datebapteme = $form['datebapteme']->getData();
//             if ($datebapteme) {
//                 $formatbapteme = $datebapteme->format('Y');
//                 $convert1 = $form['dateconversion']->getData();
//                 $formatconvertion = $convert1->format('Y');
//                 $compare2 = ($formatbapteme - $formatconvertion);
//                 if ($compare2 < 0) {
//                     $this->addFlash('compare1', 'Date de baptème ne peut être inferieure à la date de conversion.');
//                     return $this->redirect('add');
//                 }
//             }
//             //Calcul d'age en fonction de la date de naissance et la date d'ohjodui
//             $naiss = $form['datenaiss']->getData();
//             $formatyear = $naiss->format('Y');
//             $aujourdhui = new DateTime("now");
//             $formatojodui = $aujourdhui->format('Y');
//             $age1 = ($formatojodui - $formatyear);
//             if ($age1 <= 0) {
//                 $this->addFlash('echec', 'Veuillez verifier la date de naissance.');
//                 return $this->redirect('add');
//             }

//             $fidele->setAge($age1);
//             //  $eglise = $this->getUser()->getEglise();
//             // $user = $this->getUser();
//             //$fidele->setCreatedBy($user);
//             $enrEglise = $egliseRepository->findByCode($codeEglise);
// //            $lignchurch = $egliseRepository->find($id);
// //            $nomeglise = $lignchurch->getDenomination();
//             // On met etat marié à 1 si le fidèle est arié et à 0 dans le cas contraire
//             $mariage = $form['statutmatri']->getData();

//             if ($mariage != 'Marié(e)') {
//                 $fidele->setEtatmariage('0');
//                 $fidele->setDatemariage(NULL);
//                 $fidele->setPasteurmariage(NULL);
//                 $fidele->setLieumariage(NULL);
//                 $fidele->setNommariage(NULL);
//             }
//             if ($mariage == 'Marié(e)') {
//                 $fidele->setEtatmariage('1');
//             }

//             $fidele->setEtatfidele(0);
//             foreach ($enrEglise as $value) {
//                 $fidele->setEglise($value);
//             }

//             $entityManager = $this->getDoctrine()->getManager();
//             $entityManager->persist($fidele);
//             $entityManager->flush();

// //            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'fidele_preinscription' : 'fidele';
// //            if ($nextAction) {
// //                $this->addFlash('inscription', 'Enregistrement avec succès.');
// //            }
//             $this->addFlash('inscription', 'Enregistrement avec succès.');
//             return $this->redirectToRoute('app_login');
//         }
//         // $response = new Response(null, $form->isSubmitted() ? 422 : 200);
//         return $this->render('fidele/preinscription.html.twig', [
//                     'fidele' => $fidele,
//                     'code' => $recherche,
// //                    'denomination' => $nomeglise,
//                     'form' => $form->createView(),
//                         //  'response' => $response,
//         ]);
//     }

    #[Route('/listepreinscription', name: 'listepreinscription', methods: ['GET', 'POST'])]
    public function indexpreinscription(FideleRepository $fideleRepository, EnfantRepository $enfantRepository, CelluleRepository $celluleRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise()->getId();
        $user = $this->getUser();
        $enfant = $enfantRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL, "etatenfant" => 1]);

        $cellule = $celluleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $fidele = $fideleRepository->findBy(['eglise' => $eglise, "etatfidele" => 0, "deletedBy" => NULL]);
        $fidele2 = $fideleRepository->findBy(['eglise' => $eglise, "etatfidele" => 0, "deletedAt" => NULL]);
        $fidele3 = $fideleRepository->findBy(['eglise' => $eglise, "etatfidele" => 0, "deletedBy" => NULL]);
        return $this->render('fidele/listepreinscription.html.twig', [
                    'fidele' => $fidele,
                    'fideles' => $fidele3,
                    'inscrit' => $fidele2,
                    'enfant' => $enfant,
                    'cellule' => $cellule,
        ]);
    }

    #[Route('/{id}/detailpreinscription', name: 'fidele_detailpreinscription', methods: ['GET', 'POST'])]
    public function detailpreinscription(Fidele $fidele): Response {
        return $this->render('fidele/detailpreinscription.html.twig', [
                    'fidele' => $fidele,
        ]);
    }

    #[Route('/{id}/validation', name: 'fidele_validation', methods: ['GET', 'POST'])]
    public function validation(Request $request, Fidele $fidele, string $photoDir = null, FideleRepository $fideleRepository, QuartierRepository $quartierRepository,
            ZoneRepository $zoneRepository, FamilleRepository $familleRepository, FonctionRepository $fonctionRepository,
            CelluleRepository $celluleRepository, EthnieRepository $ethineRepository,  CommuneRepository $communeRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $data = json_decode($request->getContent(), true);

        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $quartier = $quartierRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $cellule = $celluleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $zone = $zoneRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $ethnie = $ethineRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $famille = $familleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $commune = $communeRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $fonction = $fonctionRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);

        $form = $this->createForm(FideleType::class, $fidele, ['quartier' => $quartier, 'cellule' => $cellule, 'zone' => $zone, 'ethnie' => $ethnie, 'famille' => $famille, 'fonction' => $fonction, 'commune' => $commune]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $fidele->setUpdatedBy($user);
            $fidele->setCreatedBy($user);
            $fidele->setEtatfidele(1);

            // Calcul d'âge à après modification de la date de naissance
            $naiss = $form['datenaiss']->getData();
            $formatyear = $naiss->format('Y');
            $aujourdhui = new DateTime("now");
            $formatojodui = $aujourdhui->format('Y');

            $age1 = ($formatojodui - $formatyear);

            if ($age1 <= 0) {
                $this->addFlash('warning', 'Veuillez verifier la date de naissance.');
                return $this->redirect('validation');
            }

            $fidele->setAge($age1);

            //Comparaison entre de naissance $datenaiss  et date de conversion $dateconversion

            $naissance = $form['datenaiss']->getData();
            $formatnaiss = $naissance->format('Y');
            $converti = $form['dateconversion']->getData();
            $formatconvertion = $converti->format('Y');
            $compare = ($formatconvertion - $formatnaiss);
            if ($compare < 0) {
                $this->addFlash('warning', 'Date de conversion ne peut être inferieure à la date de naissance.');
                return $this->redirect('validation');
            }

            //Comparaison entre la date de conversion et la date de baptème

            $datebapteme = $form['datebapteme']->getData();
            if ($datebapteme) {
                $formatbapteme = $datebapteme->format('Y');
                $convert1 = $form['dateconversion']->getData();
                $formatconvertion = $convert1->format('Y');
                $compare2 = ($formatbapteme - $formatconvertion);
                if ($compare2 < 0) {
                    $this->addFlash('warning', 'Date de baptème ne peut être inferieure à la date de conversion.');
                    return $this->redirect('validation');
                }
            }

            //            Insertion image de profile
            if ($photo = $form['photo']->getData()) {
                $filename = bin2hex(random_bytes(6)) . '.' . $photo->guessExtension();
                try {
                    $photo->move($photoDir, $filename);
                } catch (FileException $e) {
                    // unable to upload the photo, give up
                }
                $fidele->setPhotoFile($filename);
            }


            // On met etat marié à 1 si le fidèle est arié et à 0 dans le cas contraire
            $mariage = $form['statutmatri']->getData();

            if ($mariage != 'Marié(e)') {
                $fidele->setEtatmariage('0');
                $fidele->setDatemariage(NULL);
                $fidele->setPasteurmariage(NULL);
                $fidele->setLieumariage(NULL);
                $fidele->setNommariage(NULL);
            }
            if ($mariage == 'Marié(e)') {
                $fidele->setEtatmariage('1');
            }

            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Validation avec succès.');
            return $this->redirectToRoute('fidele');
        }

        return $this->render('fidele/validation.html.twig', [
                    'fidele' => $fidele,
                    'form' => $form->createView(),
        ]);
    }

    #[Route('/recherchurch', name: 'recherchchurch')]
    public function rechercheEglise(): Response {


        return $this->render('recherchurch/recherchechur.html.twig');
    }

    #[Route('/{id}', name: 'fidele_delete', methods: ['POST'])]
    public function delete(Request $request, Fidele $fidele, FideleRepository $fideleRepository): Response {
        if ($this->isCsrfTokenValid('delete' . $fidele->getId(), $request->request->get('_token'))) {
            if (!$this->isGranted('ROLE_SECRETAIRE')) {
                throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
            }
            $entityManager = $this->getDoctrine()->getManager();

            $fidele->setDeletedFromIp($this->GetIp());
            $fidele->setDeletedAt(new DateTime("now"));
            $fidele->setEditable("0");
            $fidele->setEtatfidele("0");
            $user = $this->getUser();
            $fidele->setDeletedBy($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('fidele');
    }

    #[Route('/{id}', name: 'fidele_rejet', methods: ['POST'])]
    public function refusPrrinscription(Request $request, Fidele $fidele): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($this->isCsrfTokenValid('delete' . $fidele->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($fidele);
            $this->addFlash('danger', 'Suppression effectuée avec succès.');
            $entityManager->flush();
        }

        return $this->redirectToRoute('istepreinscription', [], Response::HTTP_SEE_OTHER);
    }
//Recherche de quartier selon commune
    //Selection quartier par commune

    #[Route('/quartiers/{communesId}', name: 'get_quartiers', methods: ['GET'])]
    public function getQuartiers(int $communesId, QuartierRepository $quartierRepo, CommuneRepository $communeRepository): JsonResponse

    {
        $commune = $communeRepository->find($communesId);
        $quartiers = $quartierRepo->findBy(['commune' => $commune]);

        $data = array_map(fn($quartier)=>[
            'id' => $quartier->getId(),
            'libelle' => $quartier->getLibelle()
        ], $quartiers);

        return $this->json($data);
    }

      
    #[Route('/cellules/{zonesId}', name: 'get_cellules', methods: ['GET'])]
    public function getCellules(int $zonesId, CelluleRepository $celluleRepo, ZoneRepository $zoneRepository): JsonResponse

    {
        $zone = $zoneRepository->find($zonesId);
        $cellules = $celluleRepo->findBy(['zone' => $zone]);

        $data = array_map(fn($cellule)=>[
            'id' => $cellule->getId(),
            'nom' => $cellule->getNom()
        ], $cellules);

        return $this->json($data);
    }



    
 
}
