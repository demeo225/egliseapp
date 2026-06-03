<?php

namespace App\Controller;

use App\Entity\Detailenfantactivite;
use App\Entity\Enfantactivite;
use App\Form\EnfantactiviteType;
use App\Repository\DetailenfantactiviteRepository;
use App\Repository\EcodimactiviteRepository;
use App\Repository\EnfantactiviteRepository;
use App\Repository\EnfantRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Traits\ClientIp;

#[Route('/enfantactivite')]
class EnfantactiviteController extends AbstractController {
    use ClientIp;
    
    #[Route('/', name: 'enfantactivite_index', methods: ['GET'])]
    public function index(EnfantactiviteRepository $enfantactiviteRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_ECODIM')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $enfantactivite = $enfantactiviteRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL,]);
        return $this->render('enfantactivite/index.html.twig', [
                    'enfantactivites' => $enfantactivite,
        ]);
    }

    #[Route('/detailcotisation', name: 'enfantactivite_detailcotisation', methods: ['GET'])]
    public function detailCotisation(DetailenfantactiviteRepository $detailRepo): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_ECODIM')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $detailcotisation = $detailRepo->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        return $this->render('enfantactivite/detailcotisation.html.twig', [
                    'details' => $detailcotisation,
        ]);
    }

    #[Route('/new', name: 'enfantactivite_new', methods: ['GET', 'POST'])]
    public function new(EntityManagerInterface $entityManager, Request $request, EnfantactiviteRepository $enfantactiviteRepository, EcodimactiviteRepository $ecodimactiviteRepository, EnfantRepository $enfantRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_ECODIM')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $enfantactivite = new Enfantactivite();
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $enfant = $enfantRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL, "etatenfant" => 1, "editable" => 1]);
        $ecodimactivite = $ecodimactiviteRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $form = $this->createForm(EnfantactiviteType::class, $enfantactivite, ['enfant' => $enfant, 'ecodimactivite' => $ecodimactivite]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $idact = $form['ecodimactivite']->getData();
            $idenf = $form['enfant']->getData();
            $montant = $form['montantpayer']->getData();
            $date = $form['datecotiser']->getData();

            $dql = $enfantactiviteRepository->findBy(['enfant' => $enfantactivite->getEnfant(), 'ecodimactivite' => $enfantactivite->getEcodimactivite()]);

            if ($dql) {


                $id = $dql[0]->getId();
                $activite = $enfantactiviteRepository->findOneByEnfantactivite($id);
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

                $detail2 = new Detailenfantactivite();
                $detail2->setEnfant($idenf);
                $detail2->setEcodimactivite($idact);
//                $detail2->setEnfantactivite($idact2);
                $detail2->setEglise($eglise);
                $detail2->setMontant($b1);
                $detail2->setMontantpayer($montant);
                $detail2->setCreatedBy($user);
                $detail2->setCreatedFromIp($this->GetIp());
                $detail2->setReste($a1);
                $detail2->setDatedetail($date);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($detail2);

                $entityManager->flush();
//                $this->getDoctrine()->getManager()->flush();
            } else {


                $enfantactivite = $form->getData();
                $cotiser1 = $ecodimactiviteRepository->findOneByEcodimactivite($idact);
                $payer = $cotiser1->getParticipation();
//                    $montant = $cotiser->getMontant();
                $restepayer = $payer - $montant;

                $enfantactivite->setReste($restepayer);
                $enfantactivite->setEglise($eglise);
                $enfantactivite->setCreatedFromIp($this->GetIp());
                $enfantactivite->setCreatedBy($user);

                
                 $detail = new Detailenfantactivite();
                $detail->setEnfant($idenf);
                $detail->setEcodimactivite($idact);
//                $detail->setEnfantactivite($idact2);
                $detail->setEglise($eglise);
                $detail->setMontant($payer);
                $detail->setMontantpayer($montant);
                $detail->setCreatedBy($user);
                $detail->setCreatedFromIp($this->GetIp());
                $detail->setReste($restepayer);
                $detail->setDatedetail($date);
               
                $entityManager->persist($detail);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($enfantactivite);
                $entityManager->flush();
            }

            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'enfantactivite_new' : 'enfantactivite_index';
            if ($nextAction) {
                $this->addFlash('success', 'Enregistrement avec succès.');
            }

            return $this->redirectToRoute($nextAction);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('enfantactivite/new.html.twig', [
                    'enfantactivite' => $enfantactivite,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    #[Route('/{id}/edit', name: 'enfantactivite_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Enfantactivite $enfantactivite, EnfantactiviteRepository $enfantactiviteRepository, EcodimactiviteRepository $ecodimactiviteRepository, EnfantRepository $enfantRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_ECODIM')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
//        $enfantactivite->setCreatedBy($user);
        $enfant = $enfantRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL, "etatenfant" => 1, "editable" => 1]);
        $ecodimactivite = $ecodimactiviteRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL,]);
        $form = $this->createForm(EnfantactiviteType::class, $enfantactivite, ['enfant' => $enfant, 'ecodimactivite' => $ecodimactivite]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

 

            $id = $form['ecodimactivite']->getData();

            $cotise2 = $ecodimactiviteRepository->findOneByEcodimactivite($id);
            if ($cotise2) {
                $a = 0;
                $mont = $cotise2->getParticipation();

                $montant = $form['montantpayer']->getData();
                $a = $mont - $montant;
                $enfantactivite->setMontantpayer($montant);
                $enfantactivite->setReste($a);
                $enfantactivite->setUpdatedFromIp($this->GetIp());
                $enfantactivite->setUpdatedBy($user);
                $this->getDoctrine()->getManager()->flush();
            }
                $this->addFlash('success', 'Modification effectuée avec succès.');
            return $this->redirectToRoute('enfantactivite_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('enfantactivite/edit.html.twig', [
                    'enfantactivite' => $enfantactivite,
                    'form' => $form->createView(),
        ]);
    }

    #[Route('/listeparticipant', name: 'enfantactivite_listeparticipant', methods: ['POST', 'GET'])]
    public function getListeparticipant(Request $request, EnfantactiviteRepository $enfantactiviteRepository) {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_ECODIM')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $participant = $request->request->get('selActivite');

        $listeparticipant = $enfantactiviteRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL, "etat" => 1]);
        if (!$participant) {

            return $this->render('enfantactivite/listeparticipant.html.twig',
                            [
                                'listeparticipant' => '',
                                'enfantactivite' => $listeparticipant,
                            ]
            );
        } else {

            $listed = $enfantactiviteRepository->participantByActivite($participant);

            return $this->render('enfantactivite/listeparticipant.html.twig',
                            [
                                'listepartcipants' => $listed,
                                'enfantactivites' => $listeparticipant,
            ]);
        }
    }

    #[Route('/suppenfantactive/{id}', name: 'enfantactivite_suppenfantactive')]
    public function supp(Request $request, Enfantactivite $enfantactivite): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_ECODIM')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($this->isCsrfTokenValid('supp' . $enfantactivite->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();

  
            $enfantactivite->setDeletedFromIp($this->GetIp());
            $enfantactivite->setDeletedAt(new DateTime("now"));
            $enfantactivite->setEtat('0');
            $user = $this->getUser();
            $enfantactivite->setDeletedBy($user);
            if ($request) {
                $this->addFlash('danger', 'Suppression avec succès.');
            }
            $entityManager->flush();
        }

        return $this->redirectToRoute('enfantactivite_listeparticipant');
    }

    #[Route('/archive', name: 'enfantactivite_archiveenfantactivite')]
    public function listeSupp(EnfantactiviteRepository $enfantactiviteRepository, Request $request): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $em = $this->getDoctrine()->getManager();

        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $enfantactivite = $enfantactiviteRepository->findBy(['eglise' => $eglise, "etat" => 0]);

        return $this->render('enfantactivite/archiveenfantactivite.html.twig', [
                    'enfantactivite' => $enfantactivite,
        ]);
    }

    #[Route('/restaureenftactive/{id}', name: 'enfantactive_restaureenftactive')]
    public function restaure(Request $request, Enfantactivite $enfantactivite): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_ECODIM')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($this->isCsrfTokenValid('restaure' . $enfantactivite->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();

            $enfantactivite->setDeletedAt(NULL);
            $user = $this->getUser();
            $enfantactivite->setDeletedBy(NULL);
            $enfantactivite->setEtat("1");
            if ($request) {
                $this->addFlash('success', 'Restauration avec succès.');
            }
            $entityManager->flush();
        }

        return $this->redirectToRoute('enfantactivite_listeparticipant');
    }

    #[Route('/presence', name: 'presenceactivite', methods: ['POST', 'GET'])]
    public function presenceActivite(EnfantRepository $enfantRepository, Request $request, EcodimactiviteRepository $ecodimactiviteRepo, EnfantactiviteRepository $activiteRepo): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_ECODIM')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise()->getId();
        $user = $this->getUser();
        if ($request->isMethod('POST')) {
            $ecodimactivite = $request->request->get('ecodimactivitie');

            $idecodimactivite = $ecodimactiviteRepo->find($ecodimactivite);
            $tabpost = $request->request->get('tab');

            foreach ($tabpost as $value) {
                $em = $this->getDoctrine()->getManager();
                $idenfant = $enfantRepository->find($value);
                $enfantactivite = new Enfantactivite();

//                $enfantact= $activiteRepo->find($value);
//
//                $dql = $activiteRepo->findBy(['enfant' => $enfantactivite->getEnfant(),
//                    'ecodimactivite' => $enfantactivite->getEcodimactivite(),
//                ]);
//                if ($dql) {
//                    $this->addFlash('success', 'Enfant déjà participant à cette activité.');
//                    return $this->redirectToRoute('enfantactivite_presence', [], Response::HTTP_SEE_OTHER);
//                } else {

                $eglise = $this->getUser()->getEglise();
                $user = $this->getUser();
                $enfantactivite->setEnfant($idenfant);
                $enfantactivite->setEcodimactivite($idecodimactivite);
                $enfantactivite->setEglise($eglise);
                $enfantactivite->setEtat(1);
                $enfantactivite->setCreatedBy($this->getUser());
                $em->persist($enfantactivite);
                $em->flush();
//                }
            }
            return $this->redirectToRoute('enfantactivite_listeparticipant');
        } else {
            $enfant = $enfantRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL, "etatenfant" => 1, "editable" => 1]);
            $ecodim = $ecodimactiviteRepo->findBy(['eglise' => $eglise]);
            return $this->render('enfantactivite/presence.html.twig',
                            [
                                'enfant' => $enfant,
                                'ecoactivite' => $ecodim
            ]);
        }
    }

    #[Route('/participation', name: 'enfantactivite_participation', methods: ['GET', 'POST'])]
    public function participation(Request $request, EnfantactiviteRepository $enfantactiviteRepository, EcodimactiviteRepository $ecodimactiviteRepository, EnfantRepository $enfantRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_ECODIM')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $enfantactivite = new Enfantactivite();
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $enfantactivite->setCreatedBy($user);
        $enfant = $enfantRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL, "etatenfant" => 1, "editable" => 1]);
        $ecodimactivite = $ecodimactiviteRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL,]);
        $form = $this->createForm(EnfantactiviteType::class, $enfantactivite, ['enfant' => $enfant, 'ecodimactivite' => $ecodimactivite]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $enfantactivite = $form->getData();

//            // CHoix de l'enfant de l'activité à laquelle il va participer
            $dql = $enfantactiviteRepository->findBy(['enfant'=>$enfantactivite->getEnfant(), 'ecodimactivite' => $enfantactivite->getEcodimactivite()]);

            if ($dql) {
                $this->addFlash('success', 'Enfant déjà participant à cette activité.');
                return $this->redirectToRoute('enfantactivite_new', [], Response::HTTP_SEE_OTHER);
            } else {

                $enfantactivite->setEtat("1");
                $eglise = $this->getUser()->getEglise();
                $user = $this->getUser();
                $enfantactivite->setCreatedBy($user);
                $enfantactivite->setEglise($eglise);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($enfantactivite);
                $entityManager->flush();
            }
            return $this->redirectToRoute('enfantactivite_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('enfantactivite/participation.html.twig', [
                    'enfantactivite' => $enfantactivite,
                    'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'enfantactivite_show', methods: ['GET'])]
    public function show(Enfantactivite $enfantactivite): Response {
        if (!$this->isGranted('ROLE_RESPONSABLE_ECODIM')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        return $this->render('enfantactivite/show.html.twig', [
                    'enfantactivite' => $enfantactivite,
        ]);
    }

    #[Route('enfatact/{id}', name: 'enfantactivite_delete', methods: ['POST'])]
    public function delete(Request $request, Enfantactivite $enfantactivite): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_ECODIM')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($this->isCsrfTokenValid('delete' . $enfantactivite->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();

            $enfantactivite->setDeletedFromIp($this->GetIp());
            $enfantactivite->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $enfantactivite->setDeletedBy($user);
            $this->addFlash('danger', 'Suppression effectuée avec succès.');
            $entityManager->flush();
        }

        return $this->redirectToRoute('enfantactivite_index');
    }

}
