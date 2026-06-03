<?php

namespace App\Controller;

use App\Entity\Deces;
use App\Entity\Fidele;
use App\Form\DecesType;
use App\Repository\DecesRepository;
use App\Repository\FideleRepository;
use DateTime;
use App\Traits\ClientIp;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/deces')]

class DecesController extends AbstractController {
    use ClientIp;
    
    #[Route('/', name: 'deces_index', methods: ['GET'])]

    public function index(DecesRepository $decesRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }

        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $roles = $user->getRoles();
        $deces = $decesRepository->findBy(['eglise' => $eglise, 'deletedAt' => NULL]);
        return $this->render('deces/index.html.twig', [
                    'deces' => $deces,
        ]);
    }

    #[Route('/new', name: 'deces_new', methods: ['GET','POST'])]

    public function new(Request $request, EntityManagerInterface $entityManager, FideleRepository $fideleRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }

        $dece = new Deces();
        $eglise = $this->getUser()->getEglise()->getId();
        $user = $this->getUser();

        $fidele = $fideleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL, 'etatfidele' => 1]);

        $form = $this->createForm(DecesType::class, $dece, ['fidele' => $fidele]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $id = $form['fidele']->getData()->getId();
            $eglise = $this->getUser()->getEglise();
            $user = $this->getUser();
            $fideleM = $fideleRepository->findOneByFidele($id);
            $fideleM->setEtatfidele("0");
            $fideleM->setEditable("0");
            $fideleM->setDeletedFromIp($this->GetIp());
            $fideleM->setDeletedBy($user);
            $fideleM->setDeletedAt(new DateTime("now"));

            $dece->setCreatedBy($user);
            $dece->setEglise($eglise);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush($fideleM);
            $entityManager->persist($dece);
            $entityManager->flush();

            return $this->redirectToRoute('deces_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('deces/new.html.twig', [
                    'dece' => $dece,
                    'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'deces_show', methods: ['GET'])]

    public function show(Deces $dece): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        return $this->render('deces/show.html.twig', [
                    'dece' => $dece,
        ]);
    }

    #[Route('/{id}/edit', name: 'deces_edit', methods: ['GET','POST'])]

    public function edit(Request $request, Deces $dece, EntityManagerInterface $entityManager, FideleRepository $fideleRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $form = $this->createForm(DecesType::class, $dece,);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $dece->setUpdatedFromIp($this->GetIp());
            $user = $this->getUser();
            $dece->setUpdatedBy($user);



            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('deces_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('deces/edit.html.twig', [
                    'dece' => $dece,
                    'form' => $form->createView(),
        ]);
    }

    #[Route('deces/{id}', name: 'deces_delete', methods: ['POST'])]

    public function delete(Request $request, Deces $dece): Response {
        if ($this->isCsrfTokenValid('delete' . $dece->getId(), $request->request->get('_token'))) {
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
            if (!$this->isGranted('ROLE_SECRETAIRE')) {
                throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($dece);
            $entityManager->flush();
        }

        return $this->redirectToRoute('deces_index', [], Response::HTTP_SEE_OTHER);
    }

    public function editFidele($form, $rpFidele, $id) {
        $fidele = $rpFidele->findOneByFidele($id);
        $fidele->setEditable("0");

        return $fidele;
    }

}
