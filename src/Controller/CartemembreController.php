<?php

namespace App\Controller;

use App\Entity\Cartemembre;
use App\Form\CartemembreType;
use App\Repository\CartemembreRepository;
use App\Repository\FideleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use DateTime;
use App\Traits\ClientIp;

#[Route('/cartemembre')]
class CartemembreController extends AbstractController {

    use ClientIp;

    #[Route('/', name: 'app_cartemembre_index', methods: ['GET'])]
    public function index(CartemembreRepository $cartemembreRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $roles = $user->getRoles();
        $cartemembre = $cartemembreRepository->findBy(['eglise' => $eglise, 'deletedAt' => NULL]);
        return $this->render('cartemembre/index.html.twig', [
                    'cartemembres' => $cartemembre,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_cartemembre_edit', methods: ['GET', 'POST'])]
    #[Route('/new', name: 'app_cartemembre_new')]
    public function new(Request $request, CartemembreRepository $cartemembreRepository, FideleRepository $fideleRepository, ?Cartemembre $cartemembre = null): Response {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();
        $type = $cartemembre === null ? 'new' : 'edit';
        $cartemembre = $cartemembre === null ? new Cartemembre() : $cartemembre;
        $eglise = $this->getUser()->getEglise();

        $fidele = $fideleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL, "etatfidele" => 1]);
        $form = $this->createForm(CartemembreType::class, $cartemembre, ['fidele' => $fidele]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //Adresse ip de l'utilisateur

            $cartemembre->setCreatedFromIp($this->GetIp());

            if ($type === 'new') {
                $cartemembre->setCreatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setEglise($user->getEglise())
                        ->setCreatedBy($user)
                ;
                $this->addFlash('success', 'Enregistrement effcetué avec succès.');
            } else {
                $cartemembre->setUpdatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setUpdatedBy($user)
                ;
                $this->addFlash('success', 'Modification effcetuée avec succès.');
            }
            $cartemembreRepository->add($cartemembre);

            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'app_cartemembre_new' : 'app_cartemembre_index';
//            if ($nextAction) {
//            }

            return $this->redirectToRoute($nextAction);
//            return $this->redirectToRoute('app_seancezone_index', [], Response::HTTP_SEE_OTHER);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('cartemembre/new.html.twig', [
                    'cartemembre' => $cartemembre,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    #[Route('cartemembre/{id}', name: 'app_cartemembre_delete', methods: ['POST'])]
    public function delete(Request $request, Cartemembre $cartemembre): Response {
        if ($this->isCsrfTokenValid('delete' . $cartemembre->getId(), $request->request->get('_token'))) {
            if (!$this->isGranted('ROLE_SECRETAIRE')) {
                throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici! Veuillez vous adresser au secretariat!');
            }
            $entityManager = $this->getDoctrine()->getManager();

            $cartemembre->setDeletedFromIp($this->GetIp());
            $cartemembre->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $this->addFlash('danger', 'Supression avec succès.');

            $cartemembre->setDeletedBy($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_cartemembre_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}', name: 'app_cartemembre_show', methods: ['GET'])]
    public function detailCarte(Cartemembre $cartemembre): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        return $this->render('cartemembre/show.html.twig', [
                    'cartemembre' => $cartemembre,
        ]);
    }

}
