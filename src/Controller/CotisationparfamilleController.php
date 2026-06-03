<?php

namespace App\Controller;

use App\Entity\Cotisationparfamille;
use App\Form\CotisationparfamilleType;
use App\Repository\CotisationparfamilleRepository;
use App\Repository\CotiserparfamilleRepository;
use App\Traits\ClientIp;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cotisationparfamille')]
class CotisationparfamilleController extends AbstractController {

    use ClientIp;

    #[Route('/', name: 'app_cotisationparfamille_index', methods: ['GET'])]
    public function index(CotisationparfamilleRepository $cotisationparfamilleRepository): Response {
        if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $cotisationparfamille = $cotisationparfamilleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        return $this->render('cotisationparfamille/index.html.twig', [
                    'cotisationparfamilles' => $cotisationparfamille,
        ]);
    }

    #[Route('/new', name: 'app_cotisationparfamille_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CotisationparfamilleRepository $cotisationparfamilleRepository): Response {
        if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $cotisationparfamille = new Cotisationparfamille();
        $form = $this->createForm(CotisationparfamilleType::class, $cotisationparfamille);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //Adresse ip de l'utilisateur

            $cotisationparfamille->setCreatedFromIp($this->GetIp());
            $user = $this->getUser();
            $eglise = $this->getUser()->getEglise();
            $cotisationparfamille->setCreatedBy($user);
            $cotisationparfamille->setEglise($eglise);
            $cotisationparfamille->setEtatcotiser(1);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($cotisationparfamille);
            $entityManager->flush();
            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'app_cotisationparfamille_new' : 'app_cotisationparfamille_index';
            if ($nextAction) {
                $this->addFlash('succesfamille', 'Enregistrement avec succès.');
            }

            return $this->redirectToRoute($nextAction);
//            return $this->redirectToRoute('cotisationparfamille_index', [], Response::HTTP_SEE_OTHER);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('cotisationparfamille/new.html.twig', [
                    'cotisationparfamille' => $cotisationparfamille,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    #[Route('/{id}', name: 'app_cotisationparfamille_show', methods: ['GET'])]
    public function show(Cotisationparfamille $cotisationparfamille): Response {
        if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        return $this->render('cotisationparfamille/show.html.twig', [
                    'cotisationparfamille' => $cotisationparfamille,
        ]);
    }

    #[Route('/cotiser/{id}', name: 'cotisationparfamille_cotiser', methods: ['GET'])]
    public function detailCotisationparfamille(Request $request, CotiserparfamilleRepository $cotiserfamilleRepository, CotisationparfamilleRepository $cotisationparfamilleRepo) {
        //Recuperation id cotisationparfamille
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_FINANCE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $idcotisationparfamille = $request->query->get('id');
        //Recuperation de la liste des fidele par cotisation famille
        $listeCotiserparfamille = $cotiserfamilleRepository->findBy(['cotisationparfamille' => $idcotisationparfamille, 'deletedAt' => NULL]);
        $ligneCotisationparfamille = $cotisationparfamilleRepo->find($idcotisationparfamille);
        $nomCotisationparfamille = $ligneCotisationparfamille->getObjet();
        return $this->render('cotisationparfamille/detail.html.twig', [
                    'cotiserparfamilles' => $listeCotiserparfamille,
                    'id' => $idcotisationparfamille,
                    'nomcotisationparfamille' => $nomCotisationparfamille,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_cotisationparfamille_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Cotisationparfamille $cotisationparfamille, CotisationparfamilleRepository $cotisationparfamilleRepository): Response {
        if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $form = $this->createForm(CotisationparfamilleType::class, $cotisationparfamille);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $cotisationparfamille->setUpdatedFromIp($this->GetIp());
            $user = $this->getUser();
            $cotisationparfamille->setUpdatedBy($user);

            $cotisationparfamilleRepository->add($cotisationparfamille);
            return $this->redirectToRoute('app_cotisationparfamille_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('cotisationparfamille/edit.html.twig', [
                    'cotisationparfamille' => $cotisationparfamille,
                    'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_cotisationparfamille_delete', methods: ['POST'])]
    public function delete(Request $request, Cotisationparfamille $cotisationparfamille, CotisationparfamilleRepository $cotisationparfamilleRepository): Response {
        if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($this->isCsrfTokenValid('delete' . $cotisationparfamille->getId(), $request->request->get('_token'))) {
            $cotisationparfamilleRepository->remove($cotisationparfamille, true);
        }
        $this->addFlash('suppcotisationparfamille', 'Supression avec succès');
        return $this->redirectToRoute('app_cotisationparfamille_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/cotisationparfamille/', name: 'cotisationparfamille_cloture', methods: ['POST'])]
    public function clotureCotyisation(Request $request, Cotisationparfamille $cotisationparfamille): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($this->isCsrfTokenValid('cloturecotisationparfam' . $cotisationparfamille->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();
            $user = $this->getUser();
            $cotisationparfamille->setUpdatedFromIp($this->GetIp()) // remplacement de la function par le trait
                    ->setUpdatedBy($user)
            ;

            $cotisationparfamille->setEtatcotiser("0");

            $this->addFlash('cloturecotisationparfam', 'Cotisation cloturée avec succès.');
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_cotisationparfamille_index');
    }

    #[Route('/{id}/cotisationparfamille', name: 'cotisationparfamille_active', methods: ['POST'])]
    public function activeCotisation(Request $request, Cotisationparfamille $cotisationparfamille): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($this->isCsrfTokenValid('activecotisationparfam' . $cotisationparfamille->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();

            $user = $this->getUser();
            $cotisationparfamille->setUpdatedFromIp($this->GetIp()) // remplacement de la function par le trait
                    ->setUpdatedBy($user)
            ;
            $cotisationparfamille->setEtatcotiser("1");

            $this->addFlash('activecotisationparfam', 'Cotisation réactivée avec succès.');
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_cotisationparfamille_index');
    }

}
