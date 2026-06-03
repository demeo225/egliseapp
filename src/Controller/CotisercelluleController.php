<?php

namespace App\Controller;

use App\Entity\Cotisercellule;
use App\Entity\Detailcotisationcellule;
use App\Entity\Solecellule;
use App\Form\CotisercelluleType;
use App\Repository\CelluleRepository;
use App\Repository\CotisationcelluleRepository;
use App\Repository\CotisercelluleRepository;
use App\Repository\DetailcotisationcelluleRepository;
use App\Repository\FideleRepository;
use App\Repository\SolecelluleRepository;
use App\Traits\ClientIp;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cotisercellule')]
class CotisercelluleController extends AbstractController {

    use ClientIp;

    #[Route('/', name: 'cotisercellule_index', methods: ['GET'])]
    public function index(CotisercelluleRepository $cotisercelluleRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_CELLULE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $cotisercellule = $cotisercelluleRepository->findBy(['eglise' => $eglise, 'deletedAt' => NULL]);
        return $this->render('cotisercellule/index.html.twig', [
                    'cotisercellules' => $cotisercellule,
        ]);
    }

    #[Route('/new', name: 'cotisercellule_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, CelluleRepository $celluleRepository, SolecelluleRepository $soldeRepo, CotisercelluleRepository $cotisercelluleRepository, FideleRepository $fideleRepository, CotisationcelluleRepository $cotisationcelluleRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_CELLULE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $cotisercellule = new Cotisercellule();
        $eglise = $this->getUser()->getEglise();
         $user = $this->getUser();
          $cellule = $celluleRepository->findOneByUser($user);
         if (!$cellule) {
            $this->addFlash('warning', 'Vous ne disposez pas de cellule à gérer.');
            return $this->redirectToRoute('cotisercellule_index');
        }
        $fidele = $fideleRepository->findBy(['cellule' => $cellule, "deletedAt" => NULL, "etatfidele" => 1]);
          $cellule = $celluleRepository->findOneByUser($user);
        $cotisationcellule = $cotisationcelluleRepository->findBy(['cellule' => $cellule, "etatcotiser" => 1]);
        $form = $this->createForm(CotisercelluleType::class, $cotisercellule, ['fidele' => $fidele, 'cotisationcellule' => $cotisationcellule],);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $user = $this->getUser();
            $idc = $form['cotisationcellule']->getData();
            $idf = $form['fidele']->getData();
            $montant = $form['montantpayer']->getData();
            $date = $form['datecotiser']->getData();
            $idgpe = $celluleRepository->findOneByUser($user);
            $cotisercellule->setCellule($idgpe);
            $dql = $cotisercelluleRepository->findBy(['fidele' => $cotisercellule->getFidele(), 'cotisationcellule' => $cotisercellule->getCotisationcellule(), 'cellule' => $cotisercellule->getCellule()]);

            if ($dql) {
                $cotisercellule = $form->getData();

                $id = $dql[0]->getId();
                $activite = $cotisercelluleRepository->findOneByCotisercellule($id);
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

                
                             // SI le solde n'existait pas, on le crée avec le montant entré
                // Si le solde existe dejà, on ajoute le montant entré
                // On soustrait le même montant du solde ou on met le montant avec -montant

                $dql2 = $soldeRepo->findBy(['cellule' => $idgpe]);
                if ($dql2) {
                    $id = $dql2[0]->getId();
                    $activite = $soldeRepo->findOneBySoldeCellule($id);
                    $mont = $activite->getMontant();
                    $j = 0;
                    $j = $mont + $montant;
                    $activite->setMontant($j);
                } else {

                    $montantSole = new Solecellule();
                    $montantSole->setMontant($montant);
                    $montantSole->setCellule($idgpe);
                    $entityManager->persist($montantSole);
                }

                
                $detail2 = new Detailcotisationcellule();
                $detail2->setFidele($idf);
                $detail2->setCotisationcellule($idc);
                $detail2->setCellule($idgpe);
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
            } else {


                $cotisercellule = $form->getData();
                $cotiser1 = $cotisationcelluleRepository->findOneByCotisationcellule($idc);
                $payer = $cotiser1->getMontant();
//                    $montant = $cotiser->getMontant();
                $restepayer = $payer - $montant;

                $cotisercellule->setReste($restepayer);
                $cotisercellule->setCreatedFromIp($this->GetIp());
//                    $cotisercellule->setEtatcotiser("1");
                $cotisercellule->setEglise($eglise);
                $cotisercellule->setCreatedBy($user);
                $cotisercellule->setCreatedFromIp($this->GetIp());

                $detail = new Detailcotisationcellule();
                $detail->setFidele($idf);
                $detail->setCotisationcellule($idc);
                $detail->setCellule($idgpe);
                $detail->setEglise($eglise);
                $detail->setMontant($payer);
                $detail->setMontantpayer($montant);
                $detail->setCreatedBy($user);
                $detail->setCreatedFromIp($this->GetIp());
                $detail->setReste($restepayer);
                $detail->setDatedetail($date);

                             // SI le solde n'existait pas, on le crée avec le montant entré
                // Si le solde existe dejà, on ajoute le montant entré
                // On soustrait le même montant du solde ou on met le montant avec -montant

                $dql2 = $soldeRepo->findBy(['cellule' => $idgpe]);
                if ($dql2) {
                    $id = $dql2[0]->getId();
                    $activite = $soldeRepo->findOneBySoldeCellule($id);
                    $mont = $activite->getMontant();
                    $j = 0;
                    $j = $mont + $montant;
                    $activite->setMontant($j);
                } else {

                    $montantSole = new Solecellule();
                    $montantSole->setMontant($montant);
                    $montantSole->setCellule($idgpe);
                    $entityManager->persist($montantSole);
                }

   
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($detail);

                $entityManager->persist($cotisercellule);
                $entityManager->flush();
            }
            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'cotisercellule_new' : 'cotisercellule_index';
            if ($nextAction) {
                $this->addFlash('success', 'Enregistrement avec succès.');
            }

