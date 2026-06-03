<?php

namespace App\Controller;


use App\Entity\Objetcharge;

use App\Form\ObjetchargeType;

use App\Repository\ObjetchargeRepository;

use App\Traits\ClientIp;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/objetcharge')]
class ObjetchargeController extends AbstractController
{
    use ClientIp;

    #[Route('/', name: 'app_objetcharge_index', methods: ['GET'])]
    public function index(ObjetchargeRepository $objetchargeRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $eglise = $this->getUser()->getEglise();
        $objetcharge = $objetchargeRepository->findBy([ 'eglise'=>$eglise  , "deletedAt" => NULL]);
        // $difference = $objetchargeRepository->getSeanceByDates();
        return $this->render('objetcharge/index.html.twig', [
            'objetcharges' => $objetcharge,
            //    'differences' => $difference,
        ]);
    }
    #[Route('/{id}/edit', name: 'app_objetcharge_edit', methods: ['GET', 'POST'])]
    #[Route('/new', name: 'app_objetcharge_new', methods: ['GET', 'POST'])]
    public function new(EntityManagerInterface $entityManager, Request $request, ?Objetcharge $objetcharge = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_ZONE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }

        $type = $objetcharge === null ? 'new' : 'edit';
        $objetcharge = $objetcharge === null ? new Objetcharge() : $objetcharge;
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();

        $form = $this->createForm(ObjetchargeType::class, $objetcharge,);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //Adresse ip de l'utilisateur
            if ($type === 'new') {
                $objetcharge->setCreatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setEglise($user->getEglise())
                        ->setCreatedBy($user)
                ;
                
            
            } else {
                $objetcharge->setUpdatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setUpdatedBy($user)
                ;
                $this->addFlash('success', 'Modification avec succès.');
            }
           

         
            $entityManager->persist($objetcharge);
            $entityManager->flush();
            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'app_objetcharge_new' : 'app_objetcharge_index';
            if ($nextAction) {
                $this->addFlash('success', 'Enregistrement avec succès.');
            }

            return $this->redirectToRoute($nextAction);
//            return $this->redirectToRoute('app_objetcharge_index', [], Response::HTTP_SEE_OTHER);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('objetcharge/new.html.twig', [
                    'objetcharge' => $objetcharge,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    


    #[Route('/{id}', name: 'app_objetcharge_delete', methods: ['POST'])]
    public function delete(Request $request, Objetcharge $objetcharge, EntityManagerInterface $em): Response
    {


        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        //$this->denyAccessUnlessGranted('objetcharge_delete', $objetcharge);

        if ($this->isCsrfTokenValid('delete' . $objetcharge->getId(), $request->request->get('_token'))) {

          //  $entityManager = $this->getDoctrine()->getManager();

            $objetcharge->setDeletedFromIp($this->GetIp());
            $objetcharge->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $objetcharge->setDeletedBy($user);
            $em->flush();
            if ($request) {
                $this->addFlash('danger', 'Suppression avec succès.');
            }
        }

        return $this->redirectToRoute('app_objetcharge_index');
    }
}
