<?php

namespace App\Controller;

use App\Entity\Cotiserzone;
use App\Entity\Detailcotisationzone;
use App\Entity\Soldezone;
use App\Form\CotiserzoneType;
use App\Repository\CotiserzoneRepository;
use App\Repository\DetailcotisationzoneRepository;
use App\Repository\FideleRepository;
use App\Repository\SoldezoneRepository;
use App\Repository\CotisationzoneRepository;
use App\Repository\ZoneRepository;
use App\Traits\ClientIp;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cotiserzone')]
class CotiserzoneController extends AbstractController {

    use ClientIp;

    #[Route('/', name: 'app_cotiserzone_index', methods: ['GET'])]
    public function index(CotiserzoneRepository $cotiserzoneRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_ZONE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $roles = $user->getRoles();
        $cotiserzone = $cotiserzoneRepository->findBy(['eglise' => $eglise, 'deletedAt' => NULL]);
        return $this->render('cotiserzone/index.html.twig', [
                    'cotiserzones' => $cotiserzone,
        ]);
    }

    #[Route('/detailzone', name: 'app_cotiserzone_detailzone', methods: ['GET'])]
    public function detailCotisation(DetailcotisationzoneRepository $detailRepo): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_ZONE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $detailcotisation = $detailRepo->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        return $this->render('cotiserzone/detailcotisation.html.twig', [
                    'details' => $detailcotisation,
        ]);
    }

    #[Route('/new', name: 'app_cotiserzone_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, ZoneRepository $zoneRepository, SoldezoneRepository $soldeRepo, CotiserzoneRepository $cotiserzoneRepository, FideleRepository $fideleRepository, CotisationzoneRepository $cotisationzoneRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_ZONE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $cotiserzone = new Cotiserzone();
        $eglise = $this->getUser()->getEglise();
                 $user = $this->getUser();
                    //Recuperer le groupe et les membres
            $zone = $zoneRepository->findOneByUser($user);
         if (!$zone) {
            $this->addFlash('warning', 'Vous ne disposez pas de zone/secteur à gérer.');
            return $this->redirectToRoute('app_cotiserzone_index');
        } 
        $fidele = $fideleRepository->findBy(['zone' => $zone, "deletedAt" => NULL, "etatfidele" => 1]);
        //$zone = $zoneRepository->findBy(['eglise' => $eglise, "user" => $user, "deletedAt" => NULL]);
        $cotisationzone = $cotisationzoneRepository->findBy(['zone' => $zone, "etatcotiser" => 1]);
        $form = $this->createForm(CotiserzoneType::class, $cotiserzone, [ 'fidele' => $fidele, 'cotisationzone' => $cotisationzone],);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) { 


            $idc = $form['cotisationzone']->getData();
            $idf = $form['fidele']->getData();
            $montant = $form['montantpayer']->getData();
            $date = $form['datecotiser']->getData();
            $idgpe = $zoneRepository->findOneByUser($user);
            $cotiserzone->setZone($idgpe);
            $dql = $cotiserzoneRepository->findBy(['fidele' => $cotiserzone->getFidele(), 'cotisationzone' => $cotiserzone->getCotisationzone(), 'zone' => $cotiserzone->getZone()]);

            if ($dql) {
                $cotiserzone = $form->getData();

                $id = $dql[0]->getId();
                $activite = $cotiserzoneRepository->findOneByCotiserzone($id);
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

                $dql2 = $soldeRepo->findBy(['zone' => $idgpe]);
                if ($dql2) {
                    $id = $dql2[0]->getId();
                    $activite = $soldeRepo->findOneBySoldeZone($id);
                    $mont = $activite->getMontant();
                    $j = 0;
                    $j = $mont + $montant;
                    $activite->setMontant($j);
                } else {

                    $montantSole = new Soldezone();
                    $montantSole->setMontant($montant);
                    $montantSole->setZone($idgpe);
                    $entityManager->persist($montantSole);
                }

                $detail2 = new Detailcotisationzone();
                $detail2->setFidele($idf);
                $detail2->setCotisationzone($idc);
                $detail2->setEglise($eglise);
                $detail2->setZone($zone);
                $detail2->setMontant($b1);
                $detail2->setMontantpayer($montant);
                $detail2->setCreatedBy($user);
                $detail2->setCreatedFromIp($this->GetIp());
                $detail2->setReste($a1);
                $detail2->setEtat('1');
                $detail2->setDatedetail($date);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($detail2);
                $entityManager->flush();
            } else {


                $cotiserzone = $form->getData();

                $cotiser1 = $cotisationzoneRepository->findOneByCotisationzone($idc);
                $payer = $cotiser1->getMontant();
//                    $montant = $cotiser->getMontant();
                $restepayer = $payer - $montant;

                $cotiserzone->setReste($restepayer);
                $cotiserzone->setCreatedFromIp($this->GetIp());
//                    $cotiserzone->setEtatcotiser("1");
                $cotiserzone->setEglise($eglise);
                $cotiserzone->setCreatedBy($user);
                $cotiserzone->setCreatedFromIp($this->GetIp());

                $dql2 = $soldeRepo->findBy(['zone' => $idgpe]);
                if ($dql2) {
                    $id = $dql2[0]->getId();
                    $activite = $soldeRepo->findOneBySoldeZone($id);
                    $mont = $activite->getMontant();
                    $j = 0;
                    $j = $mont + $montant;
                    $activite->setMontant($j);
                } else {

                    $montantSole = new Soldezone();
                    $montantSole->setMontant($montant);
                    $montantSole->setZone($idgpe);
                    $entityManager->persist($montantSole);
                }

                $detail = new Detailcotisationzone();
                $detail->setFidele($idf);
                $detail->setCotisationzone($idc);
                $detail->setEglise($eglise);
                $detail->setMontant($payer);
                $detail->setMontantpayer($montant);
                $detail->setCreatedBy($user);
                $detail->setCreatedFromIp($this->GetIp());
                $detail->setReste($restepayer);
                $detail->setDatedetail($date);
                $detail->setEtat('1');

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($detail);
                $entityManager->persist($cotiserzone);
                $entityManager->flush();
            }

            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'app_cotiserzone_new' : 'app_cotiserzone_index';
            if ($nextAction) {
                $this->addFlash('success', 'Enregistrement avec succès.');
            }

            return $this->redirectToRoute($nextAction);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('cotiserzone/new.html.twig', [
                    'cotiserzone' => $cotiserzone,
                    'zone' => $zone,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

      
/**
 * Gestion du solde du département
 */
private function gestionSoldeZone(SoldezoneRepository $soldeRepo, EntityManagerInterface $entityManager, $zone, int $montant): void
{
    $soldeExistant = $soldeRepo->findOneBy(['zone' => $zone]);
    
    if ($soldeExistant) {
        $nouveauSolde = $soldeExistant->getMontant() + $montant;
        $soldeExistant->setMontant($nouveauSolde);
      //  $soldeExistant->setUpdatedAt(new \DateTime());
    } else {
        $nouveauSolde = new Soldezone();
        $nouveauSolde->setMontant($montant);
        $nouveauSolde->setZone($zone);
        $entityManager->persist($nouveauSolde);
    }
}


#[Route('/get-montant-cotisation-zone', name: 'get_montant_cotisation_zone', methods: ['POST'])]
public function getMontantCotisation(Request $request, CotisationzoneRepository $cotisationzoneRepository): JsonResponse
{
    $id = $request->request->get('id');
    $cotisation = $cotisationzoneRepository->find($id);
    
    if ($cotisation) {
        return $this->json([
    'montant' => (float) $cotisation->getMontant()
]);
    }
    
    return $this->json(['montant' => 0]);
}

#[Route('/get-deja-paye-fidele-zone', name: 'get_deja_paye_fidele_zone', methods: ['POST'])]
public function getDejaPayeFidele(
    Request $request, 
    CotisationzoneRepository $cotisationzoneRepository,
    CotiserzoneRepository $cotiserzoneRepository
): JsonResponse
{
    $cotisationId = $request->request->get('cotisation_id');
    $fideleId = $request->request->get('fidele_id');
    
    if ($cotisationId && $fideleId) {
        $totalPaye = $cotiserzoneRepository->createQueryBuilder('c')
            ->select('SUM(c.montantpayer) as total')
            ->where('c.fidele = :fidele')
            ->andWhere('c.cotisationzone = :cotisation')
            ->andWhere('c.deletedAt IS NULL')
            ->setParameter('fidele', $fideleId)
            ->setParameter('cotisation', $cotisationId)
            ->getQuery()
            ->getSingleScalarResult() ?? 0;
        
                return $this->json(['deja_paye' => (float) $totalPaye]);
    }
    
    return $this->json(['deja_paye' => 0]);
} 

    #[Route('/{id}', name: 'app_cotiserzone_show', methods: ['GET'])]
    public function show(Cotiserzone $cotiserzone): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_ZONE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        return $this->render('cotiserzone/show.html.twig', [
                    'cotiserzone' => $cotiserzone,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_cotiserzone_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Cotiserzone $cotiserzone, CotiserzoneRepository $cotiserzoneRepository, ZoneRepository $zoneRepository, FideleRepository $fideleRepository, CotisationzoneRepository $cotisationzoneRepository): Response {
//        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
//        if (!$this->isGranted('ROLE_RESPONSABLE_ZONE')) {
//            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
//        }

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $this->denyAccessUnlessGranted('cotiserzone_edit', $cotiserzone);

        $eglise = $this->getUser()->getEglise();
           $user = $this->getUser();
                    //Recuperer le groupe et les membres
            $zone = $zoneRepository->findOneByUser($user);
         if (!$zone) {
            $this->addFlash('warning', 'Vous ne disposez pas de zone/secteur à gérer.');
            return $this->redirectToRoute('app_cotiserzone_index');
        } 
        $fidele = $fideleRepository->findBy(['zone' => $zone, "deletedAt" => NULL, "etatfidele" => 1]);
      //  $zone = $zoneRepository->findBy(['eglise' => $eglise, "user" => $user, "deletedAt" => NULL]);
        $cotisationzone = $cotisationzoneRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $form = $this->createForm(CotiserzoneType::class, $cotiserzone, ['fidele' => $fidele, 'cotisationzone' => $cotisationzone],);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $id = $form['cotisationzone']->getData();

            $cotise2 = $cotisationzoneRepository->findOneByCotisationzone($id);
            if ($cotise2) {
                $a = 0;
                $mont = $cotise2->getMontant();
                $montant = $form['montantpayer']->getData();
                $a = $mont - $montant;
                $cotiserzone->setMontantpayer($montant);
                $cotiserzone->setReste($a);
                $cotiserzone->setUpdatedFromIp($this->GetIp());
                $cotiserzone->setUpdatedBy($user);
                $this->getDoctrine()->getManager()->flush();
            }

            return $this->redirectToRoute('app_cotiserzone_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('cotiserzone/edit.html.twig', [
                    'cotiserzone' => $cotiserzone,
                    'form' => $form->createView(),
        ]);
    }

    #[Route('cotiserzone/{id}', name: 'app_cotiserzone_delete', methods: ['POST'])]
    public function delete(Request $request, Cotiserzone $cotiserzone, CotiserzoneRepository $cotiserzoneRepository): Response {
        if ($this->isCsrfTokenValid('delete' . $cotiserzone->getId(), $request->request->get('_token'))) {
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

            $this->denyAccessUnlessGranted('cotiserzone_delete', $cotiserzone);
            $entityManager = $this->getDoctrine()->getManager();

            $cotiserzone->setDeletedFromIp($this->GetIp());
            $cotiserzone->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $cotiserzone->setDeletedBy($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_cotiserzone_index', [], Response::HTTP_SEE_OTHER);
    }

}