            return $this->redirectToRoute($nextAction);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('cotisercellule/new.html.twig', [
                    'cotisercellule' => $cotisercellule,
                    'cellule' => $cellule,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }


    

      
/**
 * Gestion du solde du département
 */
private function gestionSoleCellule(SolecelluleRepository $soldeRepo, EntityManagerInterface $entityManager, $cellule, int $montant): void
{
    $soldeExistant = $soldeRepo->findOneBy(['cellule' => $cellule]);
    
    if ($soldeExistant) {
        $nouveauSolde = $soldeExistant->getMontant() + $montant;
        $soldeExistant->setMontant($nouveauSolde);
      //  $soldeExistant->setUpdatedAt(new \DateTime());
    } else {
        $nouveauSolde = new Soldecellule();
        $nouveauSolde->setMontant($montant);
        $nouveauSolde->setCellule($cellule);
        $entityManager->persist($nouveauSolde);
    }
}


#[Route('/get-montant-cotisation-cellule', name: 'get_montant_cotisation_cellule', methods: ['POST'])]
public function getMontantCotisation(Request $request, CotisationcelluleRepository $cotisationcelluleRepository): JsonResponse
{
    $id = $request->request->get('id');
    $cotisation = $cotisationcelluleRepository->find($id);
    
    if ($cotisation) {
        return $this->json([
    'montant' => (float) $cotisation->getMontant()
]);
    }
    
    return $this->json(['montant' => 0]);
}

#[Route('/get-deja-paye-fidele-cellule', name: 'get_deja_paye_fidele_cellule', methods: ['POST'])]
public function getDejaPayeFidele(
    Request $request, 
    CotisationcelluleRepository $cotisationcelluleRepository,
    CotisercelluleRepository $cotisercelluleRepository
): JsonResponse
{
    $cotisationId = $request->request->get('cotisation_id');
    $fideleId = $request->request->get('fidele_id');
    
    if ($cotisationId && $fideleId) {
        $totalPaye = $cotisercelluleRepository->createQueryBuilder('c')
            ->select('SUM(c.montantpayer) as total')
            ->where('c.fidele = :fidele')
            ->andWhere('c.cotisationcellule = :cotisation')
            ->andWhere('c.deletedAt IS NULL')
            ->setParameter('fidele', $fideleId)
            ->setParameter('cotisation', $cotisationId)
            ->getQuery()
            ->getSingleScalarResult() ?? 0;
        
                return $this->json(['deja_paye' => (float) $totalPaye]);
    }
    
    return $this->json(['deja_paye' => 0]);
} 

    #[Route('/detailcellule', name: 'cotisercellule_detailcellule', methods: ['GET'])]
    public function detailCotisation(DetailcotisationcelluleRepository $detailRepo): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_CELLULE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $detailcotisation = $detailRepo->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        return $this->render('cotisercellule/detailcotisation.html.twig', [
                    'details' => $detailcotisation,
        ]);
    }

    #[Route('/{id}', name: 'cotisercellule_show', methods: ['GET'])]
    public function show(Cotisercellule $cotisercellule): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_CELLULE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }

        return $this->render('cotisercellule/show.html.twig', [
                    'cotisercellule' => $cotisercellule,
        ]);
    }

    #[Route('/{id}/edit', name: 'cotisercellule_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Cotisercellule $cotisercellule, CotisercelluleRepository $cotisercelluleRepository, CelluleRepository $celluleRepository, FideleRepository $fideleRepository, CotisationcelluleRepository $cotisationcelluleRepository): Response {


        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $this->denyAccessUnlessGranted('cotisercellule_edit', $cotisercellule);

        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $fidele = $fideleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL, "etatfidele" => 1]);
        $cellule = $celluleRepository->findBy(['eglise' => $eglise, "user" => $user, "deletedAt" => NULL]);
        $cotisationcellule = $cotisationcelluleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $form = $this->createForm(CotisercelluleType::class, $cotisercellule, ['cellule' => $cellule, 'fidele' => $fidele, 'cotisationcellule' => $cotisationcellule],);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $id = $form['cotisationcellule']->getData();

            $cotise2 = $cotisationcelluleRepository->findOneByCotisationcellule($id);
            if ($cotise2) {
                $a = 0;
                $mont = $cotise2->getMontant();

                $montant = $form['montantpayer']->getData();
//            );
                $a = $mont - $montant;

                $cotisercellule->setMontantpayer($montant);
                $cotisercellule->setReste($a);

                $cotisercellule->setUpdatedFromIp($this->GetIp());
                $cotisercellule->setUpdatedBy($user);
                $this->getDoctrine()->getManager()->flush();
            }
            return $this->redirectToRoute('cotisercellule_index');
        }

        return $this->render('cotisercellule/edit.html.twig', [
                    'cotisercellule' => $cotisercellule,
                    'form' => $form->createView(),
        ]);
    }

    #[Route('cotisercellule/{id}', name: 'cotisercellule_delete', methods: ['POST'])]
    public function delete(Request $request, Cotisercellule $cotisercellule): Response {
        if ($this->isCsrfTokenValid('delete' . $cotisercellule->getId(), $request->request->get('_token'))) {


            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

            $this->denyAccessUnlessGranted('cotisercellule_delete', $cotisercellule);

            $entityManager = $this->getDoctrine()->getManager();

            $cotisercellule->setDeletedFromIp($this->GetIp());
            $cotisercellule->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $cotisercellule->setDeletedBy($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('cotisercellule_index');
    }

}
