<?php

namespace App\Controller;

use App\Entity\Inscrire;
use App\Form\InscrireType;
use App\Form\RestaureinscrireType;
use App\Form\SuppinscrireType;
use App\Repository\ClassecodimRepository;
use App\Repository\EnfantRepository;
use App\Repository\InscrireRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Traits\ClientIp;


#[Route('/inscrire')]
class InscrireController extends AbstractController {
    use ClientIp;    
    
    #[Route('/', name: 'inscrire')]
    public function index(InscrireRepository $inscrireRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_ECODIM')) {
            throw $this->createAccessDeniedException('Accès refusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $inscrire = $inscrireRepository->findBy(['eglise' => $eglise, "etatinscrire" => 1]);
        return $this->render('inscrire/index.html.twig', [
                    'inscrire' => $inscrire,
        ]);
    }

    #[Route('/inscription', name: 'inscrire_inscription', methods: ['GET', 'POST'])]
    public function inscription(EntityManagerInterface $entityManager, Request $request, SluggerInterface $slugger, InscrireRepository $inscrireRepository, EnfantRepository $enfantRepository, ClassecodimRepository $classecodimRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_ECODIM')) {
            throw $this->createAccessDeniedException('Accès refusé, vous n\'avez pas les droits d\'accÃ¨s ici!');
        } $inscrire = new Inscrire();
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $inscrire->setCreatedBy($user);
        $inscrire->setEglise($eglise);
        $classecodim = $classecodimRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $enfant = $enfantRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $form = $this->createForm(InscrireType::class, $inscrire, ['enfant' => $enfant, 'classecodim' => $classecodim]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $inscrire->setCreatedFromIp($this->GetIp());
            $inscrire = $form->getData();

            $dql = $inscrireRepository->findBy(['enfant' => $inscrire->getEnfant(),
                'classecodim' => $inscrire->getClassecodim(),
            ]);

            if ($dql) {
                $this->addFlash('inscrire', 'Enfant dejà  inscrit dans cette classe.');
            } else {

                $inscrire->setEtatinscrire("1");
                $entityManager->persist($inscrire);
                $entityManager->flush();
//                $this->addFlash('success', 'Enregistrement effectuÃ© avec succÃ¨s.');
            }
            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'inscrire_inscription' : 'inscrire';
            if ($nextAction) {
                $this->addFlash('success', 'Enregistrement avec succès.');
            }

            return $this->redirectToRoute($nextAction);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('inscrire/inscription.html.twig', [
                    'inscrire' => $inscrire,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    #[Route('/{id}/detail', name: 'inscrire_detail', methods: ['GET', 'POST'])]
    public function detail(Inscrire $inscrire): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_ECODIM')) {
            throw $this->createAccessDeniedException('Accès refusé, vous n\'avez pas les droits d\'accÃ¨s ici!');
        }
        return $this->render('inscrire/detail.html.twig', [
                    'inscrire' => $inscrire,
        ]);
    }

    #[Route('/{id}/update', name: 'inscrire_update', methods: ['GET', 'POST'])]
    public function update(Request $request, Inscrire $inscrire, EnfantRepository $enfantRepository, ClassecodimRepository $classecodimRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_ECODIM')) {
            throw $this->createAccessDeniedException('Accès refusé, vous n\'avez pas les droits d\'accÃ¨s ici!');
        }
        $data = json_decode($request->getContent(), true);
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $classecodim = $classecodimRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $enfant = $enfantRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $form = $this->createForm(InscrireType::class, $inscrire, ['enfant' => $enfant, 'classecodim' => $classecodim]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {


            $inscrire->setUpdatedFromIp($this->GetIp());
            $inscrire->setUpdatedBy($user);
             $this->addFlash('success', 'Modification avec succès.');
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('inscrire');
        }
        return $this->render('inscrire/update.html.twig', [
                    'inscrire' => $inscrire,
                    'form' => $form->createView(),
        ]);
    }

    #[Route('/print', name: 'inscrire_print', methods: ['GET', 'POST'])]
    public function printinscrire(InscrireRepository $inscrireRepository): Response {
        if (!$this->isGranted('ROLE_RESPONSABLE_ECODIM')) {
            throw $this->createAccessDeniedException('Accès refusé, vous n\'avez pas les droits d\'accÃ¨s ici!');
        }
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        $inscrire = $inscrireRepository->findAll();

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('inscrire/print.html.twig', [
            'inscrire' => $inscrire,
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

    /**
     * @Route("/supp/{id}", name="inscrire_supp")
     */
    public function supp(Request $request, Inscrire $inscrire, EnfantRepository $enfantRepository, ClassecodimRepository $classecodimRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_ECODIM')) {
            throw $this->createAccessDeniedException('Accès refusé, vous n\'avez pas les droits d\'accÃ¨s ici!');
        }
        $data = json_decode($request->getContent(), true);
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $inscrire->setEglise($eglise);
//        $classecodim = $classecodimRepository->findByEglise($eglise);
//        $enfant = $enfantRepository->findByEglise($eglise);
        $form = $this->createForm(SuppinscrireType::class, $inscrire,);
        $form->handleRequest($request);
        $user = $this->getUser();
        if ($form->isSubmitted() && $form->isValid()) {


            $inscrire->setDeletedFromIp($this->GetIp());
            $inscrire = $form->getData();
            $inscrire->setEtatinscrire("0");
            $inscrire->setDeletedBy($user);
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('inscrire');
        }

        return $this->render('inscrire/supp.html.twig', [
                    'inscrire' => $inscrire,
                    'form' => $form->createView(),
                    'adjectif' => 'Suppression',
        ]);
    }

    #[Route('/archiveinscrire', name: 'inscrire_archiveinscrire')]
    public function listeSupp(InscrireRepository $inscrireRepository): Response {
        $em = $this->getDoctrine()->getManager();
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_ECODIM')) {
            throw $this->createAccessDeniedException('Acces refuse, vous n\'avez pas les droits d\'accÃ¨s ici!');
        }
        $eglise = $this->getUser()->getEglise()->getId();
        $user = $this->getUser();
        $inscrire = $inscrireRepository->findBy(['eglise' => $eglise, "etatinscrire" => 0]);

        return $this->render('inscrire/archiveinscrire.html.twig', [
                    'inscrire' => $inscrire,
        ]);
    }

    /**
     * @Route("/restaure/{id}", name="inscrire_restaure")
     */
    public function restaure(Request $request, Inscrire $inscrire): Response {
        $data = json_decode($request->getContent(), true);
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_ECODIM')) {
            throw $this->createAccessDeniedException('Accès refusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($this->isCsrfTokenValid('restaure' . $inscrire->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();

            $user = $this->getUser();
            $inscrire->setEtatinscrire("1");
            $this->addFlash('restaureenf2', 'Enfant réintegré avec succès');
            $entityManager->flush();
        }

        return $this->redirectToRoute('inscrire');
    }

    #[Route('/{id}', name: 'inscrire_delete', methods: ['POST'])]
    public function delete(Request $request, Inscrire $inscrire): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_ECODIM')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($this->isCsrfTokenValid('delete' . $inscrire->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();


            $inscrire->setDeletedFromIp($this->GetIp());
            $inscrire->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $inscrire->setEtatinscrire("0");
            $inscrire->setDeletedBy($user);
            $this->addFlash('danger', 'Supression avec succès');
            $entityManager->flush();
        }

        return $this->redirectToRoute('inscrire');
    }

}
