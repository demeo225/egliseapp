<?php

namespace App\Controller;

use App\Entity\Cotiserfamille;
use App\Entity\Detailcotisationfamille;
use App\Entity\Soldefamille;
use App\Form\CotiserfamilleType;
use App\Repository\CotisationfamilleRepository;
use App\Repository\CotiserfamilleRepository;
use App\Repository\DetailcotisationfamilleRepository;
use App\Repository\FamilleRepository;
use App\Repository\FideleRepository;
use App\Repository\SoldefamilleRepository;
use App\Traits\ClientIp;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cotiserfamille')]
class CotiserfamilleController extends AbstractController {

    use ClientIp;

    #[Route('/', name: 'cotiserfamille_index', methods: ['GET'])]
    public function index(CotiserfamilleRepository $cotiserfamilleRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_FAMILLE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $cotiserfamille = $cotiserfamilleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        return $this->render('cotiserfamille/index.html.twig', [
                    'cotiserfamilles' => $cotiserfamille,
        ]);
    }

    #[Route('/new', name: 'cotiserfamille_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FamilleRepository $familleRepository, SoldefamilleRepository $soldeRepo, CotiserfamilleRepository $cotiserfamilleRepository, FideleRepository $fideleRepository, CotisationfamilleRepository $cotisationfamilleRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_FAMILLE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $cotiserfamille = new Cotiserfamille();
        $eglise = $this->getUser()->getEglise();
            $user = $this->getUser();
                    //Recuperer le groupe et les membres
            $famille = $familleRepository->findOneByUser($user);
         if (!$famille) {
            $this->addFlash('warning', 'Vous ne disposez pas de Famille à gérer.');
            return $this->redirectToRoute('cotiserfamille_index');
        }
        $fidele = $fideleRepository->findBy(['famille' => $famille, "deletedAt" => NULL, "etatfidele" => 1]);
        $famille = $familleRepository->findOneByUser($user);
        $cotisationfamille = $cotisationfamilleRepository->findBy(['famille' => $famille, "etatcotiser" => 1]);
        $form = $this->createForm(CotiserfamilleType::class, $cotiserfamille, ['fidele' => $fidele, 'cotisationfamille' => $cotisationfamille],);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
 
            $idc = $form['cotisationfamille']->getData();
            $idf = $form['fidele']->getData();
            $idgpe = $familleRepository->findOneByUser($user);
            $date = $form['datecotiser']->getData();
            $montant = $form['montantpayer']->getData();
            $cotiserfamille->setFamille($idgpe);
            $dql = $cotiserfamilleRepository->findBy(['fidele' => $cotiserfamille->getFidele(), 'cotisationfamille' => $cotiserfamille->getCotisationfamille(), 'famille' => $cotiserfamille->getFamille()]);

            if ($dql) {
                $cotiserfamille = $form->getData();

                $id = $dql[0]->getId();
                $activite = $cotiserfamilleRepository->findOneByCotiserfamille($id);
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

                //On crée le solde si inexistant et on incrmente solde si existant
                  $dql2 = $soldeRepo->findBy(['famille' => $idgpe]);
                if ($dql2) {
                    $id = $dql2[0]->getId();
                    $activite = $soldeRepo->findOneBySoldeFamille($id);
                    $mont = $activite->getMontant();
                    $j = 0;
                    $j = $mont + $montant;
                    $activite->setMontant($j);
                } else {

                    $montantSole = new Soldefamille();
                    $montantSole->setMontant($montant);
                    $montantSole->setFamille($idgpe);
                    $entityManager->persist($montantSole);
                }
                
                $detail2 = new Detailcotisationfamille();
                $detail2->setFidele($idf);
                $detail2->setCotisationfamille($idc);
                $detail2->setFamille($user->getFamille());
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


                $cotiserfamille = $form->getData();
                $cotiser1 = $cotisationfamilleRepository->findOneByCotisationfamille($idc);
                $payer = $cotiser1->getMontant();
                $restepayer = $payer - $montant;
                $cotiserfamille->setReste($restepayer);
                $cotiserfamille->setCreatedFromIp($this->GetIp());
                $cotiserfamille->setEglise($eglise);
                $cotiserfamille->setCreatedBy($user);
                $cotiserfamille->setCreatedFromIp($this->GetIp());
                
                                //On crée le solde si inexistant et on incrmente solde si existant
                  $dql2 = $soldeRepo->findBy(['famille' => $idgpe]);
                if ($dql2) {
                    $id = $dql2[0]->getId();
                    $activite = $soldeRepo->findOneBySoldeFamille($id);
                    $mont = $activite->getMontant();
                    $j = 0;
                    $j = $mont + $montant;
                    $activite->setMontant($j);
                } else {

                    $montantSole = new Soldefamille();
                    $montantSole->setMontant($montant);
                    $montantSole->setFamille($idgpe);
                    $entityManager->persist($montantSole);
                }

                $detail = new Detailcotisationfamille();
                $detail->setFidele($idf);
                $detail->setCotisationfamille($idc);
                $detail->setEglise($eglise);
                $detail->setMontant($payer);
                $detail->setMontantpayer($montant);
                $detail->setCreatedBy($user);
                $detail->setCreatedFromIp($this->GetIp());
                $detail->setReste($restepayer);
                $detail->setDatedetail($date);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($detail);

                $entityManager->persist($cotiserfamille);
                $entityManager->flush();
            }

            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'cotiserfamille_new' : 'cotiserfamille_index';
            if ($nextAction) {
                $this->addFlash('success', 'Enregistrement avec succès.');
            }

