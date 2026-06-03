<?php

namespace App\Controller;

use App\Entity\Cotisationparcellule;
use App\Form\CotisationparcelluleType;
use App\Repository\CotisationparcelluleRepository;
use App\Repository\CotiserparcelluleRepository;
use App\Traits\ClientIp;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cotisationparcellule')]
class CotisationparcelluleController extends AbstractController {
    use ClientIp;
    
    #[Route('/', name: 'app_cotisationparcellule_index', methods: ['GET'])]
    public function index(CotisationparcelluleRepository $cotisationparcelluleRepository): Response {
        if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $cotisationparcellule = $cotisationparcelluleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        return $this->render('cotisationparcellule/index.html.twig', [
                    'cotisationparcellules' => $cotisationparcellule,
        ]);
    }

    #[Route('/new', name: 'app_cotisationparcellule_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CotisationparcelluleRepository $cotisationparcelluleRepository): Response {
        if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $cotisationparcellule = new Cotisationparcellule();
        $form = $this->createForm(CotisationparcelluleType::class, $cotisationparcellule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

 
            $cotisationparcellule->setCreatedFromIp($this->GetIp());
            $user = $this->getUser();
            $eglise = $this->getUser()->getEglise();
            $cotisationparcellule->setCreatedBy($user);
            $cotisationparcellule->setEglise($eglise);
            $cotisationparcellule->setEtatcotiser(1);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($cotisationparcellule);
            $entityManager->flush();
            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'app_cotisationparcellule_new' : 'app_cotisationparcellule_index';
            if ($nextAction) {
                $this->addFlash('succescellule', 'Enregistrement avec succès.');
            }

            return $this->redirectToRoute($nextAction);
//            return $this->redirectToRoute('cotisationparcellule_index', [], Response::HTTP_SEE_OTHER);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('cotisationparcellule/new.html.twig', [
                    'cotisationparcellule' => $cotisationparcellule,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    #[Route('/{id}', name: 'app_cotisationparcellule_show', methods: ['GET'])]
    public function show(Cotisationparcellule $cotisationparcellule): Response {
            if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        return $this->render('cotisationparcellule/show.html.twig', [
                    'cotisationparcellule' => $cotisationparcellule,
        ]);
    }

    
    
            #[Route('/cotiser/{id}', name: 'cotisationparcellule_cotiser', methods: ['GET'])]
    public function detailCotisationparcellule(Request $request, CotiserparcelluleRepository $cotisercelluleRepository, CotisationparcelluleRepository $cotisationparcelluleRepo) {
        //Recuperation id cotisationparcellule
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_FINANCE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $idcotisationparcellule = $request->query->get('id');
        //Recuperation de la liste des fidele par cotisation cellule
        $listeCotiserparcellule = $cotisercelluleRepository->findBy(['cotisationparcellule' => $idcotisationparcellule, 'deletedAt' => NULL]);
        $ligneCotisationparcellule = $cotisationparcelluleRepo->find($idcotisationparcellule);
        $nomCotisationparcellule = $ligneCotisationparcellule->getObjet();
        return $this->render('cotisationparcellule/detail.html.twig', [
                    'cotiserparcellules' => $listeCotiserparcellule,
                    'id' => $idcotisationparcellule,
                    'nomcotisationparcellule' => $nomCotisationparcellule,
        ]);
    }
    
    #[Route('/{id}/edit', name: 'app_cotisationparcellule_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Cotisationparcellule $cotisationparcellule, CotisationparcelluleRepository $cotisationparcelluleRepository): Response {
        if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $form = $this->createForm(CotisationparcelluleType::class, $cotisationparcellule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

 
            $cotisationparcellule->setUpdatedFromIp($this->GetIp());
            $user = $this->getUser();
            $cotisationparcellule->setUpdatedBy($user);

            $cotisationparcelluleRepository->add($cotisationparcellule);
            return $this->redirectToRoute('app_cotisationparcellule_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('cotisationparcellule/edit.html.twig', [
                    'cotisationparcellule' => $cotisationparcellule,
                    'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_cotisationparcellule_delete', methods: ['POST'])]
    public function delete(Request $request, Cotisationparcellule $cotisationparcellule, CotisationparcelluleRepository $cotisationparcelluleRepository): Response {
           if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($this->isCsrfTokenValid('delete' . $cotisationparcellule->getId(), $request->request->get('_token'))) {
            $cotisationparcelluleRepository->remove($cotisationparcellule, true);
        }
        $this->addFlash('suppcotisationparcellule', 'Supression avec succès');
        return $this->redirectToRoute('app_cotisationparcellule_index', [], Response::HTTP_SEE_OTHER);
    }

    
        #[Route('/{id}/cotisationparcellule/', name: 'cotisationparcellule_cloture', methods: ['POST'])]
    public function clotureCotyisation(Request $request, Cotisationparcellule $cotisationparcellule): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($this->isCsrfTokenValid('cloturecotisationparcel' . $cotisationparcellule->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();
            $user = $this->getUser();
            $cotisationparcellule->setUpdatedFromIp($this->GetIp()) // remplacement de la function par le trait
                    ->setUpdatedBy($user)
            ;

            $cotisationparcellule->setEtatcotiser("0");

            $this->addFlash('cloturecotisationparcel', 'Cotisation cloturée avec succès.');
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_cotisationparcellule_index');
    }

    #[Route('/{id}/cotisationparcellule', name: 'cotisationparcellule_active', methods: ['POST'])]
    public function activeCotisation(Request $request, Cotisationparcellule $cotisationparcellule): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($this->isCsrfTokenValid('activecotisationparcel' . $cotisationparcellule->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();

            $user = $this->getUser();
            $cotisationparcellule->setUpdatedFromIp($this->GetIp()) // remplacement de la function par le trait
                    ->setUpdatedBy($user)
            ;
            $cotisationparcellule->setEtatcotiser("1");

            $this->addFlash('activecotisationparcel', 'Cotisation réactivée avec succès.');
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_cotisationparcellule_index');
    }
}
