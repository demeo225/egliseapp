<?php

namespace App\Controller;

use App\Entity\Activitesociale;
use App\Form\ActivitesocialeType;
use App\Repository\ActivitesocialeRepository;
use App\Repository\EnfantRepository;
use App\Repository\FideleRepository;
use App\Traits\ClientIp;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/activitesociale')]
class ActivitesocialeController extends AbstractController {

    use ClientIp;

    #[Route('/', name: 'app_activitesociale_index', methods: ['GET'])]
    public function index(ActivitesocialeRepository $activitesocialeRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $activitesociale = $activitesocialeRepository->findBy(['eglise' => $eglise, 'deletedAt' => NULL]);
        return $this->render('activitesociale/index.html.twig', [
                    'activitesociales' => $activitesociale,
        ]);
    }

    #[Route('/enfantsociaux', name: 'app_activitesociale_enfantsociaux', methods: ['GET'])]
    public function indexEnfantsociaux(EnfantRepository $enfantRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $enfant = $enfantRepository->findBy(['eglise' => $eglise, 'deletedAt' => NULL, 'etatenfant' => '1', 'situationparent' => 'Non']);
        return $this->render('activitesociale/enfantsociaux.html.twig', [
                    'enfants' => $enfant,
        ]);
    }

    #[Route('/enfantparentsociaux', name: 'app_activitesociale_enfantparentsociaux', methods: ['GET'])]
    public function indexEnfantparentsociaux(EnfantRepository $enfantRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $enfant = $enfantRepository->findBy(['eglise' => $eglise, 'deletedAt' => NULL, 'etatenfant' => 1, 'situation' => 'Oui']);
        return $this->render('activitesociale/enfantparentsociaux.html.twig', [
                    'enfants' => $enfant,
        ]);
    }

    #[Route('/adultesociaux', name: 'app_activitesociale_adultesociaux', methods: ['GET'])]
    public function indexAdultesociaux(FideleRepository $fideleRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $adultesociaux = $fideleRepository->findBy(['eglise' => $eglise, 'deletedAt' => NULL, "etatfidele" => 1, 'situation' => 'Non']);
        return $this->render('activitesociale/adultesociaux.html.twig', [
                    'fideles' => $adultesociaux,
        ]);
    }

    #[Route('/adulteparentsociaux', name: 'app_activitesociale_adulteparentsociaux', methods: ['GET'])]
    public function indexAdulteparentsociaux(FideleRepository $fideleRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $adultesociaux = $fideleRepository->findBy(['eglise' => $eglise, 'deletedAt' => NULL, "etatfidele" => 1, "etatvieparent" => 'Non']);
        return $this->render('activitesociale/adulteparentsociaux.html.twig', [
                    'fideles' => $adultesociaux,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_activitesociale_edit', methods: ['GET', 'POST'])]
    #[Route('/new', name: 'app_activitesociale_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ActivitesocialeRepository $activitesocialeRepository, ?Activitesociale $activitesociale = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();
        $type = $activitesociale === null ? 'new' : 'edit';
        $activitesociale = $activitesociale === null ? new Activitesociale() : $activitesociale;

        $form = $this->createForm(ActivitesocialeType::class, $activitesociale);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $activitesociale->setCreatedFromIp($this->GetIp());
            if ($type === 'new') {
                $activitesociale->setCreatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setEglise($user->getEglise())
                        ->setCreatedBy($user)
                ;
                $this->addFlash('success', 'Enregistrement effectué avec succès.');
            } else {
                $activitesociale->setUpdatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setUpdatedBy($user)
                ;
                $this->addFlash('success', 'Modification effectuée avec succès.');
            }

            $activitesocialeRepository->add($activitesociale);

            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'app_activitesociale_new' : 'app_activitesociale_index';
//            if ($nextAction) {
//            }

            return $this->redirectToRoute($nextAction);
//            return $this->redirectToRoute('app_activitesociale_index', [], Response::HTTP_SEE_OTHER);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('activitesociale/new.html.twig', [
                    'activitesociale' => $activitesociale,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    #[Route('detail/{id}', name: 'app_activitesociale_show', methods: ['GET', 'POST'])]
    public function show(Activitesociale $activitesociale): Response {
        return $this->render('activitesociale/show.html.twig', [
                    'activitesociale' => $activitesociale,
        ]);
    }

    #[Route('activitesociale/{id}', name: 'app_activitesociale_delete', methods: ['POST'])]
    public function delete(Request $request, Activitesociale $activitesociale): Response {
        if ($this->isCsrfTokenValid('delete' . $activitesociale->getId(), $request->request->get('_token'))) {
            if (!$this->isGranted('ROLE_SECRETAIRE')) {
                throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici! Veuillez vous adresser au secretariat!');
            }
            $entityManager = $this->getDoctrine()->getManager();

            $activitesociale->setDeletedFromIp($this->GetIp());
            $activitesociale->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $activitesociale->setDeletedBy($user);
            $this->addFlash('danger', 'Suppression avec succès.');

            $entityManager->flush();
        }

        return $this->redirectToRoute('app_activitesociale_index', [], Response::HTTP_SEE_OTHER);
    }

}
