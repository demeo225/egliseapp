<?php

namespace App\Controller;

use App\Entity\Ecodimactivite;
use App\Form\EcodimactiviteType;
use App\Repository\EcodimactiviteRepository;
use App\Repository\EnfantactiviteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use DateTime;
use App\Traits\ClientIp;

#[Route('/ecodimactivite')]
class EcodimactiviteController extends AbstractController {

    use ClientIp;

    #[Route('/', name: 'ecodimactivite_index', methods: ['GET'])]
    public function index(EcodimactiviteRepository $ecodimactiviteRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_ECODIM')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $ecodimactivite = $ecodimactiviteRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        return $this->render('ecodimactivite/index.html.twig', [
                    'ecodimactivite' => $ecodimactivite,
        ]);
    }

    #[Route('/{id}/edit', name: 'ecodimactivite_edit', methods: ['GET', 'POST'])]
    #[Route('/new', name: 'ecodimactivite_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ?Ecodimactivite $ecodimactivite = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_ECODIM')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $user = $this->getUser();
        $type = $ecodimactivite === null ? 'new' : 'edit';
        $ecodimactivite = $ecodimactivite === null ? new Ecodimactivite() : $ecodimactivite;
        $form = $this->createForm(EcodimactiviteType::class, $ecodimactivite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($type === 'new') {
                $ecodimactivite->setCreatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setEglise($user->getEglise())
                        ->setCreatedBy($user)
                ;
            } else {
                $ecodimactivite->setUpdatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setUpdatedBy($user)
                ;
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($ecodimactivite);
            $entityManager->flush();
            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'ecodimactivite_new' : 'ecodimactivite_index';
            if ($nextAction) {
                $this->addFlash('success', 'Action effectuée avec succès.');
            }

            return $this->redirectToRoute($nextAction);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('ecodimactivite/new.html.twig', [
                    'ecodimactivite' => $ecodimactivite,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    #[Route('/listeparticipant', name: 'ecodimactivite_listeparticipant', methods: ['POST', 'GET'])]
    public function getListeparticipant(Request $request, EnfantactiviteRepository $enfantactiviteRepository, EcodimactiviteRepository $ecodimactiviteRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_ECODIM')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $participant = $request->request->get('selActivite');
        $listeparticipant = $ecodimactiviteRepository->findAll();
        if (!$participant) {

            return $this->render('ecodimactivite/listeparticipant.html.twig',
                            [
                                'listeparticipant' => '',
                                'ecodimactivite' => $listeparticipant,
                            ]
            );
        } else {

            $listed = $enfantactiviteRepository->participantByActivite($participant);

            return $this->render('ecodimactivite/listeparticipant.html.twig',
                            [
                                'listepartcipant' => $listed,
                                'ecodimactivite' => $listeparticipant,
            ]);
        }
    }

    #[Route('/{id}', name: 'ecodimactivite_show', methods: ['GET'])]
    public function show(Ecodimactivite $ecodimactivite): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        return $this->render('ecodimactivite/show.html.twig', [
                    'ecodimactivite' => $ecodimactivite,
        ]);
    }

    #[Route('/{id}', name: 'ecodimactivite_delete', methods: ['POST'])]
    public function delete(Request $request, Ecodimactivite $ecodimactivite): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_ECODIM')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($this->isCsrfTokenValid('delete' . $ecodimactivite->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();

            $ecodimactivite->setDeletedFromIp($this->GetIp());
            $ecodimactivite->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $ecodimactivite->setDeletedBy($user);
            $this->addFlash('danger', 'Suppression avec succès.');

            $entityManager->flush();
        }

        return $this->redirectToRoute('ecodimactivite_index');
    }

}
