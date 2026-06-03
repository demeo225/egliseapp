<?php

namespace App\Controller;

use App\Entity\Nommination;
use App\Form\NomminationType;
use App\Repository\FideleRepository;
use App\Repository\NomminationRepository;
use App\Traits\ClientIp;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/nommination')]
class NomminationController extends AbstractController {

    use ClientIp;

    #[Route('/', name: 'app_nommination_index', methods: ['GET'])]
    public function index(NomminationRepository $nomminationRepository): Response {
        if (!$this->isGranted('ROLE_PASTEUR')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $nommination = $nomminationRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        return $this->render('nommination/index.html.twig', [
                    'nomminations' => $nommination,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_nommination_edit', methods: ['GET', 'POST'])]
    #[Route('/new', name: 'app_nommination_new', methods: ['GET', 'POST'])]
    public function new(EntityManagerInterface $entityManager, Request $request,FideleRepository $fideleRepository, NomminationRepository $nomminationRepository, ?Nommination $nommination = null): Response {
        if (!$this->isGranted('ROLE_PASTEUR')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $type = $nommination === null ? 'new' : 'edit';
        $nommination = $nommination === null ? new Nommination() : $nommination;
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $fidele = $fideleRepository->findBy(['eglise' => $eglise,  "deletedAt" => NULL]);
        $form = $this->createForm(NomminationType::class, $nommination, ['fidele' => $fidele]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //Adresse ip de l'utilisateur
            if ($type === 'new') {
                $nommination->setCreatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setEglise($user->getEglise())
                        ->setCreatedBy($user)
                ;
                

            } else {
                $nommination->setUpdatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setUpdatedBy($user)
                ;
            }
            $nomminationRepository->add($nommination);

            $this->addFlash('success', 'Action effectuée avec succès');

            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'app_nommination_new' : 'app_nommination_index';
            if ($nextAction) {
              //  $this->addFlash('enregnommination', 'Enregistrement avec succès.');
            }

            return $this->redirectToRoute($nextAction);
//            return $this->redirectToRoute('app_nommination_index', [], Response::HTTP_SEE_OTHER);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('nommination/new.html.twig', [
                    'nommination' => $nommination,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    #[Route('/{id}', name: 'app_nommination_show', methods: ['GET'])]
    public function show(Nommination $nommination): Response {
        if (!$this->isGranted('ROLE_PASTEUR')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        return $this->render('nommination/show.html.twig', [
                    'nommination' => $nommination,
        ]);
    }
    
    /**
     * @Route("/search/fideles/{id}", name="nommination_search_fideles", requirements={"id"="\d+"}, methods={"POST"})
     *
     * @return Response
     */
    public function nomminationSearchFideles(SerializerInterface $serializer, Nommination $nommination): Response
    {
          if (!$this->isGranted('ROLE_PASTEUR')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
      if ($nommination) {
            $fideles = (array) json_decode($serializer->serialize($nommination->getFidele()->toArray(), 'json', ['groups' => ['public']]));
        } else {
            $fideles = [];
        }
        return new Response($this->renderView('nommination/list.fideles.html.twig', [
                    'fideles' => $fideles
        ]));
        

    }

    #[Route('/{id}', name: 'app_nommination_delete', methods: ['POST'])]
    public function delete(Request $request, Nommination $nommination): Response {


        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_PASTEUR')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($this->isCsrfTokenValid('delete' . $nommination->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();

            $nommination->setDeletedFromIp($this->GetIp());
            $nommination->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $nommination->setDeletedBy($user);
            $entityManager->flush();
            if ($request) {
                $this->addFlash('danger', 'Suppression avec succès.');
            }
        }

        return $this->redirectToRoute('app_nommination_index');
    }

}
