<?php

namespace App\Controller;

use App\Entity\Discipline;
use App\Form\DisciplineType;
use App\Repository\DisciplineRepository;
use App\Repository\FideleRepository;
use App\Traits\ClientIp;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/discipline')]
class DisciplineController extends AbstractController {
    use ClientIp;

    #[Route('/', name: 'app_discipline_index', methods: ['GET'])]
    public function index(DisciplineRepository $disciplineRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_PASTEUR')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $discipline = $disciplineRepository->findBy(['eglise' => $eglise]);
        return $this->render('discipline/index.html.twig', [
                    'disciplines' => $discipline,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_discipline_edit', methods: ['GET', 'POST'])]
    #[Route('/new', name: 'app_discipline_new', methods: ['GET', 'POST'])]
    public function new(Request $request, DisciplineRepository $disciplineRepository, FideleRepository $fideleRepository, ?Discipline $discipline = null): Response {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_PASTEUR')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $type = $discipline === null ? 'new' : 'edit';
        $discipline = $discipline === null ? new Discipline() : $discipline;
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $fidele = $fideleRepository->findBy(['eglise' => $eglise,  "deletedAt" => NULL]);
        $form = $this->createForm(DisciplineType::class, $discipline, ['fidele' => $fidele]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //Adresse ip de l'utilisateur
            if ($type === 'new') {
                $discipline->setCreatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setEglise($user->getEglise())
                        ->setCreatedBy($user)
                        ->setEtat(0);
                ;
            } else {
                $discipline->setUpdatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setUpdatedBy($user)
                ;
            }

            $disciplineRepository->add($discipline);
            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'app_discipline_new' : 'app_discipline_index';
            if ($nextAction) {
                $this->addFlash('success', 'Action effectuée avec succès.');
            }

            return $this->redirectToRoute($nextAction);
//            return $this->redirectToRoute('app_discipline_index', [], Response::HTTP_SEE_OTHER);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('discipline/new.html.twig', [
                    'discipline' => $discipline,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    #[Route('/{id}', name: 'app_discipline_show')]
    public function show(Discipline $discipline): Response {
        if (!$this->isGranted('ROLE_PASTEUR')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }

        return $this->render('discipline/show.html.twig', [
                    'discipline' => $discipline,
        ]);
    }

    #[Route('/{id}', name: 'app_discipline_delete', methods: ['POST'])]
    public function delete(Request $request, Discipline $discipline): Response {


        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_PASTEUR')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }

        if ($this->isCsrfTokenValid('delete' . $discipline->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();

            $discipline->setDeletedFromIp($this->GetIp());
            $discipline->setDeletedAt(new DateTime("now"));
            $discipline->setDatefin(new DateTime("now"));
            $user = $this->getUser();
            $discipline->setEtat(1);
            $discipline->setDeletedBy($user);
            $entityManager->flush();
            if ($request) {
                $this->addFlash('danger', 'Suppression avec succès.');
            }
        }

        return $this->redirectToRoute('app_discipline_index');
    }

}
