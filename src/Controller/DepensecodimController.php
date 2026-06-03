<?php

namespace App\Controller;

use App\Entity\Depensecodim;
use App\Form\DepensecodimType;
use App\Repository\DepensecodimRepository;
use App\Traits\ClientIp;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/depensecodim')]
class DepensecodimController extends AbstractController {

    use ClientIp;

    #[Route('/', name: 'app_depensecodim_index', methods: ['GET'])]
    public function index(DepensecodimRepository $depensecodimRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_ECODIM')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();

        $user = $this->getUser();
        $depensecodim = $depensecodimRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        return $this->render('depensecodim/index.html.twig', [
                    'depensecodims' => $depensecodim,
        ]);
    }
  #[Route('/new', name: 'app_depensecodim_new', methods: ['GET', 'POST'])]
    #[Route('/{id}/edit', name: 'app_depensecodim_edit', methods: ['GET', 'POST'])]
    public function new(EntityManagerInterface $entityManager, Request $request, ?Depensecodim $depensecodim = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $user = $this->getUser();
        $type = $depensecodim === null ? 'new' : 'edit';
        $depensecodim = $depensecodim === null ? new Depensecodim() : $depensecodim;
        $form = $this->createForm(DepensecodimType::class, $depensecodim);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            if ($type === 'new') {
                $depensecodim->setCreatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setEglise($user->getEglise())
                        ->setCreatedBy($user)
                ;
                  $this->addFlash('message', 'Enregistrement effectué avec succès');
            } else {
                $depensecodim->setUpdatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setUpdatedBy($user)
                ;
                  $this->addFlash('message', 'Modification avec succès');
            }
            $depensecodim = $form->getData();
            $entityManager->persist($depensecodim);
            $entityManager->flush();

            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'app_depensecodim_new' : 'app_depensecodim_index';
      

            return $this->redirectToRoute($nextAction);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);

        return $this->render('depensecodim/new.html.twig', [
                    'depensecodims' => $depensecodim,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    #[Route('/{id}', name: 'app_depensecodim_show', methods: ['GET'])]
    public function show(Depensecodim $depensecodim): Response {
        return $this->render('depensecodim/show.html.twig', [
                    'depensecodim' => $depensecodim,
        ]);
    }

    #[Route('depensecodim/{id}', name: 'app_depensecodim_delete', methods: ['POST'])]
    public function delete(Request $request, Depensecodim $depensecodim): Response {


        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_PASTEUR')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }

        if ($this->isCsrfTokenValid('delete' . $depensecodim->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();

            $depensecodim->setDeletedFromIp($this->GetIp());
            $depensecodim->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $depensecodim->setEditable(0);
            $depensecodim->setDeletedBy($user);
            $entityManager->flush();
            if ($request) {
                $this->addFlash('message', 'Suppression avec succès.');
            }
        }

        return $this->redirectToRoute('app_depensecodim_index');
    }

}
