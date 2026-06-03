<?php

namespace App\Controller;

use App\Entity\Cartesocial;
use App\Form\CartesocialType;
use App\Repository\CartesocialRepository;
use App\Repository\FideleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use DateTime;
use App\Traits\ClientIp;

#[Route('/cartesocial')]
class CartesocialController extends AbstractController {

    use ClientIp;

    #[Route('/', name: 'app_cartesocial_index', methods: ['GET'])]
    public function index(CartesocialRepository $cartesocialRepository): Response {
        if (!$this->isGranted('ROLE_RESPONSABLE_SOCIAL')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $roles = $user->getRoles();
        $cartesocial = $cartesocialRepository->findBy(['eglise' => $eglise, 'deletedAt' => NULL]);
        return $this->render('cartesocial/index.html.twig', [
                    'cartesocials' => $cartesocial,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_cartesocial_edit', methods: ['GET', 'POST'])]
    #[Route('/new', name: 'app_cartesocial_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CartesocialRepository $cartesocialRepository, FideleRepository $fideleRepository, ?Cartesocial $cartesocial = null): Response {
        if (!$this->isGranted('ROLE_RESPONSABLE_SOCIAL')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $type = $cartesocial === null ? 'app_cartesocial_new' : 'edit';
        $cartesocial = $cartesocial === null ? new Cartesocial() : $cartesocial;
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $fidele = $fideleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL, "etatfidele" => 1]);
        $form = $this->createForm(CartesocialType::class, $cartesocial, ['fidele' => $fidele]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $cartesocial->setCreatedFromIp($this->GetIp());

            if ($type === 'app_cartesocial_new') {
                $cartesocial->setCreatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setEglise($user->getEglise())
                        ->setCreatedBy($user)
                ;
                $this->addFlash('success', 'Enregistrement effectué avec succès.');
            } else {
                $cartesocial->setUpdatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setUpdatedBy($user)
                ;
                $this->addFlash('success', 'Modification effectuée avec succès.');
            }
            $cartesocialRepository->add($cartesocial);

            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'app_cartesocial_new' : 'app_cartesocial_index';
//            if ($nextAction) {
//            }

            return $this->redirectToRoute($nextAction);
//            return $this->redirectToRoute('app_seancezone_index', [], Response::HTTP_SEE_OTHER);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('cartesocial/new.html.twig', [
                    'cartesocial' => $cartesocial,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    #[Route('/{id}', name: 'app_cartesocial_show', methods: ['GET'])]
    public function show(Cartesocial $cartesocial): Response {
        if (!$this->isGranted('ROLE_RESPONSABLE_SOCIAL')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        return $this->render('cartesocial/show.html.twig', [
                    'cartesocial' => $cartesocial,
        ]);
    }

    #[Route('cartesocial/{id}', name: 'app_cartesocial_delete', methods: ['POST'])]
    public function delete(Request $request, Cartesocial $cartesocial): Response {
        if ($this->isCsrfTokenValid('delete' . $cartesocial->getId(), $request->request->get('_token'))) {
            if (!$this->isGranted('ROLE_RESPONSABLE_SOCIAL')) {
                throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
            }
            $entityManager = $this->getDoctrine()->getManager();

            $cartesocial->setDeletedFromIp($this->GetIp());
            $cartesocial->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $cartesocial->setDeletedBy($user);
            $this->addFlash('danger', 'Suppression avec succès.');
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_cartesocial_index', [], Response::HTTP_SEE_OTHER);
    }

}
