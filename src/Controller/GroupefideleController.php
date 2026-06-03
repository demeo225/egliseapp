<?php

namespace App\Controller;

use App\Entity\Fidele;
use App\Entity\Groupefidele;
use App\Form\GroupeFideleRestaureType;
use App\Form\GroupefideleType;
use App\Form\SuppgroupefideleType;
use App\Repository\DepartementRepository;
use App\Repository\FideleRepository;
use App\Repository\GroupefideleRepository;
use App\Repository\GroupeRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Traits\ClientIp;


#[Route('/groupefidele')]
class GroupefideleController extends AbstractController {
    use ClientIp;
    
    #[Route('/', name: 'groupefidele')]
    public function index(GroupefideleRepository $groupefideleRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $groupefidele = $groupefideleRepository->findBy(['eglise' => $eglise, "etatgroupe" => 1]);
        return $this->render('groupefidele/index.html.twig', [
                    'groupefidele' => $groupefidele,
        ]);
    }

//     #[Route('/add', name: 'groupefidele_add', methods: ['GET', 'POST'])]
//     public function add(EntityManagerInterface $entityManager, Request $request, DepartementRepository $departementRepository, GroupefideleRepository $groupefideleRepository, FideleRepository $fideleRepository, GroupeRepository $groupeRepository): Response {
//         $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
//         if (!$this->isGranted('ROLE_SECRETAIRE')) {
//             throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
//         }
//         $groupefidele = new Groupefidele();
//         $eglise = $this->getUser()->getEglise()->getId();
//         $user = $this->getUser();
//         $fidele = $fideleRepository->findBy(['eglise' => $eglise, "etatfidele" => 1, "deletedAt" => NULL]);
//         $groupe = $groupeRepository->findBy(['eglise' => $eglise, "deletedBy" => Null]);
//         $departement = $departementRepository->findBy(['eglise' => $eglise,  "deletedAt" => NULL]);
//         $form = $this->createForm(GroupefideleType::class, $groupefidele, ['fidele' => $fidele, 'groupe' => $groupe, 'departement' => $departement],);
//         $form->handleRequest($request);
//         if ($form->isSubmitted() && $form->isValid()) {

//             $groupefidele = $form->getData();
//             $dql = $groupefideleRepository->findBy(['fidele' => $groupefidele->getFidele(),
//                 'groupe' => $groupefidele->getGroupe()
//             ]);
//             if ($dql) {
//                 $this->addFlash('warning', 'Ce fidèle fait partir de ce groupe, veuillez le restaurer s\'il avait été supprimé.');
//                 return $this->redirectToRoute('groupefidele_archivegroupefidele', [], Response::HTTP_SEE_OTHER);
//             } else {


//                 $groupefidele->setCreatedFromIp($this->GetIp());
//                 $groupefidele->setEtatgroupe("1");
//                 $eglise = $this->getUser()->getEglise();
//                 $user = $this->getUser();
//                 $groupefidele->setCreatedBy($user);
//                 $groupefidele->setEglise($eglise);
//                 $groupefidele->setIdeglise($user->getEglise()->GetId());
//                 $entityManager->persist($groupefidele);
//                 $entityManager->flush();
// //                $this->addFlash('success', 'Enregistrement effectué avec succès.');
//                 // Action Ajouter et continuer
//                 $nextAction = $form->get('saveAndAdd')->isClicked() ? 'groupefidele_add' : 'groupefidele';
//                 if ($nextAction) {
//                     $this->addFlash('success', 'Enregistrement avec succès.');
//                 }
//                 return $this->redirectToRoute($nextAction);
//             }
//         }
//         $response = new Response(null, $form->isSubmitted() ? 422 : 200);
//         return $this->render('groupefidele/add.html.twig', [
//                     'groupefidele' => $groupefidele,
//                     'form' => $form->createView(),
//                     'response' => $response,
//                         ], $response);
//     }


#[Route('/add', name: 'groupefidele_add', methods: ['GET', 'POST'])]
public function add(EntityManagerInterface $entityManager, Request $request, DepartementRepository $departementRepository, GroupefideleRepository $groupefideleRepository, FideleRepository $fideleRepository, GroupeRepository $groupeRepository): Response
{
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    if (!$this->isGranted('ROLE_SECRETAIRE')) {
        throw $this->createAccessDeniedException('Accès refusé, vous n\'avez pas les droits d\'accès ici!');
    }
    
    $eglise = $this->getUser()->getEglise()->getId();
    $user = $this->getUser();
    
    $fidele = $fideleRepository->findBy(['eglise' => $eglise, "etatfidele" => 1, "deletedAt" => NULL]);
    $groupe = $groupeRepository->findBy(['eglise' => $eglise, "deletedBy" => Null]);
    $departement = $departementRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
    
    // Créer un formulaire vide pour la collection
    $form = $this->createForm(GroupefideleType::class, null, [
        'fidele' => $fidele, 
        'groupe' => $groupe, 
        'departement' => $departement
    ]);
    
    $form->handleRequest($request);
    
    if ($form->isSubmitted() && $form->isValid()) {
        $groupefideles = $form->get('groupefideles')->getData();
        $successCount = 0;
        $errorCount = 0;
        $errors = [];
        
        foreach ($groupefideles as $groupefidele) {
            // Vérifier si l'enregistrement existe déjà
            $existing = $groupefideleRepository->findOneBy([
                'fidele' => $groupefidele->getFidele(),
                'groupe' => $groupefidele->getGroupe()
            ]);

                $existingDepartement = $groupefideleRepository->findOneBy([
                'fidele' => $groupefidele->getFidele(),
                'departement' => $groupefidele->getDepartement()
            ]);
            
            if ($existing) {
                $errorCount++;
                $errors[] = $groupefidele->getFidele()->getNomfidele() . ' - ' . $groupefidele->getGroupe()->getNom();
                continue;
            }
           
            
            // Définir les propriétés communes
            $groupefidele->setCreatedFromIp($this->GetIp());
            $groupefidele->setEtatgroupe("1");
            $groupefidele->setCreatedBy($user);
            $groupefidele->setEglise($user->getEglise());
          //  $groupefidele->setIdeglise($user->getEglise()->getId());
            
            $entityManager->persist($groupefidele);
            $successCount++;
        }
        
        if ($successCount > 0) {
            $entityManager->flush();
            $this->addFlash('success', "$successCount enregistrement(s) effectué(s) avec succès.");
        }
        
        if ($errorCount > 0) {
            $this->addFlash('warning', "$errorCount enregistrement(s) non effectué(s). Doublons pour : " . implode(', ', $errors));
        }
        
        // Action Ajouter et continuer
        $nextAction = $form->get('saveAndAdd')->isClicked() ? 'groupefidele_add' : 'groupefidele';
        return $this->redirectToRoute($nextAction);
    }
    
    $response = new Response(null, $form->isSubmitted() ? 422 : 200);
    return $this->render('groupefidele/add.html.twig', [
        'form' => $form->createView(),
        'response' => $response,
    ], $response);
}

