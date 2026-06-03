<?php

namespace App\Controller;

use App\Entity\Groupe;
use App\Form\GroupeType;
use App\Form\Groupe2Type;
use App\Form\UsergroupeType;
use App\Form\GroupeMultipleType;
use App\Repository\CotisationgroupeRepository;
use App\Repository\DepartementRepository;
use App\Repository\GroupefideleRepository;
use App\Repository\GroupeRepository;
use App\Repository\PresencegroupeRepository;
use App\Repository\SeancegroupeRepository;
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

#[Route('/groupe')]
class GroupeController extends AbstractController {

    use ClientIp;

    #[Route('/', name: 'groupe', methods: ['GET', 'POST'])]
    public function index(GroupeRepository $groupeRepository): Response {
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $groupe = $groupeRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        return $this->render('groupe/index.html.twig', [
                    'groupe' => $groupe,
        ]);
    }

    #[Route('/detail/{id}', name: 'groupe_detail', methods: ['GET'])]
    public function detailgroupe(Request $request, CotisationgroupeRepository $cotisationRepository, GroupeRepository $groupeRepo) {
        //Recuperation id groupe
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $idgroupe = $request->query->get('id');
        //Recuperation de la liste des fidele par groupe
        $listeCotisation = $cotisationRepository->findBy(['groupe' => $idgroupe, "deletedAt" => NULL]);
        $ligneGroupe = $groupeRepo->find($idgroupe);
        $nomgpe = $ligneGroupe->getNom();
        return $this->render('groupe/detail.html.twig', [
                    'cotisationgroupes' => $listeCotisation,
                    'nomgpe' => $nomgpe,
                    'eglise' => $eglise,
        ]);
    }

    #[Route('/activite/{id}', name: 'groupe_activite', methods: ['GET'])]
    public function activiteGroupe(Request $request, SeancegroupeRepository $activiteRepository, GroupeRepository $groupeRepo) {
        //Recuperation id groupe
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $idgroupe = $request->query->get('id');
        //Recuperation de la liste des fidele par groupe
        $listeActivite = $activiteRepository->findBy(['groupe' => $idgroupe, "deletedAt" => NULL]);
        $ligneGroupe = $groupeRepo->find($idgroupe);
        $nomgroupe = $ligneGroupe->getNom();
        return $this->render('groupe/activite.html.twig', [
                    'activitegroupes' => $listeActivite,
                    'nomgroupe' => $nomgroupe,
                    'eglise' => $eglise,
        ]);
    }

    #[Route('/presence/{id}', name: 'groupe_presence', methods: ['GET'])]
    public function presenceGroupe(Request $request, PresencegroupeRepository $presencegroupeRepository, GroupeRepository $groupeRepo) {
        //Recuperation id groupe
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $idgroupe = $request->query->get('id');
        //Recuperation de la liste des fidele par groupe
        $listePresence = $presencegroupeRepository->findBy(['groupe' => $idgroupe, "deletedAt" => NULL]);
        $ligneGroupe = $groupeRepo->find($idgroupe);
        $nomgroupe = $ligneGroupe->getNom();
        return $this->render('groupe/presence.html.twig', [
                    'presencegroupes' => $listePresence,
                    'nomgroupe' => $nomgroupe,
                    'eglise' => $eglise,
        ]);
    }

    //  #[Route('/{id}/update', name: 'groupe_update', methods: ['GET', 'POST'])]
    #[Route('/add', name: 'groupe_add', methods: ['GET', 'POST'])]
    public function add(EntityManagerInterface $entityManager, Request $request, DepartementRepository $departementRepository): Response {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }

        $user = $this->getUser();
        $groupe = new Groupe();
        $eglise = $this->getUser()->getEglise();

        $departement = $departementRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $form = $this->createForm(Groupe2Type::class, $groupe, ['departement' => $departement]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {



            $groupe->setCreatedFromIp($this->GetIp()) // remplacement de la function par le trait
                    ->setEglise($user->getEglise())
                    ->setCreatedBy($user)
            ;

            $entityManager->persist($groupe);
            $entityManager->flush();

            return $this->redirectToRoute('groupe');
        }
        return $this->render('groupe/add.html.twig', [
                    'groupe' => $groupe,
                    'form' => $form->createView(),
        ]);
    }

