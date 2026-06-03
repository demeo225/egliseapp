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
        $culte = $culteRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        return $this->render('culte/index.html.twig', [
                    'culte' => $culte,
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

    #[Route('/listepresence', name: 'culte_listepresence', methods: ['GET'])]
    public function listePresence(PresenceculteRepository $presenceRepository, Request $request, CulteRepository $culteRepo): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $presenceculte = $presenceRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $difference = $culteRepo->getCultesByDates();
        return $this->render('culte/listepresence.html.twig', [
                    'presencecultes' => $presenceculte,
                    'differences' => $difference,
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

            foreach ($tabpost as $value) {
                $em = $this->getDoctrine()->getManager();
                $idfidele = $fideleRepository->find($value);
                $presenceculte = new Presenceculte();

                $idculte = $culteRepository->find($culte);
//
                $dql = $presenceRepo->findBy(['fidele' => $presenceculte->getFidele(), 'culte' => $presenceculte->getCulte()
                ]);
                if ($dql) {


                    $this->addFlash('present', 'Fidele déjà participant à cette activité.');
                    return $this->redirectToRoute('culte_presence', [], Response::HTTP_SEE_OTHER);
                } else {


                    $eglise = $this->getUser()->getEglise();
                    $user = $this->getUser();
                    $presenceculte->setFidele($idfidele);
                    $presenceculte->setCulte($idculte);
                    $presenceculte->setEglise($eglise);
                    $presenceculte->setCreatedBy($this->getUser());
                    $em->persist($presenceculte);
                    $em->flush();
                }
            }
            $this->addFlash('message', 'Enregistrement effectué avec succès');

            return $this->redirectToRoute('culte_listepresence');
        } else {
            $fidele = $fideleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL, "etatfidele" => 1]);

            $culte = $culteRepository->findBy(['eglise' => $eglise]);
            return $this->render('culte/presence.html.twig',
                            [
                                'fideles' => $fidele,
                                'cultes' => $culte
            ]);
        }
    }

    #[Route('/{id}/update', name: 'culte_update', methods: ['GET', 'POST'])]
    #[Route('/add', name: 'culte_add', methods: ['GET', 'POST'])]
    public function add(EntityManagerInterface $entityManager, Request $request, FileUploader $fileUploader,  TypeculteRepository $typeculteRepository ,FideleRepository $fideleRepository, ?Culte $culte = null): Response {
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
         

            if ($type === 'add') {
                $culte->setCreatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setEglise($user->getEglise())
                        ->setCreatedBy($user)
                ;
                $this->addFlash('success', 'Enregistrement avec succès.');
            } else {
                $culte->setUpdatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setUpdatedBy($user)
                ;
                $this->addFlash('success', 'Modification effectuée  avec succès.');
            }
            $culte->setCreatedFromIp($this->GetIp());

            //Calcul d'age en fonction de la date de naissance et la date d'ohjodui
            $naiss = $form['dateculte']->getData();

            $aujourdhui = new DateTime("now");

            if ($aujourdhui < $naiss) {
                $this->addFlash('warning', 'Date éronnée.');
                return $this->redirect('add');
            }
            $culte->setCreatedBy($user);
            $culte->setEglise($eglise);
            $culte = $form->getData();
            $entityManager->persist($culte);
            $entityManager->flush();
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
