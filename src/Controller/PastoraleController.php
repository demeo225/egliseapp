<?php

namespace App\Controller;

use App\Entity\Pastorale;
use App\Entity\Presencepastorale;
use App\Form\PastoraleType;
use App\Repository\FideleRepository;
use App\Repository\PastoraleRepository;
use App\Repository\PresencepastoraleRepository;
use App\Traits\ClientIp;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/pastorale')]
class PastoraleController extends AbstractController {

    use ClientIp;

    #[Route('/', name: 'app_pastorale_index', methods: ['GET'])]
    public function index(PastoraleRepository $pastoraleRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_PASTEUR')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $pastorale = $pastoraleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        return $this->render('pastorale/index.html.twig', [
                    'pastorales' => $pastorale,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_pastorale_edit', methods: ['GET', 'POST'])]
    #[Route('/new', name: 'app_pastorale_new', methods: ['GET', 'POST'])]
    public function new(Request $request, PastoraleRepository $pastoraleRepository, FideleRepository $fideleRepo, SluggerInterface $slugger, ?Pastorale $pastorale = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_PASTEUR')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $type = $pastorale === null ? 'new' : 'edit';
        $pastorale = $pastorale === null ? new Pastorale() : $pastorale;
        $user = $this->getUser();
        $eglise = $this->getUser()->getEglise();
        $fidele1 = $fideleRepo->findBy(['eglise' => $eglise, "deletedAt" => NULL, "typefidele" => 'Non', "etatfidele" => 1]);
        $fidele2 = $fideleRepo->findBy(['eglise' => $eglise, "deletedAt" => NULL,  "etatfidele" => 1]);
        $form = $this->createForm(PastoraleType::class, $pastorale, ['pasteur1' => $fidele1, 'pasteur2' => $fidele2]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($type === 'new') {


                $pastorale->setCreatedFromIp($this->GetIp())
                        ->setEglise($user->getEglise())
                        ->setCreatedBy($user)

                ;
                
                            /** @var UploadedFile $brochureFile */
            $brochureFile = $form->get('brochure')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $brochureFile->move(
                        $this->getParameter('brochures_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $product->setBrochureFilename($newFilename);
            }
                
            } else {
                $pastorale->setUpdatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setUpdatedBy($user)
                ;
                
                            /** @var UploadedFile $brochureFile */
            $brochureFile = $form->get('brochure')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $brochureFile->move(
                        $this->getParameter('brochures_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $pastorale->setBrochureFilename($newFilename);
            }
            }

            $pastoraleRepository->add($pastorale);
            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'app_pastorale_new' : 'app_pastorale_index';
            if ($nextAction) {
                $this->addFlash('success', 'Action effectuée avec succès.');
            }

            return $this->redirectToRoute($nextAction);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('pastorale/new.html.twig', [
                    'pastorale' => $pastorale,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }



    #[Route('/listepresence', name: 'pastorale_listepresence', methods:  ['GET', 'POST'])]
    public function listePresence(PresencepastoraleRepository $presenceRepository, Request $request, PastoraleRepository $pastoraleRepo): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $presencepastorale = $presenceRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $difference = $pastoraleRepo->getPastoralesByDates();
        return $this->render('pastorale/listepresence.html.twig', [
                    'presencepastorales' => $presencepastorale,
                    'differences' => $difference,
        ]);
    }

    #[Route('/presence', name: 'pastorale_presence', methods: ['POST', 'GET'])]
    public function presencePastorale(FideleRepository $fideleRepository, Request $request, PastoraleRepository $pastoraleRepository, PresencepastoraleRepository $presenceRepo): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        if ($request->isMethod('POST')) {
            $pastorale = $request->request->get('pastorale');
            $tabpost = $request->request->get('tab');

            foreach ($tabpost as $value) {
                $em = $this->getDoctrine()->getManager();
                $idfidele = $fideleRepository->find($value);
                $presencepastorale = new Presencepastorale();

                $idpastorale = $pastoraleRepository->find($pastorale);
//
                $dql = $presenceRepo->findBy(['fidele' => $presencepastorale->getFidele(), 'pastorale' => $presencepastorale->getPastorale()
                ]);
                if ($dql) {


                    $this->addFlash('presentp', 'Fidele déjà participant à cette pastorale.');
                    return $this->redirectToRoute('pastorale_presence', [], Response::HTTP_SEE_OTHER);
                } else {


                    $eglise = $this->getUser()->getEglise();
                    $user = $this->getUser();
                    $presencepastorale->setFidele($idfidele);
                    $presencepastorale->setPastorale($idpastorale);
                    $presencepastorale->setEglise($eglise);
                    $presencepastorale->setCreatedBy($user);
                    $em->persist($presencepastorale);
                    $em->flush();
                }
            }
            $this->addFlash('success', 'Enregistrement effectué avec succès');

            return $this->redirectToRoute('pastorale_listepresence');
        } else {
            $fidele = $fideleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL, "etatfidele" => 1]);

            $pastorale = $pastoraleRepository->findBy(['eglise' => $eglise]);
            return $this->render('pastorale/presence.html.twig',
                            [
                                'fideles' => $fidele,
                                'pastorales' => $pastorale
            ]);
        }
    }
    
    
        #[Route('/{id}', name: 'app_pastorale_show')]
    public function show(Pastorale $pastorale): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_PASTEUR')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        return $this->render('pastorale/show.html.twig', [
                    'pastorale' => $pastorale,
        ]);
    }

    #[Route('{id}/presencepastorale', name: 'presencepastorale_delete', methods: ['POST'])]
    public function deletePresence(Request $request, Presencepastorale $presencepastorale): Response {
        if ($this->isCsrfTokenValid('delete' . $presencepastorale->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();

 
            $presencepastorale->setDeletedFromIp($this->GetIp());
            $presencepastorale->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $presencepastorale->setDeletedBy($user);
            $entityManager->flush();
        }

        if ($request) {
            $this->addFlash('success', 'Suppression avec succès.');
        }

        return $this->redirectToRoute('pastorale_listepresence', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}', name: 'app_pastorale_delete', methods: ['POST'])]
    public function delete(Request $request, Pastorale $pastorale): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_PASTEUR')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if ($this->isCsrfTokenValid('delete' . $pastorale->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();

            $pastorale->setDeletedFromIp($this->GetIp());
            $pastorale->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $pastorale->setDeletedBy($user);
            $entityManager->flush();

            if ($request) {
                $this->addFlash('danger', 'Suppression avec succès.');
            }
        }

        return $this->redirectToRoute('app_pastorale_index');
    }

}
