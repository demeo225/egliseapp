<?php

namespace App\Controller;

use App\Entity\Region;
use App\Form\RegionType;
use App\Repository\EgliseRepository;
use App\Repository\RegionRepository;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Traits\ClientIp;

#[Route('/region')]
#[IsGranted('ROLE_SUPER_ADMIN')]
class RegionController extends AbstractController {

    use ClientIp;

    #[Route('/', name: 'app_region_index', methods: ['GET'])]
    public function index(RegionRepository $regionRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $region = $regionRepository->findBy(["deletedAt" => NULL]);
        return $this->render('region/index.html.twig', [
                    'regions' => $region,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_region_edit', methods: ['GET', 'POST'])]
    #[Route('/new', name: 'app_region_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ?Region $region=null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
       
              $user = $this->getUser();
        $type = $region === null ? 'add' : 'update';
        $region = $region === null ? new Region() : $region;
        $form = $this->createForm(RegionType::class, $region);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


           if ($type === 'add') {
                $region->setCreatedFromIp($this->GetIp()) // remplacement de la function par le trait
                    ->setCreatedBy($user)
                ;
            } else {
                $region->setUpdatedFromIp($this->GetIp()) // remplacement de la function par le trait
                    ->setUpdatedBy($user)
                ;
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($region);
            $entityManager->flush();
            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'app_region_new' : 'app_region_index';
            if ($nextAction) {
                $this->addFlash('success', 'Action effectuée avec succès.');
            }
            return $this->redirectToRoute($nextAction);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);

        return $this->render('region/new.html.twig', [
                    'region' => $region,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    #[Route('/{id}', name: 'app_region_show', methods: ['GET'])]
    public function show(Region $region): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        return $this->render('region/show.html.twig', [
                    'region' => $region,
        ]);
    }

    #[Route('/detail/{id}', name: 'app_region_detail', methods: ['GET'])]
    public function detailregion(Request $request, EgliseRepository $egliseRepository, RegionRepository $regionRepository) {
        //Recuperation id region
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $user = $this->getUser();
        $idregion = $request->query->get('id');
        //Recuperation de la liste des eglise par region
        $listeEglise = $egliseRepository->findBy(['region' => $idregion, 'deletedAt' => NULL]);
        $ligneregion = $regionRepository->find($idregion);
        $nomregion = $ligneregion->getLibelle();
        return $this->render('region/detail.html.twig', [
                    'eglises' => $listeEglise,
                    'id' => $idregion,
                    'nomregion' => $nomregion,
        ]);
    }

    #[Route('/{id}', name: 'app_region_delete', methods: ['POST'])]
    public function delete(Request $request, Region $region): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if ($this->isCsrfTokenValid('delete' . $region->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();

            $region->setDeletedFromIp($this->GetIp());
            $region->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $region->setDeletedBy($user);
            $this->addFlash('danger', 'Supression avec succès');
            $entityManager->flush();
        }


        return $this->redirectToRoute('app_region_index', [], Response::HTTP_SEE_OTHER);
    }

}
