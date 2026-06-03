<?php

namespace App\Controller;

use App\Entity\Nationalite;
use App\Form\NationaliteType;
use App\Repository\FideleRepository;
use App\Repository\NationaliteRepository;
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
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/nationalite')]
class NationaliteController extends AbstractController {

    use ClientIp;

    #[Route('/', name: 'nationalite')]
    public function index(NationaliteRepository $nationaliteRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $roles = $user->getRoles();
        $nationalite = $nationaliteRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        return $this->render('nationalite/index.html.twig', [
                    'nationalite' => $nationalite,
        ]);
    }

    #[Route('/detail/{id}', name: 'nationalite_detail', methods: ['GET'])]
    public function detailnationalite(Request $request, FideleRepository $fideleRepository, NationaliteRepository $natioanliteRepo) {
        //Recuperation id nationalite
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $idnationalite = $request->query->get('id');
        //Recuperation de la liste des fidele par nationalite
        $listeFidele = $fideleRepository->findBy(['nationalite' => $idnationalite, "deletedAt" => NULL]);
        $lignepays = $natioanliteRepo->find($idnationalite);

        $nom = $lignepays->getLibelle();
        return $this->render('nationalite/detail.html.twig', [
                    'fidele' => $listeFidele,
                    'id' => $idnationalite,
                    'nom' => $nom,
        ]);
    }

