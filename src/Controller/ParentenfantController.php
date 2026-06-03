<?php

namespace App\Controller;

use App\Entity\Parentenfant;
use App\Form\ParentenfantType;
use App\Repository\ParentenfantRepository;
use App\Repository\EnfantRepository;
use App\Repository\FideleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use DateTime;
use App\Traits\ClientIp;


#[Route('/parentenfant')]

class ParentenfantController extends AbstractController {
    use ClientIp;
    
    #[Route('/', name: 'parentenfant_index', methods: ['GET'])]

    public function index(ParentenfantRepository $parentenfantRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
            if (!$this->isGranted('ROLE_RESPONSABLE_CONJUGAL')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $parentenfant = $parentenfantRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        return $this->render('parentenfant/index.html.twig', [
                    'parentenfant' => $parentenfant,
        ]);
    }

    #[Route('/new', name: 'parentenfant_new', methods: ['GET', 'POST'])]

    public function new(Request $request, ParentenfantRepository $parentenfantRepository, FideleRepository $fideleRepository, EnfantRepository $enfantRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    if (!$this->isGranted('ROLE_RESPONSABLE_CONJUGAL')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $parentenfant = new Parentenfant();
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $fidele = $fideleRepository->findByEglise($eglise);
        $enfant = $enfantRepository->findByEglise($eglise);
        $form = $this->createForm(ParentenfantType::class, $parentenfant, ['fidele' => $fidele, 'enfant' => $enfant]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $parentenfant->setCreatedFromIp($this->GetIp());
            $parentenfant = $form->getData();

            $dql = $parentenfantRepository->findBy(['fidele' => $parentenfant->getFidele(),
                'enfant' => $parentenfant->getEnfant()
            ]);

            if ($dql) {
                $this->addFlash('success', 'Lien de paternité déjà établi.');
                return $this->redirectToRoute('parentenfant_new', [], Response::HTTP_SEE_OTHER);
            } else {
                $eglise = $this->getUser()->getEglise();
                $user = $this->getUser();
                $parentenfant->setCreatedBy($user);
                $parentenfant->setEglise($eglise);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($parentenfant);
                $entityManager->flush();

                return $this->redirectToRoute('parentenfant_index', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('parentenfant/new.html.twig', [
                    'parentenfant' => $parentenfant,
                    'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'parentenfant_show', methods: ['GET'])]

    public function show(Parentenfant $parentenfant): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    if (!$this->isGranted('ROLE_RESPONSABLE_CONJUGAL')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        return $this->render('parentenfant/show.html.twig', [
                    'parentenfant' => $parentenfant,
        ]);
    }

    #[Route('/{id}/edit', name: 'parentenfant_edit', methods: ['GET', 'POST'])]

    public function edit(Request $request, Parentenfant $parentenfant): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    if (!$this->isGranted('ROLE_RESPONSABLE_CONJUGAL')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $form = $this->createForm(ParentenfantType::class, $parentenfant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $parentenfant->setUpdatedFromIp($this->GetIp());
            $user = $this->getUser();
            $parentenfant->setUpdatedBy($user);
             $this->addFlash('success', 'Modification effectuée avec succès.');
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('parentenfant_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('parentenfant/edit.html.twig', [
                    'parentenfant' => $parentenfant,
                    'form' => $form->createView(),
        ]);
    }


        #[Route('/{id}', name: 'parentenfant_delete', methods: ['POST'])]
    public function delete(Request $request, Parentenfant $parentenfant): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
            if (!$this->isGranted('ROLE_RESPONSABLE_CONJUGAL')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($this->isCsrfTokenValid('delete' . $parentenfant->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();

            $parentenfant->setDeletedFromIp($this->GetIp());
            $parentenfant->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $parentenfant->setDeletedBy($user);
             $this->addFlash('danger', 'Suppression effectuée avec succès.');
            $entityManager->flush();
        }

        return $this->redirectToRoute('parentenfant_index');
    }

}
