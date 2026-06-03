<?php

namespace App\Controller;

use App\Entity\Fiancaille;
use App\Form\FiancailleType;
use App\Repository\FideleRepository;
use App\Repository\FiancailleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use DateTime;
use App\Traits\ClientIp;


#[Route('/fiancaille')]

class FiancailleController extends AbstractController {
    use ClientIp;

    #[Route('/', name: 'fiancaille_index', methods: ['GET'])]
    public function index(FiancailleRepository $fiancailleRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
            if (!$this->isGranted('ROLE_RESPONSABLE_CONJUGAL')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $fiancaille = $fiancailleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        return $this->render('fiancaille/index.html.twig', [
                    'fiancailles' => $fiancaille,
        ]);
    }

    #[Route('/new', name: 'fiancaille_new', methods: ['GET', 'POST'])]

    public function new(Request $request, FiancailleRepository $fiancailleRepository, FideleRepository $fideleRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
            if (!$this->isGranted('ROLE_RESPONSABLE_CONJUGAL')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise()->getId();
        $user = $this->getUser();
        $fiancaille = new Fiancaille();
        $fiancemembre = $fideleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL, "sexe" => 'Homme',  "etatmariage" => 0]);
        $fianceemembre = $fideleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL, "sexe" => 'Femme', "etatmariage" => 0]);
        $form = $this->createForm(FiancailleType::class, $fiancaille, ['fiancemembre' => $fiancemembre, 'fianceemembre' => $fianceemembre]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //Adresse ip de l'utilisateur

            $fiancaille->setCreatedFromIp($this->GetIp());
            $listefiancaille = $fiancailleRepository->findBy(array(), array('id' => 'desc'), 1, 0);
            $id = 0;
            foreach ($listefiancaille as $value) {
                $id = $value->getId();
            }

            $listefidele = $fideleRepository->findBy(array(), array('id' => 'desc'), 1, 0);
            $idfidele = 0;
            foreach ($listefidele as $value) {
                $idfidele = $value->getId();
            }
            $val2 = $idfidele + 1;

//            $fideles = $form['fiance']->getId();

//            $datefiance = $form['datefiance']->getData();
//
//            $year = $datefiance->format('Y-m-d');
//            $year1 = explode('-', $year);
//            $nom = substr($fideles, 0, 5);
//            $code = $year1[0] . $nom . $val2;
//            $fiancaille->setCode($code);
            $eglise = $this->getUser()->getEglise();
            $user = $this->getUser();
            $fiancaille->setCreatedBy($user);
            $fiancaille->setEglise($eglise);
            $fiancaille = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($fiancaille);
            $this->addFlash('success', 'Enregistrement effectué avec succès.');
            $entityManager->flush();

            return $this->redirectToRoute('fiancaille_index');
        }

        return $this->render('fiancaille/new.html.twig', [
                    'fiancaille' => $fiancaille,
                    'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'fiancaille_show', methods: ['GET'])]

    public function show(Fiancaille $fiancaille): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    if (!$this->isGranted('ROLE_RESPONSABLE_CONJUGAL')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        return $this->render('fiancaille/show.html.twig', [
                    'fiancaille' => $fiancaille,
        ]);
    }

    #[Route('/{id}/edit', name: 'fiancaille_edit', methods: ['GET', 'POST'])]

    public function edit(Request $request, Fiancaille $fiancaille, FideleRepository $fideleRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
            if (!$this->isGranted('ROLE_RESPONSABLE_CONJUGAL')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise()->getId();
        $user = $this->getUser();
        $fiancemembre = $fideleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL, "sexe" => 'Homme', "etatmariage" => 0]);
        $fianceemembre = $fideleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL, "sexe" => 'Femme', "etatmariage" => 0]);
        $form = $this->createForm(FiancailleType::class, $fiancaille, ['fiancemembre' => $fiancemembre, 'fianceemembre' => $fianceemembre]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //Adresse ip de l'utilisateur
  
            $fiancaille->setUpdatedFromIp($this->GetIp());
            $user = $this->getUser();
            $fiancaille->setUpdatedBy($user);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('fiancaille_index');
        }

        return $this->render('fiancaille/edit.html.twig', [
                    'fiancaille' => $fiancaille,
                    'form' => $form->createView(),
        ]);
    }

//    #[Route('/{id}', name: 'fiancaille_delete', methods: ['POST'])]
//
//    public function delete(Request $request, Fiancaille $fiancaille): Response {
//        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
//
//        if ($this->isCsrfTokenValid('delete' . $fiancaille->getId(), $request->request->get('_token'))) {
//            $entityManager = $this->getDoctrine()->getManager();
//            $entityManager->remove($fiancaille);
//            $entityManager->flush();
//        }
//
//        return $this->redirectToRoute('fiancaille_index');
//    }
    #[Route('/{id}', name: 'fiancaille_delete', methods: ['POST'])]
    public function delete(Request $request, Fiancaille $fiancaille): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
            if (!$this->isGranted('ROLE_RESPONSABLE_CONJUGAL')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($this->isCsrfTokenValid('delete' . $fiancaille->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();

   
            $fiancaille->setDeletedFromIp($this->GetIp());
            $fiancaille->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $fiancaille->setDeletedBy($user);
            $this->addFlash('danger', 'Suppression effectuée avec succès.');
            $entityManager->flush();
        }

        return $this->redirectToRoute('fiancaille_index');
    }

}
