<?php

namespace App\Controller;

use App\Entity\Cotiserpargroupe;
use App\Entity\Detailpargroupe;
use App\Form\CotiserpargroupeType;
use App\Repository\GroupeRepository;
use App\Repository\CotisationpargroupeRepository;
use App\Repository\CotiserpargroupeRepository;
use App\Repository\DetailpargroupeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use DateTime;
use App\Traits\ClientIp;


#[Route('/cotiserpargroupe')]
class CotiserpargroupeController extends AbstractController {
    use ClientIp;
    
    #[Route('/', name: 'app_cotiserpargroupe_index', methods: ['GET'])]
    public function index(CotiserpargroupeRepository $cotiserpargroupeRepository): Response {
        if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $cotiserpargroupe = $cotiserpargroupeRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        return $this->render('cotiserpargroupe/index.html.twig', [
                    'cotiserpargroupes' => $cotiserpargroupe,
        ]);
    }

    #[Route('/new', name: 'app_cotiserpargroupe_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, CotiserpargroupeRepository $cotiserpargroupeRepository, GroupeRepository $groupeRepository, CotisationpargroupeRepository $cotisationpargroupeRepository, DetailpargroupeRepository $detail): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $cotiserpargroupe = new Cotiserpargroupe();
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $cotiserpargroupe->setEglise($eglise);
        $groupe = $groupeRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $cotisationpargroupe = $cotisationpargroupeRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL, "etatcotiser"=>1]);
        $form = $this->createForm(CotiserpargroupeType::class, $cotiserpargroupe, ['groupe' => $groupe, 'cotisationpargroupe' => $cotisationpargroupe]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();


            $idc = $form['cotisationpargroupe']->getData();
            $date = $form['datecotiser']->getData();
            $idf = $form['groupe']->getData();
            $montant = $form['montantpayer']->getData();

            $dql = $cotiserpargroupeRepository->findBy(['groupe' => $cotiserpargroupe->getGroupe(), 'cotisationpargroupe' => $cotiserpargroupe->getCotisationpargroupe()]);

            if ($dql) {

                $id = $dql[0]->getId();
                $activite = $cotiserpargroupeRepository->findOneByCotiserpargroupe($id);
                $reste = $activite->getReste();
                $dejapayer = $activite->getMontantpayer();
                $a1 = 0;
                $b1 = 0;
                $a1 = ($reste - $montant);
                $b1 = ($dejapayer + $montant);
                $activite->setUpdatedFromIp($this->GetIp());
                $activite->setUpdatedBy($user);
                $activite->setMontantpayer($b1);
                $activite->setReste($a1);

                $detail2 = new Detailpargroupe();
                $detail2->setGroupe($idf);
                $detail2->setCotisationpargroupe($idc);
                $detail2->setEglise($eglise);
                $detail2->setMontant($b1);
                $detail2->setMontantpayer($montant);
                $detail2->setCreatedBy($user);
                $detail2->setCreatedFromIp($this->GetIp());
                $detail2->setReste($a1);
                // $detail2->setEtat('1');

                $detail2->setDatedetail($date);
                $entityManager->persist($detail2);
                $entityManager->flush();
            } else {

                $cotiserpargroupe = $form->getData();
                $cotiser2 = $cotisationpargroupeRepository->findOneByCotisationpargroupe($idc);
                $payer = $cotiser2->getMontant();
                $cotiserpargroupe->setCreatedBy($user);
                $restepayer = $payer - $montant;
                $cotiserpargroupe->setReste($restepayer);
                $cotiserpargroupe->setCreatedFromIp($this->GetIp());
                //$cotiserpargroupe->setEtatcotiser("1");

                $detail = new Detailpargroupe();
                $detail->setGroupe($idf);
                $detail->setCotisationpargroupe($idc);
                $detail->setEglise($eglise);
                $detail->setMontant($payer);
                $detail->setMontantpayer($montant);
                $detail->setCreatedBy($user);
                $detail->setCreatedFromIp($this->GetIp());
                $detail->setReste($restepayer);
                $detail->setDatedetail($date);
               // $detail->setEtat('1');
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($cotiserpargroupe);
                $entityManager->persist($detail);
                $entityManager->flush();
            }


            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'app_cotiserpargroupe_new' : 'app_cotiserpargroupe_index';
            if ($nextAction) {
                $this->addFlash('cotiserpargroupe', 'Enregistrement avec succès.');
            }

            return $this->redirectToRoute($nextAction);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('cotiserpargroupe/new.html.twig', [
                    'cotiserpargroupe' => $cotiserpargroupe,
                    'details' => $detail,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    #[Route('/detailcotisation', name: 'cotiserpargroupe_detailcotisation', methods: ['GET'])]
    public function detailCotisation(DetailpargroupeRepository $detailRepo): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $detailcotisation = $detailRepo->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        return $this->render('cotiserpargroupe/detailcotisation.html.twig', [
                    'details' => $detailcotisation,
        ]);
    }

    #[Route('cotiserpargroupe/{id}', name: 'app_cotiserpargroupe_show', methods: ['GET'])]
    public function show(Cotiserpargroupe $cotiserpargroupe): Response {
        if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        return $this->render('cotiserpargroupe/show.html.twig', [
                    'cotiserpargroupe' => $cotiserpargroupe,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_cotiserpargroupe_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Cotiserpargroupe $cotiserpargroupe, GroupeRepository $groupeRepository, CotisationpargroupeRepository $cotisationpargroupeRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $cotiserpargroupe->setUpdatedBy($user);
        // $$cotiserpargroupe->setEglise($eglise);
        $groupe = $groupeRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $cotisationpargroupe = $cotisationpargroupeRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL, "etatcotiser"=>1]);
        $form = $this->createForm(CotiserpargroupeType::class, $cotiserpargroupe, ['groupe' => $groupe, 'cotisationpargroupe' => $cotisationpargroupe]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

 
            $id = $form['cotisationpargroupe']->getData();

            $cotise2 = $cotisationpargroupeRepository->findOneByCotisationpargroupe($id);
            if ($cotise2) {
                $a = 0;
                $mont = $cotise2->getMontant();

                $montant = $form['montantpayer']->getData();
//            );
                $a = $mont - $montant;

                $cotiserpargroupe->setMontantpayer($montant);
                $cotiserpargroupe->setReste($a);
                $cotiserpargroupe->setUpdatedFromIp($this->GetIp());
                $cotiserpargroupe->setUpdatedBy($user);
                $this->getDoctrine()->getManager()->flush();
            }
            return $this->redirectToRoute('app_cotiserpargroupe_index');
        }

        return $this->render('cotiserpargroupe/edit.html.twig', [
                    'cotiserpargroupe' => $cotiserpargroupe,
                    'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_cotiserpargroupe_delete', methods: ['POST'])]
    public function delete(Request $request, Cotiserpargroupe $cotiserpargroupe): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_TRESORERIE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($this->isCsrfTokenValid('delete' . $cotiserpargroupe->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $cotiserpargroupe->setDeletedFromIp($this->GetIp());
            $cotiserpargroupe->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $cotiserpargroupe->setDeletedBy($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_cotiserpargroupe_index');
    }

}
