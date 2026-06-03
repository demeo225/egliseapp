<?php

namespace App\Controller;

use App\Entity\Detailcotisationcellule;
use App\Form\DetailcotisationcelluleType;
use App\Repository\CotisercelluleRepository;
use App\Repository\DetailcotisationcelluleRepository;
use App\Repository\FideleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Traits\ClientIp;

#[Route('/detailcotisationcellule')]
class DetailcotisationcelluleController extends AbstractController {

    use ClientIp;

    #[Route('/', name: 'app_detailcotisationcellule_index', methods: ['GET'])]
    public function index(DetailcotisationcelluleRepository $detailcotisationcelluleRepository): Response {

        return $this->render('detailcotisationcellule/index.html.twig', [
                    'detailcotisationcellules' => $detailcotisationcelluleRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_detailcotisationcellule_new', methods: ['GET', 'POST'])]
    public function new(Request $request, DetailcotisationcelluleRepository $detailcotisationcelluleRepository): Response {
        $detailcotisationcellule = new Detailcotisationcellule();
        $form = $this->createForm(DetailcotisationcelluleType::class, $detailcotisationcellule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $detailcotisationcelluleRepository->add($detailcotisationcellule, true);

            return $this->redirectToRoute('app_detailcotisationcellule_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('detailcotisationcellule/new.html.twig', [
                    'detailcotisationcellule' => $detailcotisationcellule,
                    'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_detailcotisationcellule_show', methods: ['GET'])]
    public function show(Detailcotisationcellule $detailcotisationcellule): Response {
        return $this->render('detailcotisationcellule/show.html.twig', [
                    'detailcotisationcellule' => $detailcotisationcellule,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_detailcotisationcellule_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Detailcotisationcellule $detailcotisationcellule, FideleRepository $fideleRepository, DetailcotisationcelluleRepository $detailcotisationcelluleRepository, CotisercelluleRepository $cotiserRepo): Response {

        $user = $this->getUser();
        $eglise = $this->getUser()->getEglise();
        $fidele = $fideleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $cotisercellule = $cotiserRepo->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $form = $this->createForm(DetailcotisationcelluleType::class, $detailcotisationcellule, ['fidele' => $fidele, 'cotisercellule' => $cotisercellule]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $detailcotisationcellule->setUpdatedFromIp($this->GetIp());

            $detailcotisationcellule->setUpdatedBy($user);
            $nature = $form['typeoff']->getData();

            if ($nature == 1) {
//                $id = $form['cotisercellule']->getData()->getId();
                $valeur = $form['ajout']->getData();
//                $idf = $form['fidele']->getData();
//
//                // On cumule le montant total dans une table Montantoff
//                $dql = $cotiserRepo->findBy(['fidele' => $idf]);
//
//               
                $dql = $detailcotisationcelluleRepository->findBy(['fidele' => $detailcotisationcellule->getFidele(), 'cotisationcellule' => $detailcotisationcellule->getCotisationcellule(), 'cotisercellule' => $detailcotisationcellule->getCotisercellule()]);
                $id = $dql[0]->getId();
                $activite = $cotiserRepo->findOneByCotisercellule($id);
                $mont = $activite->getMontantpayer();

                $reste = $activite->getReste();

                $j = 0;
                $r = 0;
                $r = $reste - $valeur;
                $j = $mont + $valeur;
                                                           

                $activite->setReste($r);
                $activite->setMontantpayer($j);

                $mont1 = $detailcotisationcellule->getMontantpayer();
                $restedetail = $detailcotisationcellule->getReste();
                $rested = $restedetail - $valeur;

                $mon = $valeur + $mont1;
                $detailcotisationcellule->setMontantpayer($mon);
                $detailcotisationcellule->setReste($rested);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($activite);
                $entityManager->persist($detailcotisationcellule);
                $this->getDoctrine()->getManager()->flush();
            } elseif ($nature == 0) {
                $valeur2 = $form['ajout']->getData();
                   $dql11 = $detailcotisationcelluleRepository->findBy(['fidele' => $detailcotisationcellule->getFidele(), 'cotisationcellule' => $detailcotisationcellule->getCotisationcellule()]);
                $id1 = $dql11[0]->getId();
                $j1 = 0;
            
                $activite1 = $cotiserRepo->findOneByCotisercellule($id1);
               
                $mont1 = $activite1->getMontantpayer();
                $restecotiser = $activite1->getReste();
                $j1 = $mont1 - $valeur2;
                $restecot = $restecotiser + $valeur2;
                $activite1->setMontantpayer($j1);
                $activite1->setReste($restecot);

                $mont2 = $detailcotisationcellule->getMontantpayer();
                $reste2 = $detailcotisationcellule->getReste();
                $rest2 = $reste2 + $valeur2;
                $mon1 = $mont2 - $valeur2;

                $detailcotisationcellule->setMontantpayer($mon1);
                $detailcotisationcellule->setReste($rest2);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($activite1);
                $entityManager->persist($detailcotisationcellule);
                $this->getDoctrine()->getManager()->flush();
            }



            return $this->redirectToRoute('app_detailcotisationcellule_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('detailcotisationcellule/edit.html.twig', [
                    'detailcotisationcellule' => $detailcotisationcellule,
                    'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_detailcotisationcellule_delete', methods: ['POST'])]
    public function delete(Request $request, Detailcotisationcellule $detailcotisationcellule, DetailcotisationcelluleRepository $detailcotisationcelluleRepository): Response {
        if ($this->isCsrfTokenValid('delete' . $detailcotisationcellule->getId(), $request->request->get('_token'))) {
            $detailcotisationcelluleRepository->remove($detailcotisationcellule, true);
        }

        return $this->redirectToRoute('app_detailcotisationcellule_index', [], Response::HTTP_SEE_OTHER);
    }

}
