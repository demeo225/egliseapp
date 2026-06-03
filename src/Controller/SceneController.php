<?php

namespace App\Controller;

use App\Entity\Scene;
use App\Form\SceneType;
use App\Repository\FideleRepository;
use App\Repository\SceneRepository;
use App\Traits\ClientIp;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/scene')]
class SceneController extends AbstractController {

    use ClientIp;

    #[Route('/', name: 'app_scene_index', methods: ['GET'])]
    public function index(SceneRepository $sceneRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_PASTEUR')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $scene = $sceneRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        return $this->render('scene/index.html.twig', [
                    'scenes' => $scene,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_scene_edit', methods: ['GET', 'POST'])]
    #[Route('/new', name: 'app_scene_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SceneRepository $sceneRepository, FideleRepository $fideleRepo, ?Scene $scene = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_PASTEUR')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $type = $scene === null ? 'new' : 'edit';
        $scene = $scene === null ? new Scene() : $scene;
        $user = $this->getUser();
        $eglise = $this->getUser()->getEglise();
        $fidele1 = $fideleRepo->findBy(['eglise' => $eglise, "deletedAt" => NULL, "typefidele" => 'Non', "etatfidele" => 1]);
        $fidele2 = $fideleRepo->findBy(['eglise' => $eglise, "deletedAt" => NULL, "typefidele" => 'Non', "etatfidele" => 1]);
        $form = $this->createForm(SceneType::class, $scene, ['pasteur1'=>$fidele1, 'pasteur2'=>$fidele2]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($type === 'new') {
            

                $scene->setCreatedFromIp($this->GetIp())
                        ->setEglise($user->getEglise())
                        ->setCreatedBy($user)
                     
                ;
            } else {
                $scene->setUpdatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setUpdatedBy($user)
                ;
            }

            $sceneRepository->add($scene);
            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'app_scene_new' : 'app_scene_index';
            if ($nextAction) {
                $this->addFlash('success', 'Action effectuée avec succès.');
            }

            return $this->redirectToRoute($nextAction);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('scene/new.html.twig', [
                    'scene' => $scene,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    #[Route('/{id}', name: 'app_scene_show', methods: ['GET'])]
    public function show(Scene $scene): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_PASTEUR')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        return $this->render('scene/show.html.twig', [
                    'scene' => $scene,
        ]);
    }

    #[Route('/{id}', name: 'app_scene_delete', methods: ['POST'])]
    public function delete(Request $request, Scene $scene): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_PASTEUR')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if ($this->isCsrfTokenValid('delete' . $scene->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();

            $scene->setDeletedFromIp($this->GetIp());
            $scene->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $scene->setDeletedBy($user);
             $this->addFlash('danger', 'Suppression effectuée avec succès.');
            $entityManager->flush();

         
        }

        return $this->redirectToRoute('app_scene_index');
    }

}
