<?php

namespace App\Controller;

use App\Entity\Classecodim;
use App\Form\ClassecodimType;
use App\Repository\ClassecodimRepository;
use App\Traits\ClientIp;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Flex\Options;

#[Route('/classecodim')]
class ClassecodimController extends AbstractController {

    use ClientIp;

    #[Route('/', name: 'classecodim')]
    public function index(ClassecodimRepository $classecodimRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_ECODIM')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $classecodim = $classecodimRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        return $this->render('classecodim/index.html.twig', [
                    'classecodims' => $classecodim,
        ]);
    }

    #[Route('/{id}/detail', name: 'classecodim_detail', methods: ['GET', 'POST'])]
    public function detail(Classecodim $classecodim): Response {
        if (!$this->isGranted('ROLE_RESPONSABLE_ECODIM')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        return $this->render('classecodim/detail.html.twig', [
                    'classecodim' => $classecodim,
        ]);
    }

   // #[Route('/{id}/update', name: 'classecodim_update', methods: ['GET', 'POST'])]
    #[Route('/add', name: 'classecodim_add', methods: ['GET', 'POST'])]
    public function add(EntityManagerInterface $entityManager, Request $request): Response {

        if (!$this->isGranted('ROLE_RESPONSABLE_ECODIM')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }

        $user = $this->getUser();
    
        $classecodim =  new Classecodim();
        $form = $this->createForm(ClassecodimType::class, $classecodim,);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

          
                $classecodim->setCreatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setEglise($user->getEglise())
                        ->setCreatedBy($user)
                ;
    
        

            $entityManager->persist($classecodim);
            $entityManager->flush();

            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'classecodim_add' : 'classecodim';
            if ($nextAction) {
                $this->addFlash('success', 'Enregistrement effectué avec succès.');
            }

            return $this->redirectToRoute($nextAction);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('classecodim/add.html.twig', [
                    'classecodim' => $classecodim,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    #[Route('classecodim/print', name: 'classecodim_print', methods: ['GET', 'POST'])]
    public function printclassecodim(ClassecodimRepository $classecodimRepository): Response {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        $classecodim = $classecodimRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('classecodim/print.html.twig', [
            'classecodim' => $classecodim,
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

   
    #[Route('/update/datatable/{id}', name: 'classecodim_update_datatable', methods: ['POST'])]
    public function classecodimUpdateDatatable(EntityManagerInterface $entityManager, Request $request, ?Classecodim $classecodim = null): JsonResponse {

        $return = [
            'update' => false,
            'notification' => false,
        ];
        $new_classecodim = \strip_tags($request->request->get('classecodim'));

        // Si l'entité existe et que le nouveau nom de la classecodim apre le strip_tags comporte plus que 0 caractères
        if ($classecodim && strlen($new_classecodim) > 0) {
            // strip_tags pour enlever tout code html
            // évite d'envoyer des balise <script>
            // ref: https://www.php.net/manual/fr/function.strip-tags.php
            $classecodim->setNom($new_classecodim);
            $user = $this->getUser();
            $classecodim->setUpdatedBy($user)
                    ->setUpdatedFromIp($this->GetIp());
            $entityManager->persist($classecodim);
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

    #[Route('/add1', name: 'classecodim_add1', methods: ['GET', 'POST'])]
    public function newClassecodim(Request $request, EntityManagerInterface $entityManager): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $classecodim = new Classecodim();
        $form = $this->createForm(ClassecodimType::class, $classecodim);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $classecodim->setCreatedFromIp($this->GetIp());
            $user = $this->getUser();
            $eglise = $this->getUser()->getEglise();
            $classecodim->setCreatedBy($user);
            $classecodim->setEglise($eglise);
            $entityManager->persist($classecodim);
            $entityManager->flush();
            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'classecodim_add1' : 'classecodim';
            if ($nextAction) {
                $this->addFlash('success', 'Enregistrement effectué avec succès.');
            }

            return $this->redirectToRoute($nextAction);
        }

        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('classecodim/add1.html.twig', [
                    'classecodim' => $classecodim,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    
    
        
        #[Route('/{id}/update', name: 'classecodim_update', methods: ['GET', 'POST'])]
    public function updateClassecodim(Request $request, Classecodim $classecodim): Response {
        $data = json_decode($request->getContent(), true);
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $user = $this->getUser();
        $form = $this->createForm(ClassecodimType::class, $classecodim);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //Adresse ip de l'utilisateur
        $user = $this->getUser();

            $classecodim->setUpdatedFromIp($this->GetIp());
            $classecodim->setUpdatedBy($user);
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Modification effectuée avec succès');
      
            return $this->redirectToRoute('classecodim');
        }
        return $this->render('classecodim/update.html.twig', [
                    'classecodim' => $classecodim,
                    'form' => $form->createView(),
                        ]);
    }
    
    #[Route('ecodim/{id}', name: 'classecodim_delete', methods: ['POST'])]
    public function delete(Request $request, Classecodim $classecodim, ClassecodimRepository $classecodimRepository): Response {
        if ($this->isCsrfTokenValid('delete' . $classecodim->getId(), $request->request->get('_token'))) {
            if (!$this->isGranted('ROLE_SECRETAIRE')) {
                throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
            }
            $entityManager = $this->getDoctrine()->getManager();

            $classecodim->setDeletedFromIp($this->GetIp());
            $classecodim->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $classecodim->setDeletedBy($user);
            $this->addFlash('danger', 'Suppression avec succès.');

            $entityManager->flush();
        }

        return $this->redirectToRoute('classecodim');
    }

}
