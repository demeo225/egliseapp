<?php

namespace App\Controller;

use App\Entity\Departement;
use App\Form\DepartementType;
use App\Form\DepartementMultipleType;
use App\Form\UserdepartementType;
use App\Repository\CotisationdepartementRepository;
use App\Repository\DepartementRepository;
use App\Repository\FideleRepository;
use App\Repository\GroupeRepository;
use App\Repository\PresencedepartementRepository;
use App\Repository\SeancedepartementRepository;
use App\Repository\UserRepository;
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

#[Route('/departement')]
class DepartementController extends AbstractController {

    use ClientIp;

    #[Route('/', name: 'departement', methods: ['GET'])]
    public function index(DepartementRepository $departementRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $departement = $departementRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        return $this->render('departement/index.html.twig', [
                    'departement' => $departement,
        ]);
    }

//    # #[Route('/detail/{id}', name: 'departement_detail', methods: ['GET'])]
//
//    public function detaildepartement(Request $request, GroupeRepository $groupeRepository, DepartementRepository $departementRepo) {
//        //Recuperation id departement
//        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
//        $user = $this->getUser();
//        $iddepartement = $request->query->get('id');
//        //Recuperation de la liste des fidele par departement
//        $listeGroupe = $groupeRepository->findBy(['departement' => $iddepartement]);
//        $ligneDepartement = $departementRepo->find($iddepartement);
//        $nomDepartement = $ligneDepartement->getNom();
//        return $this->render('departement/detail.html.twig', [
//                    'groupe' => $listeGroupe,
//                    'id' => $iddepartement,
//                    'nomdepartement' => $nomDepartement,
//        ]);
//    }

    #[Route('/detail/{id}', name: 'departement_detail', methods: ['GET'])]
    public function detaildepartement(Request $request, GroupeRepository $groupeRepository, DepartementRepository $departementRepo) {
        //Recuperation id departement
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $iddepartement = $request->query->get('id');
        //Recuperation de la liste des groupe par departement
        $listeGroupe = $groupeRepository->findBy(['departement' => $iddepartement, "deletedAt" => NULL]);
        $lignedepartement = $departementRepo->find($iddepartement);
        $nomdepart = $lignedepartement->getNom();
        return $this->render('departement/detail.html.twig', [
                    'groupe' => $listeGroupe,
                    'nompdepart' => $nomdepart,
                    'id' => $iddepartement,
        ]);
    }

    #[Route('/activite/{id}', name: 'departement_activite', methods: ['GET'])]
    public function activiteDepartement(Request $request, SeancedepartementRepository $activiteRepository, DepartementRepository $departementRepo) {
        //Recuperation id departement
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $iddepartement = $request->query->get('id');
        //Recuperation de la liste des fidele par departement
        $listeActivite = $activiteRepository->findBy(['departement' => $iddepartement, "deletedAt" => NULL]);
        $ligneDepartement = $departementRepo->find($iddepartement);
        $nomdepartement = $ligneDepartement->getNom();
        return $this->render('departement/activite.html.twig', [
                    'activitedepartements' => $listeActivite,
                    'nomdepartement' => $nomdepartement,
                    'eglise' => $eglise,
        ]);
    }

    #[Route('/cotisation/{id}', name: 'departement_cotisation', methods: ['GET'])]
    public function cotisationDepartement(Request $request, CotisationdepartementRepository $cotisationRepository, DepartementRepository $departementRepo) {
        //Recuperation id departement
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $iddepartement = $request->query->get('id');
        //Recuperation de la liste des fidele par departement
        $listeCotisation = $cotisationRepository->findBy(['departement' => $iddepartement, "deletedAt" => NULL]);
        $ligneDepartement = $departementRepo->find($iddepartement);
        $nomdepartement = $ligneDepartement->getNom();
        return $this->render('departement/cotisation.html.twig', [
                    'cotisationdepartements' => $listeCotisation,
                    'nomdepartement' => $nomdepartement,
                    'eglise' => $eglise,
        ]);
    }

