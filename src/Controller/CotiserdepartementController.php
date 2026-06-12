<?php

namespace App\Controller;

use App\Entity\Cotiserdepartement;
use App\Entity\Detailcotisationdepartement;
use App\Entity\Soldedepartement;
use App\Form\CotiserdepartementType;
use App\Repository\CotisationdepartementRepository;
use App\Repository\CotiserdepartementRepository;
use App\Repository\DepartementRepository;
use App\Repository\DetailcotisationdepartementRepository;
use App\Repository\FideleRepository;
use App\Repository\SoldedepartementRepository;
use App\Traits\ClientIp;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cotiserdepartement')]
class CotiserdepartementController extends AbstractController {

    use ClientIp;

    #[Route('/', name: 'cotiserdepartement_index', methods: ['GET'])]
    public function index(CotiserdepartementRepository $cotiserdepartementRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_DEPARTEMENT')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $cotiserdepartement = $cotiserdepartementRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        return $this->render('cotiserdepartement/index.html.twig', [
                    'cotiserdepartements' => $cotiserdepartement,
        ]); 
    }

    #[Route('/new', name: 'cotiserdepartement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, DepartementRepository $departementRepository, SoldedepartementRepository $soldeRepo, CotiserdepartementRepository $cotiserdepartementRepository, FideleRepository $fideleRepository, CotisationdepartementRepository $cotisationdepartementRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_DEPARTEMENT')) {
            throw $this->createAccessDeniedException('Accès refusé, vous n\'avez pas les droits d\'accès ici!');
        }
        
        $cotiserdepartement = new Cotiserdepartement();
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        
        // Récupérer le département de l'utilisateur
          $departement = $departementRepository->findOneByUser($user);

        if (!$departement) {
            $this->addFlash('warning', 'Vous ne disposez pas de département à gérer.');
            return $this->redirectToRoute('cotiserdepartement_index');
        }
        
        $idDepart = $departement->getId();
        $fidele = $fideleRepository->findFidelesByDepartement($idDepart);
        
        // Récupérer les départements pour le formulaire
        $departements = $departementRepository->findOneByUser($user);
        
        // Récupérer les cotisations actives du département
        $cotisationdepartement = $cotisationdepartementRepository->findBy(['departement' => $departements, "etatcotiser" => 1]);
        
        $form = $this->createForm(CotiserdepartementType::class, $cotiserdepartement, [
           
            'fidele' => $fidele,
            'cotisationdepartement' => $cotisationdepartement,
        ]);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $idd = $form['cotisationdepartement']->getData();
            $idf = $form['fidele']->getData();
            $montantPaye = $form['montantpayer']->getData();
            $depart = $departementRepository->findOneByUser($user);
            $date = $form['datecotiser']->getData();
            $idgpe =  $departementRepository->findOneByUser($user);
            $cotiserdepartement->setDepartement($idgpe);
            // Récupérer la cotisation pour avoir le montant total
            $cotisation = $cotisationdepartementRepository->find($idd);
            $montantTotalCotisation = $cotisation ? $cotisation->getMontant() : 0;
            
            // Vérifier si un paiement existe déjà pour ce fidèle et cette cotisation
            $existingPaiement = $cotiserdepartementRepository->findOneBy([
                'fidele' => $idf,
                'cotisationdepartement' => $idd,
                'departement' => $depart,
                'deletedAt' => null
            ]);
            
            if ($existingPaiement) {
                // Mise à jour d'un paiement existant
                $dejaPaye = $existingPaiement->getMontantpayer();
                $ancienReste = $existingPaiement->getReste();
                $nouveauTotalPaye = $dejaPaye + $montantPaye;
                $nouveauReste = $montantTotalCotisation - $nouveauTotalPaye;
                
                // Mettre à jour l'entité existante
                $existingPaiement->setMontantpayer($nouveauTotalPaye);
                $existingPaiement->setReste($nouveauReste);
                $existingPaiement->setUpdatedFromIp($this->getIp());
                $existingPaiement->setUpdatedBy($user);
                $existingPaiement->setDatecotiser($date);
                
                // Gestion du solde du département
                $this->gestionSoldeDepartement($soldeRepo, $entityManager, $idgpe, $montantPaye);
                
                // Créer le détail de la cotisation
                $detail = new Detailcotisationdepartement();
                $detail->setFidele($idf);
                $detail->setCotisationdepartement($idd);
                $detail->setEglise($eglise);
                $detail->setMontant($montantTotalCotisation);
                $detail->setMontantpayer($montantPaye);
                $detail->setDepartement($idgpe);
                $detail->setCreatedBy($user);
                $detail->setCreatedFromIp($this->getIp());
                $detail->setReste($nouveauReste);
                $detail->setDatedetail($date);
                
                $entityManager->persist($detail);
                
                $message = sprintf(
                    'Paiement mis à jour avec succès. Total payé: %s FCFA, Reste: %s FCFA',
                    number_format($nouveauTotalPaye, 0, ',', ' '),
                    number_format($nouveauReste, 0, ',', ' ')
                );
                
                if ($nouveauReste < 0) {
                    $this->addFlash('warning', 'Attention: Trop-perçu de ' . number_format(abs($nouveauReste), 0, ',', ' ') . ' FCFA');
                } else {
                    $this->addFlash('success', $message);
                }
                
            } else {
                // Nouveau paiement
                $reste = $montantTotalCotisation - $montantPaye;
                
                $cotiserdepartement->setReste($reste);
                $cotiserdepartement->setCreatedFromIp($this->getIp());
                $cotiserdepartement->setEtat(1);
                $cotiserdepartement->setEglise($eglise);
                $cotiserdepartement->setCreatedBy($user);
                $cotiserdepartement->setFidele($idf);
                $cotiserdepartement->setCotisationdepartement($idd);
                $cotiserdepartement->setDepartement($idgpe);
                $cotiserdepartement->setMontantpayer($montantPaye);
                $cotiserdepartement->setDatecotiser($date);
                
                // Gestion du solde du département
                $this->gestionSoldeDepartement($soldeRepo, $entityManager, $idgpe, $montantPaye);
                
                // Créer le détail de la cotisation
                $detail = new Detailcotisationdepartement();
                $detail->setFidele($idf);
                $detail->setCotisationdepartement($idd);
                $detail->setEglise($eglise);
                $detail->setMontant($montantTotalCotisation);
                $detail->setMontantpayer($montantPaye);
                $detail->setDepartement($idgpe);
                $detail->setCreatedBy($user);
                $detail->setCreatedFromIp($this->getIp());
                $detail->setReste($reste);
                $detail->setDatedetail($date);
                
                $entityManager->persist($detail);
                $entityManager->persist($cotiserdepartement);
                
                $message = sprintf(
                    'Paiement enregistré avec succès. Montant payé: %s FCFA, Reste: %s FCFA',
                    number_format($montantPaye, 0, ',', ' '),
                    number_format($reste, 0, ',', ' ')
                );
                
                if ($reste < 0) {
                    $this->addFlash('warning', 'Attention: Trop-perçu de ' . number_format(abs($reste), 0, ',', ' ') . ' FCFA');
                } else {
                    $this->addFlash('success', $message);
                }
            }
            
            $entityManager->flush();
            
            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'cotiserdepartement_new' : 'cotiserdepartement_index';
            return $this->redirectToRoute($nextAction);
        }
        
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('cotiserdepartement/new.html.twig', [
            'cotiserdepartement' => $cotiserdepartement,
            'departement' => $departement,
            'form' => $form->createView(),
            'response' => $response,
        ], $response);
    }

