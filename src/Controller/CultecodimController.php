<?php

namespace App\Controller;

use App\Entity\Cultecodim;
use App\Entity\Presenceculteecodim;
use App\Form\CultecodimType;
use App\Repository\ClassecodimRepository;
use App\Repository\CultecodimRepository;
use App\Repository\EnfantRepository;
use App\Repository\PresenceculteecodimRepository;
use App\Repository\PresenceculteRepository;
use DateTime;
use App\Traits\ClientIp;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cultecodim')]
class CultecodimController extends AbstractController {

    use ClientIp;

    #[Route('/', name: 'cultecodim')]
    public function index(CultecodimRepository $cultecodimRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_ECODIM')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $cultecodim = $cultecodimRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $difference = $cultecodimRepository->findCultecodimsByDates();
        return $this->render('cultecodim/index.html.twig', [
                    'cultecodims' => $cultecodim,
                    'differences' => $difference,
                    'eglise' => $eglise,
        ]);
    }

    #[Route('/{id}/detail', name: 'cultecodim_detail', methods: ['GET', 'POST'])]
    public function detail(Cultecodim $cultecodim): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_ECODIM')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        return $this->render('cultecodim/detail.html.twig', [
                    'cultecodim' => $cultecodim,
        ]);
    }

    #[Route('/{id}/update', name: 'cultecodim_update', methods: ['GET', 'POST'])]
    #[Route('/add', name: 'cultecodim_add', methods: ['GET', 'POST'])]
    public function add(EntityManagerInterface $entityManager, Request $request, ClassecodimRepository $classecodimRepository, ?Cultecodim $cultecodim = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_ECODIM')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $user = $this->getUser();
        $type = $cultecodim === null ? 'add' : 'update';
        $cultecodim = $cultecodim === null ? new Cultecodim() : $cultecodim;
        $eglise = $this->getUser()->getEglise();
        $classecodim = $classecodimRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $form = $this->createForm(CultecodimType::class, $cultecodim, ['classecodim' => $classecodim]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

         
           if ($type === 'add') {
                $cultecodim->setCreatedFromIp($this->GetIp()) // remplacement de la function par le trait
                    ->setEglise($user->getEglise())
                    ->setCreatedBy($user)
                ;
            } else {
                $cultecodim->setUpdatedFromIp($this->GetIp()) // remplacement de la function par le trait
                    ->setUpdatedBy($user)
                ;
            }
            

            $cultecodim = $form->getData();
            $entityManager->persist($cultecodim);
            $entityManager->flush();
            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'cultecodim_add' : 'cultecodim';
            if ($nextAction) {
                $this->addFlash('culteecodim', 'Enregistrement avec succès.');
            }
            return $this->redirectToRoute($nextAction);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('cultecodim/add.html.twig', [
                    'cultecodim' => $cultecodim,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }
    
    
    
    
//     #[Route('/{id}/update', name: 'cultecodim_update', methods: ['GET', 'POST'])]
//   // #[Route('/add', name: 'cultecodim_add', methods: ['GET', 'POST'])]
//    public function updateCulte(EntityManagerInterface $entityManager, Request $request,Cultecodim $cultecodim, ClassecodimRepository $classecodimRepository): Response {
//        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
//        if (!$this->isGranted('ROLE_RESPONSABLE_ECODIM')) {
//            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
//        }
//        $user = $this->getUser();
//
//        $eglise = $this->getUser()->getEglise();
//        $classecodim = $classecodimRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
//        $form = $this->createForm(CultecodimType::class, $cultecodim, ['classecodim' => $classecodim]);
//        $form->handleRequest($request);
//        if ($form->isSubmitted() && $form->isValid()) {
//
//  
//                $cultecodim->setUpdatedFromIp($this->GetIp()) // remplacement de la function par le trait
//                    ->setUpdatedBy($user)
//                ;
//      
//            
//
//            $cultecodim = $form->getData();
//            $entityManager->persist($cultecodim);
//            $entityManager->flush();
//          
//                $this->addFlash('modifcultecodim', 'Modification effectuée avec succès.');
//            
//            return $this->redirectToRoute('cultecodim');
//        }
//        return $this->render('cultecodim/update.html.twig', [
//                    'cultecodim' => $cultecodim,
//                    'form' => $form->createView(),
//                        ]);
//    }
//    
      //  #[Route('/{id}/update', name: 'cultecodim_update', methods: ['GET', 'POST'])]
    #[Route('/add1', name: 'cultecodim_add1', methods: ['GET', 'POST'])]
    public function addEcodim(EntityManagerInterface $entityManager, Request $request, ClassecodimRepository $classecodimRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_ECODIM')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $user = $this->getUser();
     
        $cultecodim =  new Cultecodim();
        $eglise = $this->getUser()->getEglise();
        $classecodim = $classecodimRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $form = $this->createForm(CultecodimType::class, $cultecodim, ['classecodim' => $classecodim]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

         
        
                $cultecodim->setCreatedFromIp($this->GetIp()) // remplacement de la function par le trait
                    ->setEglise($user->getEglise())
                    ->setCreatedBy($user)
                ;
     

            $cultecodim = $form->getData();
            $entityManager->persist($cultecodim);
            $entityManager->flush();
          
                $this->addFlash('culteecodim', 'Action effectuée avec succès.');
            
            return $this->redirectToRoute('cultecodim');
        }
        return $this->render('cultecodim/add1.html.twig', [
                    'cultecodim' => $cultecodim,
                    'form' => $form->createView(),
                    
                        ]);
    }

    #[Route('/print', name: 'cultecodim_print', methods: ['GET', 'POST'])]
    public function printcultecodim(CultecodimRepository $cultecodimRepository): Response {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_ECODIM')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        $cultecodim = $cultecodimRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('cultecodim/print.html.twig', [
            'cultecodim' => $cultecodim,
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

    #[Route('cultecodim/{id}', name: 'cultecodim_delete', methods: ['POST'])]
    public function delete(Request $request, Cultecodim $cultecodim): Response {
        if ($this->isCsrfTokenValid('delete' . $cultecodim->getId(), $request->request->get('_token'))) {
            if (!$this->isGranted('ROLE_RESPONSABLE_ECODIM')) {
                throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
            }
            $entityManager = $this->getDoctrine()->getManager();


            $cultecodim->setDeletedFromIp($this->GetIp());
            $cultecodim->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $cultecodim->setDeletedBy($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('cultecodim');
    }

}
