<?php

namespace App\Controller;

use App\Entity\Evangelisation;
use App\Form\EvangelisationType;
use App\Repository\EvangelisationRepository;
use App\Traits\ClientIp;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/evangelisation')]
class EvangelisationController extends AbstractController {

    use ClientIp;

    #[Route('/', name: 'app_evangelisation_index', methods: ['GET'])]
    public function index(EvangelisationRepository $evangelisationRepository): Response {
        if (!$this->isGranted('ROLE_RESPONSABLE_EVANGELISATION')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $evangelisation = $evangelisationRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        return $this->render('evangelisation/index.html.twig', [
                    'evangelisations' => $evangelisation,
        ]);
    }

   // #[Route('/{id}/edit', name: 'app_evangelisation_edit', methods: ['GET', 'POST'])]
    #[Route('/new', name: 'app_evangelisation_new', methods: ['GET', 'POST'])]
    public function new(EntityManagerInterface $entityManager, Request $request, EvangelisationRepository $evangelisationRepository, ?Evangelisation $evangelisation = null): Response {
        if (!$this->isGranted('ROLE_RESPONSABLE_EVANGELISATION')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $type = $evangelisation === null ? 'new' : 'edit';
        $evangelisation = $evangelisation === null ? new Evangelisation() : $evangelisation;
        $user = $this->getUser();
        $eglise = $this->getUser()->getEglise();
        //  $ame = $ameRepository->findBy(['eglise' => $eglise,  "deletedAt" => NULL]);
        $form = $this->createForm(EvangelisationType::class, $evangelisation);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //Adresse ip de l'utilisateur
            if ($type === 'new') {
                $evangelisation->setCreatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setEglise($user->getEglise())
                        ->setCreatedBy($user)
                ;
            } else {
                $evangelisation->setUpdatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setUpdatedBy($user)
                ;
            }
            $evangelisationRepository->add($evangelisation);

            $this->addFlash('success', 'Enregistrement effectué avec succès');

            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'app_evangelisation_new' : 'app_evangelisation_index';
            if ($nextAction) {
                $this->addFlash('sucess', 'Enregistrement avec succès.');
            }

            return $this->redirectToRoute($nextAction);
//            return $this->redirectToRoute('app_evangelisation_index', [], Response::HTTP_SEE_OTHER);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('evangelisation/new.html.twig', [
                    'evangelisation' => $evangelisation,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    #[Route('/{id}/update', name: 'app_evangelisation_edit', methods: ['GET', 'POST'])]
    public function updateEvangelisation(Request $request, Evangelisation $evangelisation): Response {
        $data = json_decode($request->getContent(), true);
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $user = $this->getUser();
        $form = $this->createForm(EvangelisationType::class, $evangelisation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //Adresse ip de l'utilisateur
            $user = $this->getUser();

            $evangelisation->setUpdatedFromIp($this->GetIp());
            $evangelisation->setUpdatedBy($user);
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Modification avec succès');

            return $this->redirectToRoute('app_evangelisation_index');
        }
        return $this->render('evangelisation/edit.html.twig', [
                    'evangelisation' => $evangelisation,
                    'form' => $form->createView(),
        ]);
    }

    #[Route('evangile/{id}', name: 'app_evangelisation_show', methods: ['GET', 'POST'])]
    public function show(Evangelisation $evangelisation): Response {
        if (!$this->isGranted('ROLE_RESPONSABLE_EVANGELISATION')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        return $this->render('evangelisation/show.html.twig', [
                    'evangelisation' => $evangelisation,
        ]);
    }

    /**
     * @Route("/search/ames/{id}", name="evangelisation_search_ames", requirements={"id"="\d+"}, methods={"POST"})
     *
     * @return Response
     */
    public function evangelisationSearchFideles(SerializerInterface $serializer, Evangelisation $evangelisation): Response {
        if (!$this->isGranted('ROLE_RESPONSABLE_EVANGELISATION')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($evangelisation) {
            $ames = (array) json_decode($serializer->serialize($evangelisation->getAmes()->toArray(), 'json', ['groups' => ['public']]));
        } else {
            $ames = [];
        }
        return new Response($this->renderView('evangelisation/list.fideles.html.twig', [
                    'ames' => $ames
        ]));
    }

    #[Route('evangelisation/{id}', name: 'app_evangelisation_delete', methods: ['POST'])]
    public function delete(Request $request, Evangelisation $evangelisation): Response {


        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_EVANGELISATION')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($this->isCsrfTokenValid('delete' . $evangelisation->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();

            $evangelisation->setDeletedFromIp($this->GetIp());
            $evangelisation->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $evangelisation->setDeletedBy($user);
            $entityManager->flush();
            if ($request) {
                $this->addFlash('danger', 'Suppression avec succès.');
            }
        }

        return $this->redirectToRoute('app_evangelisation_index');
    }

}