            return $this->redirectToRoute($nextAction);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('cotiserfamille/new.html.twig', [
                    'cotiserfamille' => $cotiserfamille,
                    'famille' => $famille,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    #[Route('/detailfamille', name: 'cotiserfamille_detailfamille', methods: ['GET'])]
    public function detailCotisation(DetailcotisationfamilleRepository $detailRepo): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_FAMILLE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $detailcotisation = $detailRepo->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        return $this->render('cotiserfamille/detailcotisation.html.twig', [
                    'details' => $detailcotisation,
        ]);
    }

    
/**
 * Gestion du solde du département
 */
private function gestionSoldeFamille(SoldefamilleRepository $soldeRepo, EntityManagerInterface $entityManager, $famille, int $montant): void
{
    $soldeExistant = $soldeRepo->findOneBy(['famille' => $famille]);
    
    if ($soldeExistant) {
        $nouveauSolde = $soldeExistant->getMontant() + $montant;
        $soldeExistant->setMontant($nouveauSolde);
      //  $soldeExistant->setUpdatedAt(new \DateTime());
    } else {
        $nouveauSolde = new Soldefamille();
        $nouveauSolde->setMontant($montant);
        $nouveauSolde->setFamille($famille);
        $entityManager->persist($nouveauSolde);
    }
}


#[Route('/get-montant-cotisation-famille', name: 'get_montant_cotisation_famille', methods: ['POST'])]
public function getMontantCotisation(Request $request, CotisationfamilleRepository $cotisationfamilleRepository): JsonResponse
{
    $id = $request->request->get('id');
    $cotisation = $cotisationfamilleRepository->find($id);
    
    if ($cotisation) {
        return $this->json([
    'montant' => (float) $cotisation->getMontant()
]);
    }
    
    return $this->json(['montant' => 0]);
}

#[Route('/get-deja-paye-fidele-famille', name: 'get_deja_paye_fidele_famille', methods: ['POST'])]
public function getDejaPayeFidele(
    Request $request, 
    CotisationfamilleRepository $cotisationfamilleRepository,
    CotiserfamilleRepository $cotiserfamilleRepository
): JsonResponse
{
    $cotisationId = $request->request->get('cotisation_id');
    $fideleId = $request->request->get('fidele_id');
    
    if ($cotisationId && $fideleId) {
        $totalPaye = $cotiserfamilleRepository->createQueryBuilder('c')
            ->select('SUM(c.montantpayer) as total')
            ->where('c.fidele = :fidele')
            ->andWhere('c.cotisationfamille = :cotisation')
            ->andWhere('c.deletedAt IS NULL')
            ->setParameter('fidele', $fideleId)
            ->setParameter('cotisation', $cotisationId)
            ->getQuery()
            ->getSingleScalarResult() ?? 0;
        
                return $this->json(['deja_paye' => (float) $totalPaye]);
    }
    
    return $this->json(['deja_paye' => 0]);
} 


    #[Route('cotiserfamill/{id}', name: 'cotiserfamille_show', methods: ['GET'])]
    public function show(Cotiserfamille $cotiserfamille): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_FAMILLE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        return $this->render('cotiserfamille/show.html.twig', [
                    'cotiserfamille' => $cotiserfamille,
        ]);
    }

    #[Route('/{id}/edit', name: 'cotiserfamille_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Cotiserfamille $cotiserfamille, FamilleRepository $familleRepository, FideleRepository $fideleRepository, CotisationfamilleRepository $cotisationfamilleRepository): Response {


        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $this->denyAccessUnlessGranted('cotiserfamille_edit', $cotiserfamille);

        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $fidele = $fideleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL, "etatfidele" => 1]);
        $famille = $familleRepository->findBy(['eglise' => $eglise, "user" => $user, "deletedAt" => NULL]);
        $cotisationfamille = $cotisationfamilleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $form = $this->createForm(CotiserfamilleType::class, $cotiserfamille, ['famille' => $famille, 'fidele' => $fidele, 'cotisationfamille' => $cotisationfamille],);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $id = $form['cotisationfamille']->getData();

            $cotise2 = $cotisationfamilleRepository->findOneByCotisationfamille($id);
            if ($cotise2) {
                $a = 0;
                $mont = $cotise2->getMontant();

                $montant = $form['montantpayer']->getData();
//            );
                $a = $mont - $montant;

                $cotiserfamille->setMontantpayer($montant);
                $cotiserfamille->setReste($a);
                $cotiserfamille->setUpdatedFromIp($this->GetIp());
                $cotiserfamille->setUpdatedBy($user);
                $this->getDoctrine()->getManager()->flush();
            }

            return $this->redirectToRoute('cotiserfamille_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('cotiserfamille/edit.html.twig', [
                    'cotiserfamille' => $cotiserfamille,
                    'form' => $form->createView(),
        ]);
    }

    #[Route('cotiserfamill/{id}', name: 'cotiserfamille_delete', methods: ['POST'])]
    public function delete(Request $request, Cotiserfamille $cotiserfamille): Response {
        if ($this->isCsrfTokenValid('delete' . $cotiserfamille->getId(), $request->request->get('_token'))) {

            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
            $this->denyAccessUnlessGranted('cotiserfamille_delete', $cotiserfamille);
            $entityManager = $this->getDoctrine()->getManager();

            $cotiserfamille->setDeletedFromIp($this->GetIp());
            $cotiserfamille->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $cotiserfamille->setDeletedBy($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('cotiserfamille_index');
    }

}
