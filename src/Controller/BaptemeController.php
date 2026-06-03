<?php

namespace App\Controller;

use App\Entity\Bapteme;
use App\Form\BaptemeType;
use App\Repository\BaptemeRepository;
use App\Repository\FideleRepository;
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

#[Route('/bapteme')]
class BaptemeController extends AbstractController {

    use ClientIp;

    #[Route('/', name: 'bapteme_index', methods: ['GET'])]
    public function index(BaptemeRepository $baptemeRepository): Response {
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $bapteme = $baptemeRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        return $this->render('bapteme/index.html.twig', [
                    'baptemes' => $bapteme,
        ]);
    }

    #[Route('/fidele/{id}', name: 'bapteme_fidele', methods: ['GET'])]
    public function listeFidele(Request $request, FideleRepository $fideleRepository, BaptemeRepository $baptemeRepo) {
        //Recuperation id bapteme
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $user = $this->getUser();
        $idbapteme = $request->query->get('id');
        //Recuperation de la liste des fidele par bapteme
        $listeFidele = $fideleRepository->findBy(['bapteme' => $idbapteme, 'deletedAt' => NULL]);
        $ligneBapteme = $baptemeRepo->find($idbapteme);
        $nomBapteme = $ligneBapteme->getPromotion();
        return $this->render('bapteme/fidele.html.twig', [
                    'fideles' => $listeFidele,
                    'id' => $idbapteme,
                    'nombapteme' => $nomBapteme,
        ]);
    }

    #[Route('/printmembre', name: 'bapteme_printmembre', methods: ['GET', 'POST'])]
    public function printMembre(BaptemeRepository $baptemeRepository, FideleRepository $fideleRepository, Request $request): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $id = $request->query->get('id');

        // Configure Dompdf according to your needs
        $pdfOptions = new Options(array('enable_remote' => true));
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->set('isHtml5ParserEnabled', true);

        $dompdf = new Dompdf($pdfOptions);

        //Recuperation de la liste des fidele par bapteme

        $listeMembre = $fideleRepository->findBy(['bapteme' => $id]);
        $lignebapteme = $baptemeRepository->find($id);
        $nombapteme = $lignebapteme->getPromotion();
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('bapteme/printmembre.html.twig', [
            'fideles' => $listeMembre,
            'nombapteme' => $nombapteme,
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
        $dompdf->stream("ekkllesia.pdf", [
            "Attachment" => false
                ],
        );
    }

    #[Route('/new', name: 'bapteme_new', methods: ['GET', 'POST'])]
    #[Route('/edit/{id}', name: 'bapteme_edit', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FideleRepository $fideleRepository, ?Bapteme $bapteme = null): Response {
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }

        $user = $this->getUser();
        $type = $bapteme === null ? 'new' : 'edit';
        $bapteme = $bapteme === null ? new Bapteme() : $bapteme;
        $form = $this->createForm(BaptemeType::class, $bapteme, [
            'fidele' => $fideleRepository->findBy(['eglise' => $user->getEglise(), "stutbapteme" => 'Non', "etatfidele" => 1])
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($type === 'new') {
                $bapteme->setCreatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setEglise($user->getEglise())
                        ->setCreatedBy($user)
                ;
                $this->addFlash('success', 'Enregistrement effectué avec succès');
            } else {
                $bapteme->setUpdatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setUpdatedBy($user)
                ;
                $this->addFlash('success', 'Modification effectuée avec succès');
            }


            $entityManager->persist($bapteme);

            foreach ($bapteme->getFidele()->toArray() as $fidele) {
                $fidele->setDatebapteme($bapteme->getDatebapteme())
                        ->setLieubapteme($bapteme->getLieubapteme())
                        ->setPasteurbapteme($bapteme->getPasteurofficient())
                        ->setStutbapteme('Oui')
                        ->setBapteme($bapteme)
                ;
                $entityManager->persist($fidele);
            }

            $entityManager->flush();

            return $this->redirectToRoute('bapteme_index');
        }

        return $this->render('bapteme/new.html.twig', [
                    'bapteme' => $bapteme,
                    'form' => $form->createView(),
        ]);
    }
    
    
    
    /**
     * @Route("/search/ames/{id}", name="bapteme_search_ames", requirements={"id"="\d+"}, methods={"POST"})
     *
     * @return Response
     */
    public function baptemeSearchFideles(SerializerInterface $serializer, Bapteme $bapteme): Response {
        if (!$this->isGranted('ROLE_RESPONSABLE_EVANGELISATION')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($bapteme) {
            $fideles = (array) json_decode($serializer->serialize($bapteme->getFidele()->toArray(), 'json', ['groups' => ['public']]));
        } else {
            $fideles = [];
        }
        return new Response($this->renderView('bapteme/listefidele.html.twig', [
                    'fideles' => $fideles
        ]));
    }

    #[Route('/{id}', name: 'bapteme_show', methods: ['GET'])]
    public function show(Bapteme $bapteme): Response {
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        return $this->render('bapteme/show.html.twig', [
                    'bapteme' => $bapteme,
        ]);
    }

    #[Route('/{id}', name: 'bapteme_delete', methods: ['POST'])]
    public function delete(Request $request, Bapteme $bapteme): Response {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($this->isCsrfTokenValid('delete' . $bapteme->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();

            $bapteme->setDeletedFromIp($this->GetIp());
            $bapteme->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $bapteme->setDeletedBy($user);
            $this->addFlash('danger', 'Supression avec succès');

            $entityManager->flush();
        }

        return $this->redirectToRoute('bapteme_index');
    }

}