/**
 * Gestion du solde du département
 */
private function gestionSoldeDepartement(SoldedepartementRepository $soldeRepo, EntityManagerInterface $entityManager, $departement, int $montant): void
{
    $soldeExistant = $soldeRepo->findOneBy(['departement' => $departement]);
    
    if ($soldeExistant) {
        $nouveauSolde = $soldeExistant->getMontant() + $montant;
        $soldeExistant->setMontant($nouveauSolde);
      //  $soldeExistant->setUpdatedAt(new \DateTime());
    } else {
        $nouveauSolde = new Soldedepartement();
        $nouveauSolde->setMontant($montant);
        $nouveauSolde->setDepartement($departement);
        $entityManager->persist($nouveauSolde);
    }
}


#[Route('/get-montant-cotisation-departement', name: 'get_montant_cotisation_departement', methods: ['POST'])]
public function getMontantCotisation(Request $request, CotisationdepartementRepository $cotisationdepartementRepository): JsonResponse
{
    $id = $request->request->get('id');
    $cotisation = $cotisationdepartementRepository->find($id);
    
    if ($cotisation) {
        return $this->json([
    'montant' => (float) $cotisation->getMontant()
]);
    }
    
    return $this->json(['montant' => 0]);
}

#[Route('/get-deja-paye-fidele-departement', name: 'get_deja_paye_fidele_departement', methods: ['POST'])]
public function getDejaPayeFidele(
    Request $request, 
    CotisationdepartementRepository $cotisationdepartementRepository,
    CotiserdepartementRepository $cotiserdepartementRepository
): JsonResponse
{
    $cotisationId = $request->request->get('cotisation_id');
    $fideleId = $request->request->get('fidele_id');
    
    if ($cotisationId && $fideleId) {
        $totalPaye = $cotiserdepartementRepository->createQueryBuilder('c')
            ->select('SUM(c.montantpayer) as total')
            ->where('c.fidele = :fidele')
            ->andWhere('c.cotisationdepartement = :cotisation')
            ->andWhere('c.deletedAt IS NULL')
            ->setParameter('fidele', $fideleId)
            ->setParameter('cotisation', $cotisationId)
            ->getQuery()
            ->getSingleScalarResult() ?? 0;
        
                return $this->json(['deja_paye' => (float) $totalPaye]);
    }
    
    return $this->json(['deja_paye' => 0]);
}

    #[Route('/detaildepartement', name: 'cotiserdepartement_detaildepartement', methods: ['GET'])]
    public function detailCotisation(DetailcotisationdepartementRepository $detailRepo): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_DEPARTEMENT')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $detailcotisation = $detailRepo->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        return $this->render('cotiserdepartement/detailcotisation.html.twig', [
                    'details' => $detailcotisation,
        ]);
    }

    #[Route('/{id}', name: 'cotiserdepartement_show', methods: ['GET'])]
    public function show(Cotiserdepartement $cotiserdepartement): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_DEPARTEMENT')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        return $this->render('cotiserdepartement/show.html.twig', [
                    'cotiserdepartement' => $cotiserdepartement,
        ]);
    }

    #[Route('/{id}/edit', name: 'cotiserdepartement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Cotiserdepartement $cotiserdepartement, DepartementRepository $departementRepository, FideleRepository $fideleRepository, CotisationdepartementRepository $cotisationdepartementRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
