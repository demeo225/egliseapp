<?php

namespace App\Controller;

use App\Entity\Cotisergroupe;
use App\Entity\Detailcotisationgroupe;
use App\Entity\Soldegroupe;
use App\Form\CotisergroupeType;
use App\Repository\CotisationgroupeRepository;
use App\Repository\CotisergroupeRepository;
use App\Repository\DetailcotisationgroupeRepository;
use App\Repository\FideleRepository;
use App\Repository\GroupeRepository;
use App\Repository\SoldegroupeRepository;
use App\Traits\ClientIp;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cotisergroupe')]
class CotisergroupeController extends AbstractController {
 
    use ClientIp;

    #[Route('/', name: 'cotisergroupe_index', methods: ['GET'])]
    public function index(CotisergroupeRepository $cotisergroupeRepository, SoldegroupeRepository $soldegroupeRepo): Response {

        if (!$this->isGranted('ROLE_RESPONSABLE_GROUPE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $cotisergroupe = $cotisergroupeRepository->findBy(['eglise' => $eglise, 'deletedAt' => NULL]);
        return $this->render('cotisergroupe/index.html.twig', [
                    'cotisergroupes' => $cotisergroupe,
        ]);
    }

    #[Route('/new', name: 'cotisergroupe_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, GroupeRepository $groupeRepository, SoldegroupeRepository $soldeRepo, CotisergroupeRepository $cotisergroupeRepository, FideleRepository $fideleRepository, CotisationgroupeRepository $cotisationgroupeRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_GROUPE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $cotisergroupe = new Cotisergroupe();
        $eglise = $this->getUser()->getEglise();
                 $user = $this->getUser();
                    //Recuperer le groupe et les membres
            $groupe = $groupeRepository->findOneByUser($user);
         if (!$groupe) {
            $this->addFlash('warning', 'Vous ne disposez pas de sous-groupe à gérer.');
            return $this->redirectToRoute('cotisergroupe_index');
        }

              $idGroupe = $groupe->getId();
        $fidele = $fideleRepository->findFidelesByGroupe($idGroupe);
        
        $groupe = $groupeRepository->findOneByUser($user);
        $cotisationgroupe = $cotisationgroupeRepository->findBy([ "deletedAt" => NULL, "etatcotiser" => 1]);
        $form = $this->createForm(CotisergroupeType::class, $cotisergroupe, ['fidele' => $fidele, 'cotisationgroupe' => $cotisationgroupe],);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $idc = $form['cotisationgroupe']->getData();
            $idgpe = $groupeRepository->findOneByUser($user);
            $idf = $form['fidele']->getData();
            $montant = $form['montantpayer']->getData();
            $date = $form['datecotiser']->getData();
            $cotisergroupe->setGroupe($idgpe);
            $dql = $cotisergroupeRepository->findBy(['fidele' => $cotisergroupe->getFidele(), 'cotisationgroupe' => $cotisergroupe->getCotisationgroupe(), 'groupe' => $cotisergroupe->getGroupe()]);

            if ($dql) {
                $cotisergroupe = $form->getData();

                $id = $dql[0]->getId();
                $activite = $cotisergroupeRepository->findOneByCotisergroupe($id);
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

                $dql2 = $soldeRepo->findBy(['groupe' => $idgpe]);
                if ($dql2) {
                    $id = $dql2[0]->getId();
                    $activite = $soldeRepo->findOneBySoldeGroupe($id);
                    $mont = $activite->getMontant();
                    $j = 0;
                    $j = $mont + $montant;
                    $activite->setMontant($j);
                } else {

                    $montantSole = new Soldegroupe();
                    $montantSole->setMontant($montant);
                    $montantSole->setGroupe($idgpe);
                    $entityManager->persist($montantSole);
                }

                $detail2 = new Detailcotisationgroupe();
                $detail2->setFidele($idf);
                $detail2->setCotisationgroupe($idc);
                $detail2->setGroupe($idgpe);
                $detail2->setEglise($eglise);
                $detail2->setMontant($b1);
                $detail2->setMontantpayer($montant);
                $detail2->setCreatedBy($user);
                $detail2->setCreatedFromIp($this->GetIp());
                $detail2->setReste($a1);
                $detail2->setDatecotiser($date);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($detail2);
                $entityManager->flush();
            } else {


                $cotisergroupe = $form->getData();
                $cotiser1 = $cotisationgroupeRepository->findOneByCotisationgroupe($idc);
                $payer = $cotiser1->getMontant();
//                    $montant = $cotiser->getMontant();
                $restepayer = $payer - $montant;

                $cotisergroupe->setReste($restepayer);
                $cotisergroupe->setCreatedFromIp($this->GetIp());
//                    $cotisergroupe->setEtatcotiser("1");
                $cotisergroupe->setEglise($eglise);
                $cotisergroupe->setCreatedBy($user);
                $cotisergroupe->setCreatedFromIp($this->GetIp());

                $dql2 = $soldeRepo->findBy(['groupe' => $idgpe]);
                if ($dql2) {
                    $id = $dql2[0]->getId();
                    $activite = $soldeRepo->findOneBySoldeGroupe($id);
                    $mont = $activite->getMontant();
                    $j = 0;
                    $j = $mont + $montant;
                    $activite->setMontant($j);
                } else {

                    $montantSole = new Soldegroupe();
                    $montantSole->setMontant($montant);
                    $montantSole->setGroupe($idgpe);
                    $entityManager->persist($montantSole);
                }

                $detail = new Detailcotisationgroupe();
                $detail->setFidele($idf);
                $detail->setCotisationgroupe($idc);
                $detail->setGroupe($idgpe);
                $detail->setEglise($eglise);
                $detail->setMontant($payer);
                $detail->setMontantpayer($montant);
                $detail->setCreatedBy($user);
                $detail->setCreatedFromIp($this->GetIp());
                $detail->setReste($restepayer);
                $detail->setDatecotiser($date);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($detail);

                $entityManager->persist($cotisergroupe);
                $entityManager->flush();
            }

            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'cotisergroupe_new' : 'cotisergroupe_index';
            if ($nextAction) {
                $this->addFlash('success', 'Enregistrement avec succès.');
            }

            return $this->redirectToRoute($nextAction);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('cotisergroupe/new.html.twig', [
                    'cotisergroupe' => $cotisergroupe,
                    'groupe' => $groupe,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    #[Route('/detailgroupe', name: 'cotisergroupe_detailgroupe', methods: ['GET'])]
    public function detailCotisation(DetailcotisationgroupeRepository $detailRepo): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_GROUPE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $detailcotisation = $detailRepo->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        return $this->render('cotisergroupe/detailcotisation.html.twig', [
                    'details' => $detailcotisation,
        ]);
    }

    #[Route('/{id}', name: 'cotisergroupe_show', methods: ['GET'])]
    public function show(Cotisergroupe $cotisergroupe): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_GROUPE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        return $this->render('cotisergroupe/show.html.twig', [
                    'cotisergroupe' => $cotisergroupe,
        ]);
    }

    #[Route('/{id}/edit', name: 'cotisergroupe_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Cotisergroupe $cotisergroupe, CotisergroupeRepository $cotisergroupeRepository, GroupeRepository $groupeRepository, FideleRepository $fideleRepository, CotisationgroupeRepository $cotisationgroupeRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->denyAccessUnlessGranted('cotisergroupe_edit', $cotisergroupe);

        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $fidele = $fideleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL, "etatfidele" => 1]);
        $groupe = $groupeRepository->findBy(['eglise' => $eglise, "user" => $user, "deletedAt" => NULL]);
        $cotisationgroupe = $cotisationgroupeRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $form = $this->createForm(CotisergroupeType::class, $cotisergroupe, ['groupe' => $groupe, 'fidele' => $fidele, 'cotisationgroupe' => $cotisationgroupe],);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $id = $form['cotisationgroupe']->getData();
            $cotise2 = $cotisationgroupeRepository->findOneByCotisationgroupe($id);
            if ($cotise2) {
                $a = 0;
                $mont = $cotise2->getMontant();

                $montant = $form['montantpayer']->getData();
//            );
                $a = $mont - $montant;

                $cotisergroupe->setMontantpayer($montant);
                $cotisergroupe->setReste($a);
                $cotisergroupe->setUpdatedFromIp($this->GetIp());
                $cotisergroupe->setUpdatedBy($user);
                $this->getDoctrine()->getManager()->flush();
            }
            return $this->redirectToRoute('cotisergroupe_index');
        }

        return $this->render('cotisergroupe/edit.html.twig', [
                    'cotisergroupe' => $cotisergroupe,
                    'form' => $form->createView(),
        ]);
    }

    
