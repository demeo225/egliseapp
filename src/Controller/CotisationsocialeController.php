<?php

namespace App\Controller;

use App\Entity\Cotisationsociale;
use App\Form\CotisationsocialeType;
use App\Repository\CotisationsocialeRepository;
use App\Repository\CotisersocialeRepository;
use App\Traits\ClientIp;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cotisationsociale')]
class CotisationsocialeController extends AbstractController {
    use ClientIp;

    #[Route('/', name: 'app_cotisationsociale_index', methods: ['GET'])]
    public function index(CotisationsocialeRepository $cotisationsocialeRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $cotisationsociale = $cotisationsocialeRepository->findBy(['eglise' => $eglise, 'deletedAt' => NULL]);
        return $this->render('cotisationsociale/index.html.twig', [
                    'cotisationsociales' => $cotisationsociale,
        ]);
    }

    #[Route('/new', name: 'app_cotisationsociale_new', methods: ['GET', 'POST'])]
    #[Route('/{id}/edit', name: 'app_cotisationsociale_edit', methods: ['GET', 'POST'])]
    public function new(Request $request, CotisationsocialeRepository $cotisationsocialeRepository, ?Cotisationsociale $cotisationsociale = null): Response {
       $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        $type = $cotisationsociale === null ? 'new' : 'edit';
        $cotisationsociale = $cotisationsociale === null ? new Cotisationsociale() : $cotisationsociale;

        $form = $this->createForm(CotisationsocialeType::class, $cotisationsociale);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($type === 'new') {
                $cotisationsociale->setCreatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setEglise($user->getEglise())
                        ->setCreatedBy($user)
                ;
            } else {
                $cotisationsociale->setUpdatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setUpdatedBy($user)
                ;
            }


            $cotisationsocialeRepository->add($cotisationsociale);

            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'app_cotisationsociale_new' : 'app_cotisationsociale_index';
            if ($nextAction) {
                $this->addFlash('cotisationsociale', 'Action effectuée avec succès.');
            }

            return $this->redirectToRoute($nextAction);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('cotisationsociale/new.html.twig', [
                    'cotisationsociale' => $cotisationsociale,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

        #[Route('/cotiser/{id}', name: 'cotisationsociale_cotisersociale', methods: ['GET'])]
    public function detailCotisationsociale(Request $request, CotisersocialeRepository $cotisersocialeRepository, CotisationsocialeRepository $cotisationRepo) {
        //Recuperation id cotisation
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_SOCIAL')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $idcotisation = $request->query->get('id');
        //Recuperation de la liste des fidele par cotisation 
        $listeCotisersociale = $cotisersocialeRepository->findBy(['cotisationsociale' => $idcotisation, 'deletedAt' => NULL]);
        $ligneCotisationsociale = $cotisationRepo->find($idcotisation);
        $nomCotisationsociale = $ligneCotisationsociale->getObjet();
        return $this->render('cotisationsociale/detail.html.twig', [
                    'cotisersociales' => $listeCotisersociale,
                    'id' => $idcotisation,
                    'nomcotisationsociale' => $nomCotisationsociale,
        ]);
    }
    #[Route('/{id}', name: 'app_cotisationsociale_show', methods: ['GET'])]
    public function show(Cotisationsociale $cotisationsociale): Response {
        return $this->render('cotisationsociale/show.html.twig', [
                    'cotisationsociale' => $cotisationsociale,
        ]);
    }

    #[Route('cotisationsociale/{id}', name: 'app_cotisationsociale_delete', methods: ['POST'])]
    public function delete(Request $request, Cotisationsociale $cotisationsociale): Response {
        if ($this->isCsrfTokenValid('delete' . $cotisationsociale->getId(), $request->request->get('_token'))) {
            if (!$this->isGranted('ROLE_RESPONSABLE_SOCIAL')) {
                throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici! Veuillez vous adresser au secretariat!');
            }
            $entityManager = $this->getDoctrine()->getManager();
            $cotisationsociale->setDeletedFromIp($this->GetIp());
            $cotisationsociale->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $cotisationsociale->setDeletedBy($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_cotisationsociale_index', [], Response::HTTP_SEE_OTHER);
    }

}
