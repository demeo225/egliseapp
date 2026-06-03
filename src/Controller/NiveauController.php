<?php

namespace App\Controller;


use App\Entity\Niveau;

use App\Form\NiveauType;

use App\Repository\NiveauRepository;
use App\Repository\SoldeministreRepository;
use App\Service\FileUploader;
use App\Traits\ClientIp;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/niveau')]
class NiveauController extends AbstractController
{
    use ClientIp;

    #[Route('/', name: 'app_niveau_index', methods: ['GET'])]
    public function index(NiveauRepository $niveauRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $eglise = $this->getUser()->getEglise();
        $niveau = $niveauRepository->findBy([ 'eglise'=>$eglise  , "deletedAt" => NULL]);
        // $difference = $niveauRepository->getSeanceByDates();
        return $this->render('niveau/index.html.twig', [
            'niveaus' => $niveau,
            //    'differences' => $difference,
        ]);
    }
    #[Route('/{id}/edit', name: 'app_niveau_edit', methods: ['GET', 'POST'])]
    #[Route('/new', name: 'app_niveau_new', methods: ['GET', 'POST'])]
    public function new(EntityManagerInterface $entityManager, Request $request, ?Niveau $niveau = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_ZONE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }

        $type = $niveau === null ? 'new' : 'edit';
        $niveau = $niveau === null ? new Niveau() : $niveau;
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();

        $form = $this->createForm(NiveauType::class, $niveau,);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //Adresse ip de l'utilisateur
            if ($type === 'new') {
                $niveau->setCreatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setEglise($user->getEglise())
                        ->setCreatedBy($user)
                ;
                
            
            } else {
                $niveau->setUpdatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setUpdatedBy($user)
                ;
                $this->addFlash('success', 'Modification avec succès.');
            }
           

         
            $entityManager->persist($niveau);
            $entityManager->flush();
            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'app_niveau_new' : 'app_niveau_index';
            if ($nextAction) {
                $this->addFlash('success', 'Enregistrement avec succès.');
            }

            return $this->redirectToRoute($nextAction);
//            return $this->redirectToRoute('app_niveau_index', [], Response::HTTP_SEE_OTHER);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('niveau/new.html.twig', [
                    'niveau' => $niveau,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    


    #[Route('/{id}', name: 'app_niveau_delete', methods: ['POST'])]
    public function delete(Request $request, Niveau $niveau, EntityManagerInterface $em): Response
    {


        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        //$this->denyAccessUnlessGranted('niveau_delete', $niveau);

        if ($this->isCsrfTokenValid('delete' . $niveau->getId(), $request->request->get('_token'))) {

          //  $entityManager = $this->getDoctrine()->getManager();

            $niveau->setDeletedFromIp($this->GetIp());
            $niveau->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $niveau->setDeletedBy($user);
            $em->flush();
            if ($request) {
                $this->addFlash('danger', 'Suppression avec succès.');
            }
        }

        return $this->redirectToRoute('app_niveau_index');
    }
}