    #[Route('/printgroupe', name: 'groupe_printgroupe', methods: ['GET', 'POST'])]
    public function printgroupe(GroupeRepository $groupeRepository): Response {
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();

        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        $groupe = $groupeRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('groupe/printgroupe.html.twig', [
            'groupe' => $groupe,
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        
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
        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (force download)
        $dompdf->stream("mypdf.pdf", [
            "Attachment" => false
        ]);
    }

    #[Route('/update/datatable/{id}', name: 'groupe_update_datatable', methods: ['POST'])]
    public function groupeUpdateDatatable(EntityManagerInterface $entityManager, Request $request, ?Groupe $groupe = null): JsonResponse {

        $return = [
            'update' => false,
            'notification' => false,
        ];
        $new_groupe = \strip_tags($request->request->get('groupe'));

        // Si l'entité existe et que le nouveau nom de la groupe apre le strip_tags comporte plus que 0 caractères
        if ($groupe && strlen($new_groupe) > 0) {
            // strip_tags pour enlever tout code html
            // évite d'envoyer des balise <script>
            // ref: https://www.php.net/manual/fr/function.strip-tags.php
            $groupe->setNom($new_groupe);
            $user = $this->getUser();
            $groupe->setUpdatedBy($user)
                    ->setUpdatedFromIp($this->GetIp());
            $entityManager->persist($groupe);
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

    #[Route('/add1', name: 'groupe_add1', methods: ['GET', 'POST'])]
    public function newGroupe(Request $request, EntityManagerInterface $entityManager, DepartementRepository $departementRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $groupe = new Groupe();
        $eglise = $this->getUser()->getEglise();
        $departement = $departementRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $form = $this->createForm(GroupeType::class, $groupe, ['departement' => $departement,]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $groupe->setCreatedFromIp($this->GetIp());
            $user = $this->getUser();
            $eglise = $this->getUser()->getEglise();
            $groupe->setCreatedBy($user);
            $groupe->setEglise($eglise);
            $entityManager->persist($groupe);
            $entityManager->flush();
            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'groupe_add1' : 'groupe';
            if ($nextAction) {
                $this->addFlash('success', 'Action effectuée avec succès.');
            }

            return $this->redirectToRoute($nextAction);
        }

        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('groupe/add1.html.twig', [
                    'groupe' => '$groupe',
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    #[Route('/{id}/update', name: 'groupe_update', methods: ['GET', 'POST'])]
    public function updateGroupe(Request $request, Groupe $groupe, DepartementRepository $departementRepository): Response {
        $data = json_decode($request->getContent(), true);
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $user = $this->getUser();
        $eglise = $this->getUser()->getEglise();
        $departement = $departementRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $form = $this->createForm(GroupeType::class, $groupe, ['departement' => $departement,]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //Adresse ip de l'utilisateur
            $user = $this->getUser();

            $groupe->setUpdatedFromIp($this->GetIp());
            $groupe->setUpdatedBy($user);
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Modification avec succès');

            return $this->redirectToRoute('groupe');
        }
        return $this->render('groupe/update.html.twig', [
                    'groupe' => $groupe,
                    'form' => $form->createView(),
        ]);
    }

    #[Route('/bilanfidele', name: 'groupe_bilan', methods: ['POST', 'GET'])]
    public function getListeFideleByDepartement(Request $request, DepartementRepository $departementRepository, GroupefideleRepository $groupefidelerepository) {
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $depart = $request->request->get('selGroupe');
        $listedepartement = $departementRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        if (!$depart) {

            return $this->render('bilan/fidele_departement.html.twig',
                            [
                                'listefidele' => '',
                                'departement' => $listedepartement,
                            ]
            );
        } else {

            $listed = $groupefidelerepository->getListeFideleByDepartement($depart);

            return $this->render('bilan/fidele_departement.html.twig',
                            [
                                'listefidele' => $listed,
                                'departement' => $listedepartement,
            ]);
        }
    }

    #[Route('/fidelebygroupe', name: 'groupe_fidelegroupe', methods: ['POST', 'GET'])]
    public function getFideleByGroupe(Request $request, GroupeRepository $groupeRepository, GroupefideleRepository $groupefidelerepository) {
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $groupe = $request->request->get('selGroupe');
        $listegroupe = $groupeRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        if (!$groupe) {

            return $this->render('bilan/fidele_groupe.html.twig',
                            [
                                'listefidele' => '',
                                'groupe' => $listegroupe,
                            ]
            );
        } else {

            $listeg = $groupefidelerepository->getListeFideleByGroupe($groupe);

            return $this->render('bilan/fidele_groupe.html.twig',
                            [
                                'listefidele' => $listeg,
                                'groupe' => $listegroupe,
            ]);
        }
    }

    #[Route('/{id}/usergroupe', name: 'groupe_usergroupe', methods: ['GET', 'POST'])]
    public function usergroupe(Request $request, Groupe $groupe, UserRepository $userRepository): Response {
        $data = json_decode($request->getContent(), true);
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise()->getId();
        $user1 = $this->getUser();
        $user = $userRepository->findBy(['eglise' => $eglise, 'etat' => 1]);
        $form = $this->createForm(UsergroupeType::class, $groupe, ['user' => $user,]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //Adresse ip de l'utilisateur
          //Adresse ip de l'utilisateur
                /** @var User[] $selectedUsers */
            $selectedUsers = $form->get('users')->getData();

            foreach ($selectedUsers as $user) {
                $user->setGroupe($groupe);
            }

            $groupe->setUpdatedBy($user1);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('groupe');
        }

        return $this->render('groupe/usergroupe.html.twig', [
                    'groupe' => $groupe,
                    'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'groupe_delete', methods: ['POST'])]
    public function delete(Request $request, Groupe $groupe, GroupeRepository $groupeRepository): Response {
        if ($this->isCsrfTokenValid('delete' . $groupe->getId(), $request->request->get('_token'))) {
            if (!$this->isGranted('ROLE_SECRETAIRE')) {
                throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
            }
            $entityManager = $this->getDoctrine()->getManager();

            $groupe->setDeletedFromIp($this->GetIp());
            $groupe->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $groupe->setDeletedBy($user);
            $this->addFlash('danger', 'Suppression avec succès');
            $entityManager->flush();
        }

        return $this->redirectToRoute('groupe');
    }

    
      // Nouvelle méthode pour l'enregistrement multiple
    #[Route('/multiple/add', name: 'groupe_multiple_add', methods: ['GET', 'POST'])]
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

        // Récupérer les départements disponibles
        $departements = $departementRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        
        // Récupérer les groupes existants pour vérifier les doublons
        $existingGroupes = $entityManager->getRepository(Groupe::class)->findBy([
            'eglise' => $eglise,
            'deletedAt' => null
        ]);
        
        // Créer un tableau des noms existants
        $existingNames = [];
        foreach ($existingGroupes as $existing) {
            $key = strtolower(trim($existing->getNom()));
            $existingNames[$key] = $existing->getNom();
        }
        
        // Créer le formulaire multiple
        $groupesData = ['groupes' => []];
        $form = $this->createForm(GroupeMultipleType::class, $groupesData, [
            'departement' => $departements
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $groupes = $data['groupes'];
            $savedCount = 0;
            $errors = [];
            $submittedNames = [];
            
            // Validation de tous les groupes
            foreach ($groupes as $index => $groupe) {
                $nom = trim($groupe->getNom());
                $nomLower = strtolower($nom);
                $lineNumber = $index + 1;
                
                // Vérification champ vide
                if (empty($nom)) {
                    $errors[] = "Ligne {$lineNumber}: Le nom du groupe ne peut pas être vide.";
                    continue;
                }
                
                // Vérification doublon avec base de données
                if (isset($existingNames[$nomLower])) {
                    $errors[] = "Ligne {$lineNumber}: Le groupe '{$nom}' existe déjà.";
                    continue;
                }
                
                // Vérification doublon dans la soumission
                if (in_array($nomLower, $submittedNames)) {
                    $errors[] = "Ligne {$lineNumber}: Le groupe '{$nom}' est en double dans la liste.";
                    continue;
                }
                
                $submittedNames[] = $nomLower;
            }
            
            // Affichage des erreurs
            if (!empty($errors)) {
                foreach ($errors as $error) {
                    $this->addFlash('danger', $error);
                }
                return $this->redirectToRoute('groupe_multiple_add');
            }
            
            // Enregistrement des groupes
            foreach ($groupes as $groupe) {
                $nom = trim($groupe->getNom());
                
                if (!empty($nom)) {
                    $groupe
                        ->setNom($nom)
                        ->setDescription($groupe->getDescription())
                        ->setResponsable1($groupe->getResponsable1())
                        ->setResponsable2($groupe->getResponsable2())
                        ->setDepartement($groupe->getDepartement())
                        ->setCreatedFromIp($this->getIp())
                        ->setEglise($eglise)
                       // ->setIdeglise($eglise->getId())
                        ->setCreatedBy($user)
                        ->setCreateAt(new \DateTime());
                    
                    $entityManager->persist($groupe);
                    $savedCount++;
                }
            }
            
            if ($savedCount > 0) {
                $entityManager->flush();
                $this->addFlash('success', $savedCount . ' groupe(s) ont été enregistré(s) avec succès.');
            }
            
            return $this->redirectToRoute('groupe');
        }
        
        return $this->render('groupe/multiple_add.html.twig', [
            'form' => $form->createView(),
            'departements' => $departements,
        ]);
    }
    
    private function getIp(): string
    {
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }

}