   // #[Route('/{id}/update', name: 'nationalite_update', methods: ['GET', 'POST'])]
    #[Route('/add', name: 'nationalite_add', methods: ['GET', 'POST'])]
    public function add(EntityManagerInterface $entityManager, Request $request, ?Nationalite $nationalite = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();
        $type = $nationalite === null ? 'add' : 'update';
        $nationalite = $nationalite === null ? new Nationalite() : $nationalite;
        $form = $this->createForm(NationaliteType::class, $nationalite);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {


            if ($type === 'add') {
                $nationalite->setCreatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setEglise($user->getEglise())
                        ->setCreatedBy($user)
                ;
            } else {
                $nationalite->setUpdatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setUpdatedBy($user)
                ;
            }
            $entityManager->persist($nationalite);
            $entityManager->flush();
            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'nationalite_add' : 'nationalite';
            if ($nextAction) {
                $this->addFlash('success', 'Action effectuée avec succès.');
            }

            return $this->redirectToRoute($nextAction);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('nationalite/add.html.twig', [
                    'nationalite' => $nationalite,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    #[Route('/print', name: 'nationalite_print', methods: ['GET', 'POST'])]
    public function print(NationaliteRepository $nationaliteRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
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

        $nationalite = $nationaliteRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('nationalite/print.html.twig', [
            'nationalite' => $nationalite,
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
            "Attachment" => false
        ]);
    }

//    #[Route('/enfant/{id}', name: 'nationalite_enfant', methods: ['GET'])]
//    public function listeEnfant(Request $request, EnfantRepository $enfantRepository, NationaliteRepository $nationaliteRepo) {
//        //Recuperation id nationalite
//        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
//        if (!$this->isGranted('ROLE_SECRETAIRE')) {
//            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
//        }
//        $user = $this->getUser();
//        $idnationalite = $request->query->get('id');
//        //Recuperation de la liste des fidele par nationalite
//        $listeEnfant = $enfantRepository->findBy(['nationalite' => $idnationalite, 'deletedAt' => NULL]);
//        $ligneNationalite = $nationaliteRepo->find($idnationalite);
//        $nomNationalite = $ligneNationalite->getLibelle();
//        return $this->render('nationalite/enfant.html.twig', [
//                    'enfants' => $listeEnfant,
//                    'id' => $idnationalite,
//                    'nomnationalite' => $nomNationalite,
//        ]);
//    }
    
    
     
    
     #[Route('/update/datatable/{id}', name: 'nationalite_update_datatable', methods: ['POST'])]
    public function nationaliteUpdateDatatable(EntityManagerInterface $entityManager, Request $request, ?Nationalite $nationalite = null): JsonResponse {

        $return = [
            'update' => false,
            'notification' => false,
        ];
        $new_nationalite = \strip_tags($request->request->get('nationalite'));

        // Si l'entité existe et que le nouveau nom de la nationalite apre le strip_tags comporte plus que 0 caractères
        if ($nationalite && strlen($new_nationalite) > 0) {
            // strip_tags pour enlever tout code html
            // évite d'envoyer des balise <script>
            // ref: https://www.php.net/manual/fr/function.strip-tags.php
            $nationalite->setLibelle($new_nationalite);
            $user = $this->getUser();
            $nationalite->setUpdatedBy($user)
                    ->setUpdatedFromIp($this->GetIp());
            $entityManager->persist($nationalite);
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

    #[Route('/add1', name: 'nationalite_add1', methods: ['GET', 'POST'])]
    public function newCommune(Request $request, EntityManagerInterface $entityManager): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $nationalite = new Nationalite();
        $form = $this->createForm(NationaliteType::class, $nationalite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $nationalite->setCreatedFromIp($this->GetIp());
            $user = $this->getUser();
            $eglise = $this->getUser()->getEglise();
            $nationalite->setCreatedBy($user);
            $nationalite->setEglise($eglise);
            $entityManager->persist($nationalite);
            $entityManager->flush();
            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'nationalite_add1' : 'nationalite';
            if ($nextAction) {
                $this->addFlash('success', 'Action effectuée avec succès.');
            }

            return $this->redirectToRoute($nextAction);
        }

        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('nationalite/add1.html.twig', [
                    'nationalite' => '$nationalite',
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

 
    
    

         /**
     * @Route("/search/fideles/{id}", name="nationalite_search_fideles", requirements={"id"="\d+"}, methods={"POST"})
     *
     * @return Response
     */
    public function nationaliteSearchFideles(SerializerInterface $serializer, Nationalite $nationalite): Response {
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($nationalite) {
            $fideles = (array) json_decode($serializer->serialize($nationalite->getFidele()->toArray(), 'json', ['groups' => ['public']]));
        } else {
            $fideles = [];
        }

        return new Response($this->renderView('nationalite/listefidele.html.twig', [
                    'fideles' => $fideles
        ]));
    }

    /**
     * @Route("/search/enfants/{id}", name="nationalite_search_enfants", requirements={"id"="\d+"}, methods={"POST"})
     *
     * @return Response
     */
    public function nationaliteSearchEnfants(SerializerInterface $serializer, Nationalite $nationalite): Response {
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($nationalite) {
            $enfants = (array) json_decode($serializer->serialize($nationalite->getEnfants()->toArray(), 'json', ['groups' => ['public']]));
        } else {
            $enfants = [];
        }

        return new Response($this->renderView('nationalite/enfant.html.twig', [
                    'enfants' => $enfants
        ]));
    }
    
    #[Route('/printmembre/', name: 'nationalite_printmembre', methods: ['GET', 'POST'])]
    public function printmembre(NationaliteRepository $nationaliteRepository, FideleRepository $fideleRepository, Request $request): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $id = $request->query->get('id');

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

        //Recuperation de la liste des fidele par nationalite

        $listeMembre = $fideleRepository->findBy(['nationalite' => $id]);
        $lignenationalite = $nationaliteRepository->find($id);
        $nomnationalite = $lignenationalite->getLibelle();
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('nationalite/printmembre.html.twig', [
            'fideles' => $listeMembre,
            'nomnationalite' => $nomnationalite,
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
            "Attachment" => false
        ]);
    }


    #[Route('/{id}', name: 'nationalite_delete', methods: ['POST'])]
    public function delete(Request $request, Nationalite $nationalite, NationaliteRepository $nationaliteRepository): Response {
        if ($this->isCsrfTokenValid('delete' . $nationalite->getId(), $request->request->get('_token'))) {
            if (!$this->isGranted('ROLE_SECRETAIRE')) {
                throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
            }
            $entityManager = $this->getDoctrine()->getManager();

            $nationalite->setDeletedFromIp($this->GetIp());
            $nationalite->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $nationalite->setDeletedBy($user);
            $this->addFlash('danger', 'Suppression avec succès.');

            $entityManager->flush();
        }

        return $this->redirectToRoute('nationalite');
    }

}
