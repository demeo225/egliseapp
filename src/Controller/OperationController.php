<?php

namespace App\Controller;

use App\Entity\Operation;
use App\Entity\Solde;
use App\Form\OperationType;
use App\Form\UpdateoperationType;
use App\Repository\OperationRepository;
use App\Repository\SoldeRepository;
use App\Service\FileUploader;
use App\Traits\ClientIp;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function Sodium\bin2hex;

#[Route('/operation')]
class OperationController extends AbstractController {

    use ClientIp;

    #[Route('/', name: 'operation')]
    public function index(OperationRepository $operationRepository, SoldeRepository $soldeRepo): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_FINANCE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $user = $this->getUser();
        $eglise = $this->getUser()->getEglise();
        $solde = $soldeRepo->findBy(['eglise' => $eglise]);
        $operation = $operationRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        return $this->render('operation/index.html.twig', [
                    'operation' => $operation,
                    'soldes' => $solde,
        ]);
    }

    #[Route('/{id}/detail', name: 'operation_detail', methods: ['GET', 'POST'])]
    public function detail(Operation $operation): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        return $this->render('operation/detail.html.twig', [
                    'operation' => $operation,
        ]);
    }

    #[Route('/{id}/update', name: 'operation_update', methods: ['GET', 'POST'])]
    public function update(Request $request, EntityManagerInterface $entityManager, FileUploader $fileUploader, Operation $operation, SoldeRepository $soldeRepo): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_FINANCE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $data = json_decode($request->getContent(), true);

        $form = $this->createForm(UpdateoperationType::class, $operation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();

            $eglise = $this->getUser()->getEglise();

            //            Insertion image de profile
       $brochureFile = $form->get('photo')->getData();
            if ($brochureFile) {
                $brochureFileName = $fileUploader->upload($brochureFile);
                $operation->setPhotoFile($brochureFileName);
            }

            $operation->setUpdatedFromIp($this->GetIp());

            $nature = $form['typeof']->getData();

            if ($nature == 1) {
                $valeur = $form['ajout']->getData();

                // On cumule le montant total dans une table Montantoff
                $dql = $soldeRepo->findBy(['eglise' => $eglise]);

                $mont1 = $operation->getMontant();
                $mon = $valeur + $mont1;
                $operation->setMontant($mon);

                if ($dql) {
                    $id = $dql[0]->getId();
                    $activite = $soldeRepo->findOneBySolde($id);
                    $mont = $activite->getMontant();
                    $j = 0;
                    $j = $mont - $valeur;
                    $activite->setMontant($j);
                } else {
                    $montant = new Solde();
                    $off = 0 - $valeur;
                    $montant->setMontant($off);
                    $montant->setEglise($eglise);
                    $entityManager->persist($montant);
                }
            } elseif ($nature == 0) {
                $valeur2 = $form['ajout']->getData();
                $mont2 = $operation->getMontant();
                $mon1 = $mont2 - $valeur2;
                $operation->setMontant($mon1);

                // On crée le solde si inexitant et on decremente avec la valeur concernée sinon on decrement directement

                $dql2 = $soldeRepo->findBy(['eglise' => $eglise]);
                if ($dql2) {
                    $id = $dql2[0]->getId();
                    $activite = $soldeRepo->findOneBySolde($id);
                    $mont = $activite->getMontant();
                    $j = 0;
                    $j = $mont + $valeur2;
                    $activite->setMontant($j);
                } else {
                    $montant = new Solde();
                    $mont0 = + $valeur2;
                    $montant->setMontant($mont0);
                    $montant->setEglise($eglise);
                    $entityManager->persist($montant);
                }
            }


            $this->addFlash('success', 'Modification effectuée avec succès.');
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('operation');
        }

        return $this->render('operation/update.html.twig', [
                    'operation' => $operation,
                    'form' => $form->createView(),
        ]);
    }
 
    #[Route('/add', name: 'operation_add', methods: ['GET', 'POST'])]
    public function add(EntityManagerInterface $entityManager, Request $request, FileUploader $fileUploader, SoldeRepository $soldeRepo): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_FINANCE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $operation = new Operation();
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $form = $this->createForm(OperationType::class, $operation,);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //Adresse ip de l'utilisateur
            //            Insertion image de profile
        $brochureFile = $form->get('photo')->getData();
            if ($brochureFile) {
                $brochureFileName = $fileUploader->upload($brochureFile);
                $operation->setPhotoFile($brochureFileName);
            }

            $operation->setCreatedFromIp($this->GetIp());

            $operation->setCreatedBy($user);
            $operation->setEglise($eglise);
            $dixmille = $form['montant']->getData();
            // On cumule le montant total dans une table Montantoff,on crée le solde si niexistant et on y ajoute le montant
            $dql = $soldeRepo->findBy(['eglise' => $eglise]);
            if ($dql) {
                $id = $dql[0]->getId();
                $activite = $soldeRepo->findOneBySolde($id);
                $mont = $activite->getMontant();
                $j = 0;
                $j = $mont - $dixmille;
                $activite->setMontant($j);
            } else {
                $solde = new Solde();
                $mont1 = 0 - $dixmille;
                $solde->setMontant($mont1);
                $solde->setEglise($eglise);
                $entityManager->persist($solde);
            }

            $entityManager->persist($operation);

            $entityManager->flush();
            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'operation_add' : 'operation';
            if ($nextAction) {
                $this->addFlash('success', 'Enregistrement avec succès.');
            }
            return $this->redirectToRoute($nextAction);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);

        return $this->render('operation/add.html.twig', [
                    'operation' => $operation,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    #[Route('/add1', name: 'operation_add1', methods: ['GET', 'POST'])]
    public function add1(EntityManagerInterface $entityManager, Request $request, SoldeRepository $soldeRepo, string $photoDir = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_FINANCE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $operation = new Operation();

        $form = $this->createForm(OperationType::class, $operation,);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //Adresse ip de l'utilisateur
            //            Insertion image de profile
            if ($photo = $form['photo']->getData()) {
                $filename = bin2hex(random_bytes(6)) . '.' . $photo->guessExtension();
                try {
                    $photo->move($photoDir, $filename);
                } catch (FileException $e) {
                    // unable to upload the photo, give up
                }
                $operation->setPhotoFile($filename);
            }

            $operation->setCreatedFromIp($this->GetIp());
            $user = $this->getUser();
            $eglise = $this->getUser()->getEglise();
            $operation->setCreatedBy($user);
            $operation->setEglise($eglise);
            $dixmille2 = $form['montant']->getData();
            // On cumule le montant total dans une table Montantoff
            $dql = $soldeRepo->findBy(['eglise' => $eglise]);
            if ($dql) {
                $id = $dql[0]->getId();
                $activite = $soldeRepo->findOneBySolde($id);
                $mont = $activite->getMontant();
                $b = 0;
                $b = $mont - $dixmille2;
                $activite->setMontant($b);
            } else {
                $dixmille2 = $form['montant']->getData();
                $solde = new Solde();
                $mont1 = 0 - $dixmille2;
                $solde->setMontant($mont1);
                $solde->setEglise($eglise);
                $entityManager->persist($solde);
            }

            $entityManager->persist($operation);

            $entityManager->flush();

            $this->addFlash('danger', 'Enregistrement avec succès.');
            return $this->redirectToRoute('operation');
        }

        return $this->render('operation/add1.html.twig', [
                    'operation' => $operation,
                    'form' => $form->createView(),
        ]);
    }

    #[Route('operation/print', name: 'operation_print', methods: ['GET', 'POST'])]
    public function printoperation(OperationRepository $operationRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_FINANCE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();

        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        $operation = $operationRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('operation/print.html.twig', [
            'operation' => $operation,
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

    #[Route('/{id}', name: 'operation_delete', methods: ['POST'])]
    public function delete(Request $request, Operation $operation, SoldeRepository $soldeRepo): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_FINANCE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($this->isCsrfTokenValid('delete' . $operation->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();

            $operation->setDeletedFromIp($this->GetIp());
            $operation->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $eglise = $this->getUser()->getEglise();

            $total = $operation->getMontant();
            $dql = $soldeRepo->findBy(['eglise' => $eglise]);
            $id = $dql[0]->getId();
            $activite = $soldeRepo->findOneBySolde($id);
            $mont = $activite->getMontant();
            $j = 0;
            $j = $mont + $total;
            $activite->setMontant($j);
            $operation->setDeletedBy($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('operation');
    }

}