    #[Route('/presence/{id}', name: 'departement_presence', methods: ['GET'])]
    public function presenceDepartement(Request $request, PresencedepartementRepository $presencedepartementRepository, DepartementRepository $departementRepo) {
        //Recuperation id departement
        if (!$this->isGranted('ROLE_RESPONSABLE_CELLULE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $iddepartement = $request->query->get('id');
        //Recuperation de la liste des fidele par departement
        $listePresence = $presencedepartementRepository->findBy(['departement' => $iddepartement, "deletedAt" => NULL]);
        $ligneDepartement = $departementRepo->find($iddepartement);
        $nomdepartement = $ligneDepartement->getNom();
        return $this->render('departement/presence.html.twig', [
                    'presencedepartements' => $listePresence,
                    'nomdepartement' => $nomdepartement,
                    'eglise' => $eglise,
        ]);
    }

    #[Route('/{id}/update', name: 'departement_update', methods: ['GET', 'POST'])]
    public function update(Request $request, Departement $departement): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $data = json_decode($request->getContent(), true);
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $form = $this->createForm(DepartementType::class, $departement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $departement->setUpdatedFromIp($this->GetIp());
            $user = $this->getUser();
            $departement->setUpdatedBy($user);
            $this->getDoctrine()->getManager()->flush();

                $this->addFlash('success', 'Modification avec succès.');
          
            return $this->redirectToRoute('departement');
        }
        return $this->render('departement/update.html.twig', [
                    'departement' => $departement,
                    'form' => $form->createView(),
                        ]);
    }

    //   #[Route('/{id}/update', name: 'departement_update', methods: ['GET', 'POST'])]
    #[Route('/add', name: 'departement_add', methods: ['GET', 'POST'])]
    public function add(EntityManagerInterface $entityManager, Request $request): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $user = $this->getUser();

        $departement = new Departement();
        $eglise = $this->getUser()->getEglise();
        $form = $this->createForm(DepartementType::class, $departement);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

                         //Verification doublon
            $existingFonction = $entityManager->getRepository(Departement::class)->findOneBy([
            'nom' => $departement->getNom(),
            'eglise' => $eglise,
            'deletedAt' => null
                ]);

                if ($existingFonction && ($existingFonction->getId() !== $departement->getId())) {
                    $this->addFlash('danger', 'Une departement avec ce nom existe déjà.');
                    return $this->redirectToRoute('departements_multiple_add');
                }


            $departement->setCreatedFromIp($this->GetIp()) // remplacement de la function par le trait
                    ->setEglise($user->getEglise())
                    ->setCreatedBy($user)
            ;

            $entityManager->persist($departement);
            $entityManager->flush();
            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'departement_add' : 'departement';
            if ($nextAction) {
                $this->addFlash('success', 'Enregistrement avec succès.');
            }
            return $this->redirectToRoute($nextAction);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('departement/add.html.twig', [
                    'departement' => $departement,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    
     #[Route('/update/datatable/{id}', name: 'departement_update_datatable', methods: ['POST'])]
    public function departementUpdateDatatable(EntityManagerInterface $entityManager, Request $request, ?Departement $departement = null): JsonResponse {

        $return = [
            'update' => false,
            'notification' => false,
        ];
        $new_departement = \strip_tags($request->request->get('departement'));

        // Si l'entité existe et que le nouveau nom de la departement apre le strip_tags comporte plus que 0 caractères
        if ($departement && strlen($new_departement) > 0) {
            // strip_tags pour enlever tout code html
            // évite d'envoyer des balise <script>
            // ref: https://www.php.net/manual/fr/function.strip-tags.php
            $departement->setNom($new_departement);
            $user = $this->getUser();
            $departement->setUpdatedBy($user)
                    ->setUpdatedFromIp($this->GetIp());
            $entityManager->persist($departement);
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

    #[Route('/add1', name: 'departement_add1', methods: ['GET', 'POST'])]
    public function newDepartement(Request $request, EntityManagerInterface $entityManager): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $departement = new Departement();
        $form = $this->createForm(DepartementType::class, $departement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $departement->setCreatedFromIp($this->GetIp());
            $user = $this->getUser();
            $eglise = $this->getUser()->getEglise();
            $departement->setCreatedBy($user);
            $departement->setEglise($eglise);
            $entityManager->persist($departement);
            $entityManager->flush();
            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'departement_add1' : 'departement';
            if ($nextAction) {
                $this->addFlash('departement', 'Enregistrement effectuée avec succès.');
            }

            return $this->redirectToRoute($nextAction);
        }

        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('departement/add1.html.twig', [
                    'departement' => $departement,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    
    #[Route('/print', name: 'departement_print', methods: ['GET', 'POST'])]
    public function printdepartement(DepartementRepository $departementRepository): Response {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise()->getId();
        $user = $this->getUser();
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        $departement = $departementRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('departement/print.html.twig', [
            'departement' => $departement,
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

    #[Route('/{id}/userdepartement', name: 'departement_userdepartement', methods: ['GET', 'POST'])]
    public function userdepartement(Request $request, Departement $departement, UserRepository $userRepository): Response {
        $data = json_decode($request->getContent(), true);
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user1 = $this->getUser();
        $user = $userRepository->findBy(['eglise' => $eglise, 'etat' => 1]);
        $form = $this->createForm(UserdepartementType::class, $departement, ['user' => $user,]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

             //Adresse ip de l'utilisateur
                /** @var User[] $selectedUsers */
            $selectedUsers = $form->get('users')->getData();

            foreach ($selectedUsers as $user) {
                $user->setDepartement($departement);
            }


            $departement->setUpdatedFromIp($this->GetIp());
            $departement->setUpdatedBy($user1);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('departement');
        }

        return $this->render('departement/userdepartement.html.twig', [
                    'departement' => $departement,
                    'form' => $form->createView(),
        ]);
    }

    #[Route('/printgroupe/', name: 'departement_printgroupe', methods: ['GET', 'POST'])]
    public function printgroupe(GroupeRepository $groupeRepository, FideleRepository $fideleRepo, DepartementRepository $departementRepository, Request $request): Response {
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

        //Recuperation de la liste des fidele par groupe

        $listeGroupe = $groupeRepository->findBy(['departement' => $id]);
        $lignedepartement = $departementRepository->find($id);
        $nomdepartement = $lignedepartement->getNom();
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('departement/printgroupe.html.twig', [
            'groupe' => $listeGroupe,
            'nomdepartement' => $nomdepartement,
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (force download)
        $dompdf->stream("mypdf.pdf", [
            "Attachment" => false
        ]);
    }

    #[Route('departement/{id}', name: 'departement_delete', methods: ['POST'])]
    public function delete(Request $request, Departement $departement, DepartementRepository $departementRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($this->isCsrfTokenValid('delete' . $departement->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();

            $departement->setDeletedFromIp($this->GetIp());
            $departement->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $departement->setDeletedBy($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('departement');
    }

    
    // Nouvelle méthode pour l'enregistrement multiple
    #[Route('/multiple/add', name: 'departements_multiple_add', methods: ['GET', 'POST'])]
    public function addMultiple(
        EntityManagerInterface $entityManager, 
        Request $request, 
        DepartementRepository $departementRepository
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }

        $user = $this->getUser();
        $eglise = $user->getEglise();


        
        // Récupérer les departements existants pour vérifier les doublons
        $existingDepartements = $entityManager->getRepository(Departement::class)->findBy([
            'eglise' => $eglise,
            'deletedAt' => null
        ]);
        
        // Créer un tableau des noms existants
        $existingNames = [];
        foreach ($existingDepartements as $existing) {
            $key = strtolower(trim($existing->getNom()));
            $existingNames[$key] = $existing->getNom();
        }
        
        // Créer le formulaire multiple
        $departementsData = ['departements' => []];
        $form = $this->createForm(DepartementMultipleType::class, $departementsData);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $departements = $data['departements'];
            $savedCount = 0;
            $errors = [];
            $submittedNames = [];
            
            // Validation de tous les departements
            foreach ($departements as $index => $departement) {
                $nom = trim($departement->getNom());
                $nomLower = strtolower($nom);
                $lineNumber = $index + 1;
                
                // Vérification champ vide
                if (empty($nom)) {
                    $errors[] = "Ligne {$lineNumber}: Le nom du departement ne peut pas être vide.";
                    continue;
                }
                
                // Vérification doublon avec base de données
                if (isset($existingNames[$nomLower])) {
                    $errors[] = "Ligne {$lineNumber}: Le departement '{$nom}' existe déjà.";
                    continue;
                }
                
                // Vérification doublon dans la soumission
                if (in_array($nomLower, $submittedNames)) {
                    $errors[] = "Ligne {$lineNumber}: Le departement '{$nom}' est en double dans la liste.";
                    continue;
                }
                
                $submittedNames[] = $nomLower;
            }
            
            // Affichage des erreurs
            if (!empty($errors)) {
                foreach ($errors as $error) {
                    $this->addFlash('danger', $error);
                }
                return $this->redirectToRoute('departement_multiple_add');
            }
            
            // Enregistrement des departements
            foreach ($departements as $departement) {
                $nom = trim($departement->getNom());
                
                if (!empty($nom)) {
                    $departement
                        ->setNom($nom)
                        ->setDescription($departement->getDescription())
                        ->setResponsable1($departement->getResponsable1())
                        ->setResponsable2($departement->getResponsable2())
                       // ->setDepartement($departement->getDepartement())
                        ->setCreatedFromIp($this->getIp())
                        ->setEglise($eglise)
                       // ->setIdeglise($eglise->getId())
                        ->setCreatedBy($user)
                        ->setCreateAt(new \DateTime());
                    
                    $entityManager->persist($departement);
                    $savedCount++;
                }
            }
            
            if ($savedCount > 0) {
                $entityManager->flush();
                $this->addFlash('success', $savedCount . ' departement(s) ont été enregistré(s) avec succès.');
            }
            
            return $this->redirectToRoute('departement');
        }
        
        return $this->render('departement/multiple_add.html.twig', [
            'form' => $form->createView(),
           // 'departements' => $departements,
        ]);
    }
    
    private function getIp(): string
    {
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
}