//        if (!$this->isGranted('ROLE_RESPONSABLE_DEPARTEMENT')) {
//            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
//        }
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $this->denyAccessUnlessGranted('cotiserdepartement_edit', $cotiserdepartement);

        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $fidele = $fideleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL, "etatfidele" => 1]);
        $departement = $departementRepository->findBy(['eglise' => $eglise, "user" => $user, "deletedAt" => NULL]);
        $cotisationdepartement = $cotisationdepartementRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $form = $this->createForm(CotiserdepartementType::class, $cotiserdepartement, ['departement' => $departement, 'fidele' => $fidele, 'cotisationdepartement' => $cotisationdepartement],);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $id = $form['cotisationdepartement']->getData();

            $cotise2 = $cotisationdepartementRepository->findOneByCotisationdepartement($id);
            if ($cotise2) {
                $a = 0;
                $mont = $cotise2->getMontant();

                $montant = $form['montantpayer']->getData();
                $a = $mont - $montant;

                $cotiserdepartement->setMontantpayer($montant);
                $cotiserdepartement->setReste($a);
                $cotiserdepartement->setUpdatedFromIp($this->GetIp());
                $cotiserdepartement->setUpdatedBy($user);
                $this->getDoctrine()->getManager()->flush();
            }

            return $this->redirectToRoute('cotiserdepartement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('cotiserdepartement/edit.html.twig', [
                    'cotiserdepartement' => $cotiserdepartement,
                    'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'cotiserdepartement_delete', methods: ['POST'])]
    public function delete(Request $request, Cotiserdepartement $cotiserdepartement): Response {
        if ($this->isCsrfTokenValid('delete' . $cotiserdepartement->getId(), $request->request->get('_token'))) {

            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
            $this->denyAccessUnlessGranted('cotiserdepartement_delete', $cotiserdepartement);
            $entityManager = $this->getDoctrine()->getManager();

            $cotiserdepartement->setDeletedFromIp($this->GetIp());
            $cotiserdepartement->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $cotiserdepartement->setDeletedBy($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('cotiserdepartement_index');
    }

}