#[Route('/get-montant-cotisation', name: 'get_montant_cotisation', methods: ['POST'])]
public function getMontantCotisation(Request $request, CotisationgroupeRepository $cotisationgroupeRepository): JsonResponse
{
    $id = $request->request->get('id');
    $cotisation = $cotisationgroupeRepository->find($id);
    
    if ($cotisation) {
        return $this->json([
    'montant' => (float) $cotisation->getMontant()
]);
    }
    
    return $this->json(['montant' => 0]);
}

#[Route('/get-deja-paye-fidele', name: 'get_deja_paye_fidele', methods: ['POST'])]
public function getDejaPayeFidele(
    Request $request, 
    CotisationgroupeRepository $cotisationgroupeRepository,
    CotisergroupeRepository $cotisergroupeRepository
): JsonResponse
{
    $cotisationId = $request->request->get('cotisation_id');
    $fideleId = $request->request->get('fidele_id');
    
    if ($cotisationId && $fideleId) {
        $totalPaye = $cotisergroupeRepository->createQueryBuilder('c')
            ->select('SUM(c.montantpayer) as total')
            ->where('c.fidele = :fidele')
            ->andWhere('c.cotisationgroupe = :cotisation')
            ->andWhere('c.deletedAt IS NULL')
            ->setParameter('fidele', $fideleId)
            ->setParameter('cotisation', $cotisationId)
            ->getQuery()
            ->getSingleScalarResult() ?? 0;
        
                return $this->json(['deja_paye' => (float) $totalPaye]);
    }
    
    return $this->json(['deja_paye' => 0]);
}

    #[Route('cotisergroupe/{id}', name: 'cotisergroupe_delete', methods: ['POST'])]
    public function delete(Request $request, Cotisergroupe $cotisergroupe): Response {
        if ($this->isCsrfTokenValid('delete' . $cotisergroupe->getId(), $request->request->get('_token'))) {

            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

            $this->denyAccessUnlessGranted('cotisergroupe_delete', $cotisergroupe);

            $entityManager = $this->getDoctrine()->getManager();

            $cotisergroupe->setDeletedFromIp($this->GetIp());
            $cotisergroupe->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $cotisergroupe->setDeletedBy($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('cotisergroupe_index');
    }

}
