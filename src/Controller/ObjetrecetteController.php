<?php

namespace App\Controller;


use App\Entity\Objetrecette;

use App\Form\ObjetrecetteType;

use App\Repository\ObjetrecetteRepository;

use App\Traits\ClientIp;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/objetrecette')]
class ObjetrecetteController extends AbstractController
{
    use ClientIp;

    #[Route('/', name: 'app_objetrecette_index', methods: ['GET'])]
    public function index(ObjetrecetteRepository $objetrecetteRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $eglise = $this->getUser()->getEglise();
        $objetrecette = $objetrecetteRepository->findBy([ 'eglise'=>$eglise  , "deletedAt" => NULL]);
        // $difference = $objetrecetteRepository->getSeanceByDates();
        return $this->render('objetrecette/index.html.twig', [
            'objetrecettes' => $objetrecette,
            //    'differences' => $difference,
        ]);
    }
    #[Route('/{id}/edit', name: 'app_objetrecette_edit', methods: ['GET', 'POST'])]
    #[Route('/new', name: 'app_objetrecette_new', methods: ['GET', 'POST'])]
    public function new(EntityManagerInterface $entityManager, Request $request, ?Objetrecette $objetrecette = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_ZONE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }

        $type = $objetrecette === null ? 'new' : 'edit';
        $objetrecette = $objetrecette === null ? new Objetrecette() : $objetrecette;
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();

        $form = $this->createForm(ObjetrecetteType::class, $objetrecette,);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //Adresse ip de l'utilisateur
            if ($type === 'new') {
                $objetrecette->setCreatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setEglise($user->getEglise())
                        ->setCreatedBy($user)
                ;
                
            
            } else {
                $objetrecette->setUpdatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setUpdatedBy($user)
                ;
                $this->addFlash('message', 'Modification avec succès.');
            }
           

         
            $entityManager->persist($objetrecette);
            $entityManager->flush();
            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'app_objetrecette_new' : 'app_objetrecette_index';
            if ($nextAction) {
                $this->addFlash('message', 'Enregistrement avec succès.');
            }

            return $this->redirectToRoute($nextAction);
//            return $this->redirectToRoute('app_objetrecette_index', [], Response::HTTP_SEE_OTHER);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('objetrecette/new.html.twig', [
                    'objetrecette' => $objetrecette,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    


    #[Route('/{id}', name: 'app_objetrecette_delete', methods: ['POST'])]
    public function delete(Request $request, Objetrecette $objetrecette, EntityManagerInterface $em): Response
    {


        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        //$this->denyAccessUnlessGranted('objetrecette_delete', $objetrecette);

        if ($this->isCsrfTokenValid('delete' . $objetrecette->getId(), $request->request->get('_token'))) {

          //  $entityManager = $this->getDoctrine()->getManager();

            $objetrecette->setDeletedFromIp($this->GetIp());
            $objetrecette->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $objetrecette->setDeletedBy($user);
            $em->flush();
            if ($request) {
                $this->addFlash('message', 'Suppression avec succès.');
            }
        }

        return $this->redirectToRoute('app_objetrecette_index');
    }
}