    #[Route('/{id}/update', name: 'groupefidele_update', methods: ['GET', 'POST'])]
    public function update(Request $request, DepartementRepository $departementRepository, Groupefidele $groupefidele, FideleRepository $fideleRepository, GroupeRepository $groupeRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $data = json_decode($request->getContent(), true);
        $eglise = $this->getUser()->getEglise()->getId();
        $user = $this->getUser();
        $fidele = $fideleRepository->findBy(['eglise' => $eglise, "etatfidele" => 1]);
        $groupe = $groupeRepository->findBy(['eglise' => $eglise, "deletedBy" => Null]);
        $departement = $departementRepository->findBy(['eglise' => $eglise, "deletedBy" => Null]);
        $form = $this->createForm(GroupefideleType::class, $groupefidele, ['fidele' => $fidele, 'groupe' => $groupe, 'departement' => $departement],);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $groupefidele->setUpdatedFromIp($this->GetIp());
            $user = $this->getUser();
            $groupefidele->setUpdatedBy($user);

            $this->getDoctrine()->getManager()->flush();
             $this->addFlash('success', 'Modification avec succès.');
            return $this->redirectToRoute('groupefidele');
        }

        return $this->render('groupefidele/update.html.twig', [
                    'groupefidele' => $groupefidele,
                    'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/suppgroupefidele/{id}", name="groupefidele_suppgroupefidele")
     */
    public function supp(Request $request, Groupefidele $groupefidele, GroupeRepository $groupeRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $data = json_decode($request->getContent(), true);
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $groupe = $groupeRepository->findBy(['eglise' => $eglise, "deletedBy" => Null]);
        $form = $this->createForm(SuppgroupefideleType::class, $groupefidele, ['groupe' => $groupe]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $groupefidele = $form->getData();
            $groupefidele->setEtatgroupe("0");
            $groupefidele->setUpdatedBy($user);
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('groupefidele');
        }

        return $this->render('groupefidele/suppgroupefidele.html.twig', [
                    'groupefidele' => $groupefidele,
                    'form' => $form->createView(),
                    'adjectif' => 'Suppression',
        ]);
    }

    #[Route('/archivegf', name: 'groupefidele_archivegroupefidele')]
    public function listeSupp(GroupefideleRepository $groupefideleRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise()->getId();
        $user = $this->getUser();
        $groupefidele = $groupefideleRepository->findBy(['eglise' => $eglise, "etatgroupe" => 0]);

        return $this->render('groupefidele/archivegf.html.twig', [
                    'groupefideles' => $groupefidele,
        ]);
    }



    #[Route('/{id}/detail', name: 'groupefidele_detail', methods: ['GET', 'POST'])]
    public function detail(Groupefidele $groupefidele): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        return $this->render('groupefidele/detail.html.twig', [
                    'groupefidele' => $groupefidele,
        ]);
    }

    #[Route('/membre/{id}', name: 'groupefidele_membregpefidele', methods: ['GET', 'POST'])]
    public function membregroupefidele(Request $request, FideleRepository $fideleRepository) {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        //Recuperation id groupefidele
        $idgroupefidele = $request->query->get('id');
        //Recuperation de la liste des fidele par groupefidele
        $listeFidele = $fideleRepository->findBy(['groupefidele' => $idgroupefidele]);
        return $this->render('groupefidele/membregpefidele.html.twig', [
                    'fideles' => $listeFidele,
        ]);
    }
  /**
     * @Route("/restauregf/{id}", name="groupefidele_restaure")
     */
    public function restaure(Request $request, Groupefidele $groupefidele): Response {
        $data = json_decode($request->getContent(), true);
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_ECODIM')) {
            throw $this->createAccessDeniedException('Accès refusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($this->isCsrfTokenValid('restaureg' . $groupefidele->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();

            $user = $this->getUser();
            $groupefidele->setEtatgroupe("1");
            $this->addFlash('success', 'Reintegration avec succès');
            $entityManager->flush();
        }

        return $this->redirectToRoute('groupefidele');
    }

    #[Route('/{id}', name: 'groupefidele_delete', methods: ['POST'])]
    public function delete(Request $request, Groupefidele $groupefidele): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_ECODIM')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($this->isCsrfTokenValid('delete' . $groupefidele->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();

   
            $groupefidele->setDeletedFromIp($this->GetIp());
            $groupefidele->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $groupefidele->setEtatgroupe("0");
            $groupefidele->setDeletedBy($user);
            $this->addFlash('danger', 'Supression avec succès');
            $entityManager->flush();
        }

        return $this->redirectToRoute('groupefidele');
    }

    

#[Route('/get-groupes-by-departement/{departementId}', name: 'get_groupes_by_departement', methods: ['GET'])]
public function getGroupesByDepartement(int $departementId, GroupeRepository $groupeRepository): JsonResponse
{
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    
    $eglise = $this->getUser()->getEglise()->getId();
    
    // Récupérer les groupes liés à ce département
    $groupes = $groupeRepository->createQueryBuilder('g')
        ->leftJoin('g.departement', 'd')
        ->where('d.id = :departementId')
        ->andWhere('g.eglise = :eglise')
        ->andWhere('g.deletedBy IS NULL')
        ->setParameter('departementId', $departementId)
        ->setParameter('eglise', $eglise)
        ->getQuery()
        ->getResult();
    
    $data = [];
    foreach ($groupes as $groupe) {
        $data[] = [
            'id' => $groupe->getId(),
            'nom' => $groupe->getNom()
        ];
    }
    
    return $this->json($data);
}
}
