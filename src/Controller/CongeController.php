<?php

namespace App\Controller;

use App\Entity\Conge;
use App\Form\CongeType;
use App\Repository\CongeRepository;
use App\Repository\FideleRepository;
use App\Traits\ClientIp;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/conge')]
class CongeController extends AbstractController {
    use ClientIp;

    #[Route('/', name: 'app_conge_index', methods: ['GET'])]
    public function index(CongeRepository $congeRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_PASTEUR')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $conge = $congeRepository->findBy(['eglise' => $eglise]);
        return $this->render('conge/index.html.twig', [
                    'conges' => $conge,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_conge_edit', methods: ['GET', 'POST'])]
    #[Route('/new', name: 'app_conge_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CongeRepository $congeRepository, FideleRepository $fideleRepository, ?Conge $conge = null): Response {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_PASTEUR')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $type = $conge === null ? 'new' : 'edit';
        $conge = $conge === null ? new Conge() : $conge;
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $fidele = $fideleRepository->findBy(['eglise' => $eglise,  "deletedAt" => NULL, "typefidele" => 'Non']);
        $form = $this->createForm(CongeType::class, $conge, ['fidele' => $fidele]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //Adresse ip de l'utilisateur
            if ($type === 'new') {
                $conge->setCreatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setEglise($user->getEglise())
                        ->setCreatedBy($user)
                        ->setEtat(0);
                  $this->addFlash('success', 'Enregistrement avec succès.');
                
            } else {
                $conge->setUpdatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setUpdatedBy($user)
                ;
                  $this->addFlash('success', 'Modification effectuée avec succès.');
            }

            $congeRepository->add($conge);
            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'app_conge_new' : 'app_conge_index';
        
            return $this->redirectToRoute($nextAction);
//            return $this->redirectToRoute('app_conge_index', [], Response::HTTP_SEE_OTHER);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('conge/new.html.twig', [
                    'conge' => $conge,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    #[Route('/{id}', name: 'app_conge_show')]
    public function show(Conge $conge): Response {
        if (!$this->isGranted('ROLE_PASTEUR')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }

        return $this->render('conge/show.html.twig', [
                    'conge' => $conge,
        ]);
    }

    #[Route('conge/{id}', name: 'app_conge_delete', methods: ['POST'])]
    public function delete(Request $request, Conge $conge): Response {


        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_PASTEUR')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }

        if ($this->isCsrfTokenValid('delete' . $conge->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();

            $conge->setDeletedFromIp($this->GetIp());
            $conge->setDeletedAt(new DateTime("now"));
            $conge->setDatefin(new DateTime("now"));
            $user = $this->getUser();
            $conge->setEtat(1);
            $conge->setDeletedBy($user);
            $entityManager->flush();
            if ($request) {
                $this->addFlash('danger', 'Suppression avec succès.');
            }
        }

        return $this->redirectToRoute('app_conge_index');
    }

}
