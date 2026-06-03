<?php

namespace App\Controller;

use App\Entity\Presenceculteecodim;
use App\Form\PresenceculteecodimType;
use App\Repository\ClassecodimRepository;
use App\Repository\CultecodimRepository;
use App\Repository\EnfantRepository;
use App\Repository\PresenceculteecodimRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Traits\ClientIp;


#[Route('/presenceculteecodim')]
class PresenceculteecodimController extends AbstractController {
 use ClientIp;
    
    #[Route('/', name: 'app_presenceculteecodim_index', methods: ['GET'])]
    public function index(PresenceculteecodimRepository $presenceculteecodimRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_ECODIM')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $presence = $presenceculteecodimRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);

        return $this->render('presenceculteecodim/index.html.twig', [
                    'presenceculteecodims' => $presence,
        ]);
    }


    #[Route('/presence', name: 'app_presenceculteecodim_presence', methods: ['POST', 'GET'])]
    public function presenceCulte(EnfantRepository $enfantRepository, Request $request, ClassecodimRepository $classeRepo, CultecodimRepository $cultecodimRepository, PresenceculteecodimRepository $presenceRepo): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_ECODIM')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        if ($request->isMethod('POST')) {
            $cultecodim = $request->request->get('cultecodim');
            $classecodim = $request->request->get('classecodim');
            $tabpost = $request->request->get('tab');
        
            foreach ($tabpost as $value) {
                $em = $this->getDoctrine()->getManager();
                $idenfant = $enfantRepository->find($value);
                $presenceculte = new Presenceculteecodim();

                $idcultecodim = $cultecodimRepository->find($cultecodim);
                $idclassecodim = $classeRepo->find($classecodim);
//
                $dql = $presenceRepo->findBy(['enfant' => $presenceculte->getEnfant(), 'cultecodim' => $presenceculte->getCultecodim()]);

                if ($dql) {


                    $this->addFlash('presentecodim', 'Présence dejà marquée pour cet enfant.');
                    return $this->redirectToRoute('app_presenceculteecodim_presence', [], Response::HTTP_SEE_OTHER);
                } else {


                    $eglise = $this->getUser()->getEglise();
                    $user = $this->getUser();
            
                    $presenceculte->setEnfant($idenfant);
                    $presenceculte->setCultecodim($idcultecodim);
                    $presenceculte->setClassecodim($idclassecodim);
                    $presenceculte->setEglise($eglise);
                    $presenceculte->setCreatedBy($this->getUser());
                    $em->persist($presenceculte);
                    $em->flush();
                }
            }
            $this->addFlash('success', 'Enregistrement effectué avec succès');

            return $this->redirectToRoute('app_presenceculteecodim_listepresence');
        } else {
            $enfant = $enfantRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL, "etatenfant" => 1]);

            $cultecodim = $cultecodimRepository->findBy(['eglise' => $eglise]);
            $classecodim = $classeRepo->findBy(['eglise' => $eglise]);
            return $this->render('presenceculteecodim/presence.html.twig',
                            [
                                'enfants' => $enfant,
                                'cultecodims' => $cultecodim,
                                'classecodims' => $classecodim,
            ]);
        }
    }

    #[Route('{id}/presenceculte', name: 'app_presenceculteecodim_delete', methods: ['POST'])]
    public function deletePresence(Request $request, Presenceculteecodim $presenceculte): Response {
          if (!$this->isGranted('ROLE_RESPONSABLE_ECODIM')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($this->isCsrfTokenValid('delete' . $presenceculte->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();

            $presenceculte->setDeletedFromIp($this->GetIp());
            $presenceculte->setDeletedAt(new DateTime("now"));
            $presenceculte->setEditable(null);
            $user = $this->getUser();
            $presenceculte->setDeletedBy($user);
            $entityManager->flush();
        }

        if ($request) {
            $this->addFlash('success', 'Suppression avec succès.');
        }

        return $this->redirectToRoute('app_presenceculteecodim_listepresence', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/listepresence', name: 'app_presenceculteecodim_listepresence', methods: ['GET'])]
    public function listePresence(PresenceculteecodimRepository $presenceRepository, CultecodimRepository $cultedimRepo): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_ECODIM')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $presence = $presenceRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL,]);
        $diff = $cultedimRepo->findCultecodimsByDates();
        return $this->render('presenceculteecodim/listepresence.html.twig', [
                    'presenceculteecodims' => $presence,
                    'differences' => $diff,
        ]);
    }


}
