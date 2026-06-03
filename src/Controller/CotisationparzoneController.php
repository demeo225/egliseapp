<?php

namespace App\Controller;

use App\Entity\Cotisationparzone;
use App\Form\CotisationparzoneType;
use App\Repository\CotisationparzoneRepository;
use App\Repository\CotiserpazoneRepository;
use App\Traits\ClientIp;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cotisationparzone')]
class CotisationparzoneController extends AbstractController {

    use ClientIp;

    #[Route('/', name: 'app_cotisationparzone_index', methods: ['GET'])]
    public function index(CotisationparzoneRepository $cotisationparzoneRepository): Response {
        if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $cotisationparzone = $cotisationparzoneRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        return $this->render('cotisationparzone/index.html.twig', [
                    'cotisationparzones' => $cotisationparzone,
        ]);
    }

    #[Route('/new', name: 'app_cotisationparzone_new', methods: ['GET', 'POST'])]
    #[Route('/{id}/edit', name: 'app_cotisationparzone_edit', methods: ['GET', 'POST'])]
    public function new(Request $request, CotisationparzoneRepository $cotisationparzoneRepository, ?Cotisationparzone $cotisationparzone = null): Response {
        if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }

        $user = $this->getUser();
        $type = $cotisationparzone === null ? 'new' : 'edit';
        $cotisationparzone = $cotisationparzone === null ? new Cotisationparzone() : $cotisationparzone;

        $form = $this->createForm(CotisationparzoneType::class, $cotisationparzone);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($type === 'new') {
                $cotisationparzone->setCreatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setEglise($user->getEglise())
                        ->setCreatedBy($user)
                        ->setEtatcotiser(1)
                ;
            } else {
                $cotisationparzone->setUpdatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setUpdatedBy($user)
                ;
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($cotisationparzone);
            $this->addFlash('addcotisationparzone', 'Action effectuée avec succès');
            $entityManager->flush();

            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'app_cotisationparzone_new' : 'app_cotisationparzone_index';
            if ($nextAction) {
                $this->addFlash('succeszone', 'Action effectuée avec succès.');
            }

            return $this->redirectToRoute($nextAction);
//            return $this->redirectToRoute('cotisationparzone_index', [], Response::HTTP_SEE_OTHER);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('cotisationparzone/new.html.twig', [
                    'cotisationparzone' => $cotisationparzone,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    #[Route('/cotiser/{id}', name: 'cotisationparzone_cotiser', methods: ['GET'])]
    public function detailCotisationparzone(Request $request, CotiserpazoneRepository $cotiserzoneRepository, CotisationparzoneRepository $cotisationparzoneRepo) {
        //Recuperation id cotisationparzone
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_FINANCE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $idcotisationparzone = $request->query->get('id');
        //Recuperation de la liste des fidele par cotisation zone
        $listeCotiserparzone = $cotiserzoneRepository->findBy(['cotisationparzone' => $idcotisationparzone, 'deletedAt' => NULL]);
        $ligneCotisationparzone = $cotisationparzoneRepo->find($idcotisationparzone);
        $nomCotisationparzone = $ligneCotisationparzone->getObjet();
        return $this->render('cotisationparzone/detail.html.twig', [
                    'cotiserparzones' => $listeCotiserparzone,
                    'id' => $idcotisationparzone,
                    'nomcotisationparzone' => $nomCotisationparzone,
        ]);
    }

    #[Route('/{id}', name: 'app_cotisationparzone_show', methods: ['GET'])]
    public function show(Cotisationparzone $cotisationparzone): Response {
        if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        return $this->render('cotisationparzone/show.html.twig', [
                    'cotisationparzone' => $cotisationparzone,
        ]);
    }

    #[Route('cotisationparzone/{id}', name: 'app_cotisationparzone_delete', methods: ['POST'])]
    public function delete(Request $request, Cotisationparzone $cotisationparzone): Response {
        if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($this->isCsrfTokenValid('delete' . $cotisationparzone->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();

            $cotisationparzone->setDeletedFromIp($this->GetIp());
            $cotisationparzone->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $cotisationparzone->setDeletedBy($user);
            $entityManager->flush();
        }
        $this->addFlash('suppcotisationparzone', 'Supression avec succès');
        return $this->redirectToRoute('app_cotisationparzone_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/cotisationparzone/', name: 'cotisationparzone_cloture', methods: ['POST'])]
    public function clotureCotyisation(Request $request, Cotisationparzone $cotisationparzone): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($this->isCsrfTokenValid('cloturecotisationparzone' . $cotisationparzone->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();
            $user = $this->getUser();
            $cotisationparzone->setUpdatedFromIp($this->GetIp()) // remplacement de la function par le trait
                    ->setUpdatedBy($user)
            ;

            $cotisationparzone->setEtatcotiser("0");

            $this->addFlash('cloturecotisationparzone', 'Cotisation cloturée avec succès.');
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_cotisationparzone_index');
    }

    #[Route('/{id}/cotisationparzone', name: 'cotisationparzone_active', methods: ['POST'])]
    public function activeCotisation(Request $request, Cotisationparzone $cotisationparzone): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($this->isCsrfTokenValid('activecotisationparzone' . $cotisationparzone->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();

            $user = $this->getUser();
            $cotisationparzone->setUpdatedFromIp($this->GetIp()) // remplacement de la function par le trait
                    ->setUpdatedBy($user)
            ;
            $cotisationparzone->setEtatcotiser("1");

            $this->addFlash('activecotisationparzone', 'Cotisation réactivée avec succès.');
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_cotisationparzone_index');
    }

}
