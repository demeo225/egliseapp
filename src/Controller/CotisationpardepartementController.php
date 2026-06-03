<?php

namespace App\Controller;

use App\Entity\Cotisationpardepartement;
use App\Form\CotisationpardepartementType;
use App\Repository\CotisationpardepartementRepository;
use App\Repository\CotiserpardepartementRepository;
use App\Traits\ClientIp;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cotisationpardepartement')]
class CotisationpardepartementController extends AbstractController {

    use ClientIp;

    #[Route('/', name: 'app_cotisationpardepartement_index', methods: ['GET'])]
    public function index(CotisationpardepartementRepository $cotisationpardepartementRepository): Response {
        if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $cotisationpardepartement = $cotisationpardepartementRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        return $this->render('cotisationpardepartement/index.html.twig', [
                    'cotisationpardepartements' => $cotisationpardepartement,
        ]);
    }

    #[Route('/new', name: 'app_cotisationpardepartement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CotisationpardepartementRepository $cotisationpardepartementRepository): Response {
        if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $cotisationpardepartement = new Cotisationpardepartement();
        $form = $this->createForm(CotisationpardepartementType::class, $cotisationpardepartement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $cotisationpardepartement->setCreatedFromIp($this->GetIp());
            $user = $this->getUser();
            $eglise = $this->getUser()->getEglise();
            $cotisationpardepartement->setCreatedBy($user);
            $cotisationpardepartement->setEglise($eglise);
            $cotisationpardepartement->setEtatcotiser(1);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($cotisationpardepartement);
            $entityManager->flush();
            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'app_cotisationpardepartement_new' : 'app_cotisationpardepartement_index';
            if ($nextAction) {
                $this->addFlash('success', 'Enregistrement avec succès.');
            }

            return $this->redirectToRoute($nextAction);
//            return $this->redirectToRoute('cotisationpardepartement_index', [], Response::HTTP_SEE_OTHER);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('cotisationpardepartement/new.html.twig', [
                    'cotisationpardepartement' => $cotisationpardepartement,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    #[Route('/{id}', name: 'app_cotisationpardepartement_show', methods: ['GET'])]
    public function show(Cotisationpardepartement $cotisationpardepartement): Response {
        if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        return $this->render('cotisationpardepartement/show.html.twig', [
                    'cotisationpardepartement' => $cotisationpardepartement,
        ]);
    }

    #[Route('/cotiser/{id}', name: 'cotisationpardepartement_cotiser', methods: ['GET'])]
    public function detailCotisationpardepartement(Request $request, CotiserpardepartementRepository $cotiserdepartementRepository, CotisationpardepartementRepository $cotisationpardepartementRepo) {
        //Recuperation id cotisationpardepartement
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_FINANCE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $idcotisationpardepartement = $request->query->get('id');
        //Recuperation de la liste des fidele par cotisation departement
        $listeCotiserpardepartement = $cotiserdepartementRepository->findBy(['cotisationpardepartement' => $idcotisationpardepartement, 'deletedAt' => NULL]);
        $ligneCotisationpardepartement = $cotisationpardepartementRepo->find($idcotisationpardepartement);
        $nomCotisationpardepartement = $ligneCotisationpardepartement->getObjet();
        return $this->render('cotisationpardepartement/detail.html.twig', [
                    'cotiserpardepartements' => $listeCotiserpardepartement,
                    'id' => $idcotisationpardepartement,
                    'nomcotisationpardepartement' => $nomCotisationpardepartement,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_cotisationpardepartement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Cotisationpardepartement $cotisationpardepartement, CotisationpardepartementRepository $cotisationpardepartementRepository): Response {
        if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $form = $this->createForm(CotisationpardepartementType::class, $cotisationpardepartement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $cotisationpardepartement->setUpdatedFromIp($this->GetIp());
            $user = $this->getUser();
            $cotisationpardepartement->setUpdatedBy($user);

            $cotisationpardepartementRepository->add($cotisationpardepartement);
            return $this->redirectToRoute('app_cotisationpardepartement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('cotisationpardepartement/edit.html.twig', [
                    'cotisationpardepartement' => $cotisationpardepartement,
                    'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_cotisationpardepartement_delete', methods: ['POST'])]
    public function delete(Request $request, Cotisationpardepartement $cotisationpardepartement, CotisationpardepartementRepository $cotisationpardepartementRepository): Response {
        if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($this->isCsrfTokenValid('delete' . $cotisationpardepartement->getId(), $request->request->get('_token'))) {
            $cotisationpardepartementRepository->remove($cotisationpardepartement, true);
        }
        $this->addFlash('suppcotisationpardepartement', 'Supression avec succès');
        return $this->redirectToRoute('app_cotisationpardepartement_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/cotisationpardepartement/', name: 'cotisationpardepartement_cloture', methods: ['POST'])]
    public function clotureCotyisation(Request $request, Cotisationpardepartement $cotisationpardepartement): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($this->isCsrfTokenValid('cloturecotisationpardep' . $cotisationpardepartement->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();
            $user = $this->getUser();
            $cotisationpardepartement->setUpdatedFromIp($this->GetIp()) // remplacement de la function par le trait
                    ->setUpdatedBy($user)
            ;

            $cotisationpardepartement->setEtatcotiser(0);

            $this->addFlash('danger', 'Cotisation cloturée avec succès.');
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_cotisationpardepartement_index');
    }

    #[Route('/{id}/cotisationpardepartement', name: 'cotisationpardepartement_active', methods: ['POST'])]
    public function activeCotisation(Request $request, Cotisationpardepartement $cotisationpardepartement): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($this->isCsrfTokenValid('activecotisationpardep' . $cotisationpardepartement->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();

            $user = $this->getUser();
            $cotisationpardepartement->setUpdatedFromIp($this->GetIp()) // remplacement de la function par le trait
                    ->setUpdatedBy($user)
            ;
            $cotisationpardepartement->setEtatcotiser(1);

            $this->addFlash('success', 'Cotisation réactivée avec succès.');
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_cotisationpardepartement_index');
    }

}
