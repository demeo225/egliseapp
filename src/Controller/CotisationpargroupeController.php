<?php

namespace App\Controller;

use App\Entity\Cotisationpargroupe;
use App\Form\CotisationpargroupeType;
use App\Repository\CotisationpargroupeRepository;
use App\Repository\CotiserpargroupeRepository;
use App\Traits\ClientIp;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cotisationpargroupe')]
class CotisationpargroupeController extends AbstractController {

    use ClientIp;

    #[Route('/', name: 'app_cotisationpargroupe_index', methods: ['GET'])]
    public function index(CotisationpargroupeRepository $cotisationpargroupeRepository): Response {
        if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $cotisationpargroupe = $cotisationpargroupeRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        return $this->render('cotisationpargroupe/index.html.twig', [
                    'cotisationpargroupes' => $cotisationpargroupe,
        ]);
    }

    #[Route('/new', name: 'app_cotisationpargroupe_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CotisationpargroupeRepository $cotisationpargroupeRepository): Response {
        if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $cotisationpargroupe = new Cotisationpargroupe();
        $form = $this->createForm(CotisationpargroupeType::class, $cotisationpargroupe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $cotisationpargroupe->setCreatedFromIp($this->GetIp());
            $user = $this->getUser();
            $eglise = $this->getUser()->getEglise();
            $cotisationpargroupe->setCreatedBy($user);
            $cotisationpargroupe->setEglise($eglise);
            $cotisationpargroupe->setEtatcotiser(1);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($cotisationpargroupe);
            $entityManager->flush();
            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'app_cotisationpargroupe_new' : 'app_cotisationpargroupe_index';
            if ($nextAction) {
                $this->addFlash('succesgroupe', 'Enregistrement avec succès.');
            }

            return $this->redirectToRoute($nextAction);
//            return $this->redirectToRoute('cotisationpargroupe_index', [], Response::HTTP_SEE_OTHER);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('cotisationpargroupe/new.html.twig', [
                    'cotisationpargroupe' => $cotisationpargroupe,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    #[Route('/{id}', name: 'app_cotisationpargroupe_show', methods: ['GET'])]
    public function show(Cotisationpargroupe $cotisationpargroupe): Response {
        if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        return $this->render('cotisationpargroupe/show.html.twig', [
                    'cotisationpargroupe' => $cotisationpargroupe,
        ]);
    }

    #[Route('/cotiser/{id}', name: 'cotisationpargroupe_cotiser', methods: ['GET'])]
    public function detailCotisationpargroupe(Request $request, CotiserpargroupeRepository $cotisergroupeRepository, CotisationpargroupeRepository $cotisationpargroupeRepo) {
        //Recuperation id cotisationpargroupe
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_FINANCE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $idcotisationpargroupe = $request->query->get('id');
        //Recuperation de la liste des fidele par cotisation groupe
        $listeCotiserpargroupe = $cotisergroupeRepository->findBy(['cotisationpargroupe' => $idcotisationpargroupe, 'deletedAt' => NULL]);
        $ligneCotisationpargroupe = $cotisationpargroupeRepo->find($idcotisationpargroupe);
        $nomCotisationpargroupe = $ligneCotisationpargroupe->getObjet();
        return $this->render('cotisationpargroupe/detail.html.twig', [
                    'cotiserpargroupes' => $listeCotiserpargroupe,
                    'id' => $idcotisationpargroupe,
                    'nomcotisationpargroupe' => $nomCotisationpargroupe,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_cotisationpargroupe_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Cotisationpargroupe $cotisationpargroupe, CotisationpargroupeRepository $cotisationpargroupeRepository): Response {
        if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $form = $this->createForm(CotisationpargroupeType::class, $cotisationpargroupe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //Adresse ip de l'utilisateur

            $cotisationpargroupe->setUpdatedFromIp($this->GetIp());
            $user = $this->getUser();
            $cotisationpargroupe->setUpdatedBy($user);

            $cotisationpargroupeRepository->add($cotisationpargroupe);
            return $this->redirectToRoute('app_cotisationpargroupe_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('cotisationpargroupe/edit.html.twig', [
                    'cotisationpargroupe' => $cotisationpargroupe,
                    'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_cotisationpargroupe_delete', methods: ['POST'])]
    public function delete(Request $request, Cotisationpargroupe $cotisationpargroupe, CotisationpargroupeRepository $cotisationpargroupeRepository): Response {
        if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($this->isCsrfTokenValid('delete' . $cotisationpargroupe->getId(), $request->request->get('_token'))) {
            $cotisationpargroupeRepository->remove($cotisationpargroupe, true);
        }
        $this->addFlash('suppcotisationpargroupe', 'Supression avec succès');
        return $this->redirectToRoute('app_cotisationpargroupe_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/cotisationpargroupe/', name: 'cotisationpargroupe_cloture', methods: ['POST'])]
    public function clotureCotyisation(Request $request, Cotisationpargroupe $cotisationpargroupe): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($this->isCsrfTokenValid('cloturecotisationpargp' . $cotisationpargroupe->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();
            $user = $this->getUser();
            $cotisationpargroupe->setUpdatedFromIp($this->GetIp()) // remplacement de la function par le trait
                    ->setUpdatedBy($user)
            ;

            $cotisationpargroupe->setEtatcotiser("0");

            $this->addFlash('cloturecotisationpargp', 'Cotisation cloturée avec succès.');
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_cotisationpargroupe_index');
    }

    #[Route('/{id}/cotisationpargroupe', name: 'cotisationpargroupe_active', methods: ['POST'])]
    public function activeCotisation(Request $request, Cotisationpargroupe $cotisationpargroupe): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($this->isCsrfTokenValid('activecotisationpargp' . $cotisationpargroupe->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();

            $user = $this->getUser();
            $cotisationpargroupe->setUpdatedFromIp($this->GetIp()) // remplacement de la function par le trait
                    ->setUpdatedBy($user)
            ;
            $cotisationpargroupe->setEtatcotiser("1");

            $this->addFlash('activecotisationpargp', 'Cotisation réactivée avec succès.');
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_cotisationpargroupe_index');
    }

}
