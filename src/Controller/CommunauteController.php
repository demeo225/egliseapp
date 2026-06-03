<?php

namespace App\Controller;

use App\Entity\Communaute;
use App\Form\CommunauteType;
use App\Repository\CommunauteRepository;
use App\Repository\EgliseRepository;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Traits\ClientIp;

#[Route('/communaute')]
#[IsGranted('ROLE_SUPER_ADMIN')]

class CommunauteController extends AbstractController {
    use ClientIp;
    
    #[Route('/', name: 'communaute_index', methods: ['GET'])]

    public function index(CommunauteRepository $communauteRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        return $this->render('communaute/index.html.twig', [
                    'communautes' => $communauteRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'communaute_new', methods: ['GET', 'POST'])]
    #[Route('/{id}/edit', name: 'communaute_edit', methods: ['GET', 'POST'])]
    public function new(Request $request, ?Communaute $communaute = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        
          $user = $this->getUser();
        $type = $communaute === null ? 'new' : 'edit';
        $communaute = $communaute === null ? new Communaute() : $communaute;
        $form = $this->createForm(CommunauteType::class, $communaute);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

             if ($type === 'new') {
                $communaute->setCreatedFromIp($this->GetIp()) // remplacement de la function par le trait
                    ->setCreatedBy($user)
                    
                ;
                  $this->addFlash('success', 'Enregistrement avec succès.');
            } else {
                $communaute->setUpdatedFromIp($this->GetIp()) // remplacement de la function par le trait
                    ->setUpdatedBy($user)
                ;
                 $this->addFlash('success', 'Modification avec succès.');
            }
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($communaute);
            $entityManager->flush();

            return $this->redirectToRoute('communaute_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('communaute/new.html.twig', [
                    'communaute' => $communaute,
                    'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'communaute_show', methods: ['GET'])]

    public function show(Communaute $communaute): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        return $this->render('communaute/show.html.twig', [
                    'communaute' => $communaute,
        ]);
    }

    #[Route('/detail/{id}', name: 'communaute_detail', methods: ['GET'])]

    public function detailcommunaute(Request $request, EgliseRepository $egliseRepository, CommunauteRepository $communauteRepository) {
        //Recuperation id communaute
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $user = $this->getUser();
        $idcommunaute = $request->query->get('id');
        //Recuperation de la liste des eglise par communaute
        $listeEglise = $egliseRepository->findBy(['communaute' => $idcommunaute, 'deletedAt' => NULL]);
        $lignecommunaute = $communauteRepository->find($idcommunaute);
        $nomcommunaute = $lignecommunaute->getLibelle();
        return $this->render('communaute/detail.html.twig', [
                    'eglises' => $listeEglise,
                    'id' => $idcommunaute,
                    'nomcommunaute' => $nomcommunaute,
        ]);
    }


    #[Route('communaute/{id}', name: 'communaute_delete', methods: ['POST'])]

    public function delete(Request $request, Communaute $communaute): Response {
        if ($this->isCsrfTokenValid('delete' . $communaute->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();

            if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
                throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
            }

    
            $communaute->setDeletedFromIp($this->GetIp());
            $communaute->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $communaute->setDeletedBy($user);
             $this->addFlash('danger', 'Suppression avec succès.');
            $entityManager->flush();
        }

        return $this->redirectToRoute('communaute_index');
    }

}
