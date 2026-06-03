<?php

namespace App\Controller;

use App\Entity\Ame;
use App\Form\AmeType;
use App\Repository\AmeRepository;
use App\Repository\EvangelisationRepository;
use App\Traits\ClientIp;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use DateTime;

#[Route('/ame')]
class AmeController extends AbstractController {

    use ClientIp;

    #[Route('/', name: 'app_ame_index', methods: ['GET'])]
    public function index(AmeRepository $ameRepository): Response {
        if (!$this->isGranted('ROLE_RESPONSABLE_EVANGELISATION')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $ame = $ameRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        return $this->render('ame/index.html.twig', [
                    'ames' => $ame,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_ame_edit', methods: ['GET', 'POST'])]
    #[Route('/new', name: 'app_ame_new', methods: ['GET', 'POST'])]
    public function new(EntityManagerInterface $entityManager, Request $request, EvangelisationRepository $evangelisationRepository, ?Ame $ame = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_EVANGELISATION')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }

        $user = $this->getUser();
        $type = $ame === null ? 'new' : 'edit';
        $ame = $ame === null ? new Ame() : $ame;
        $eglise = $this->getUser()->getEglise();
        $evangelisation = $evangelisationRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $form = $this->createForm(AmeType::class, $ame, ['evangelisation' => $evangelisation,]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {


            if ($type === 'new') {
                $ame->setCreatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setEglise($user->getEglise())
                        ->setCreatedBy($user)
                ;
                $this->addFlash('success', 'Enregistrement avec succès.');
            } else {
                $ame->setUpdatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setUpdatedBy($user)
                ;
                $this->addFlash('success', 'Modification effectuée avec succès.');
            }

            $entityManager->persist($ame);
            $entityManager->flush();
            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'app_ame_new' : 'app_ame_index';

            return $this->redirectToRoute($nextAction);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);

        return $this->render('ame/new.html.twig', [
                    'ame' => $ame,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    #[Route('/{id}/show', name: 'ame_show', methods: ['GET', 'POST'])]
    public function show(Ame $ame): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        return $this->render('ame/show.html.twig', [
                    'ame' => $ame,
        ]);
    }

    #[Route('ame/{id}', name: 'app_ame_delete', methods: ['POST'])]
    public function delete(Request $request, Ame $ame, AmeRepository $ameRepository): Response {
        if ($this->isCsrfTokenValid('delete' . $ame->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();

            if (!$this->isGranted('ROLE_RESPONSABLE_EVANGELISATION')) {
                throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
            }


            $ame->setDeletedFromIp($this->GetIp());
            $ame->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $ame->setDeletedBy($user);
            $this->addFlash('danger', 'Suppression avec succès.');

            $entityManager->flush();
        }

        return $this->redirectToRoute('app_ame_index');
    }

}
