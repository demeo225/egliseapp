<?php

namespace App\Controller;


use App\Entity\Typeculte;

use App\Form\TypeculteType;

use App\Repository\TypeculteRepository;
use App\Repository\SoldeministreRepository;
use App\Service\FileUploader;
use App\Traits\ClientIp;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/typeculte')]
class TypeculteController extends AbstractController
{
    use ClientIp;

    #[Route('/', name: 'app_typeculte_index', methods: ['GET'])]
    public function index(TypeculteRepository $typeculteRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $eglise = $this->getUser()->getEglise();
        $typeculte = $typeculteRepository->findBy([ 'eglise'=>$eglise  , "deletedAt" => NULL]);
        // $difference = $typeculteRepository->getSeanceByDates();
        return $this->render('typeculte/index.html.twig', [
            'typecultes' => $typeculte,
            //    'differences' => $difference,
        ]);
    }
    #[Route('/{id}/edit', name: 'app_typeculte_edit', methods: ['GET', 'POST'])]
    #[Route('/new', name: 'app_typeculte_new', methods: ['GET', 'POST'])]
    public function new(EntityManagerInterface $entityManager, Request $request, ?Typeculte $typeculte = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_RESPONSABLE_ZONE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }

        $type = $typeculte === null ? 'new' : 'edit';
        $typeculte = $typeculte === null ? new Typeculte() : $typeculte;
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();

        $form = $this->createForm(TypeculteType::class, $typeculte,);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //Adresse ip de l'utilisateur
            if ($type === 'new') {
                $typeculte->setCreatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setEglise($user->getEglise())
                        ->setCreatedBy($user)
                ;
                $this->addFlash('success', 'Enregistrement avec succès.');
            
            } else {
                $typeculte->setUpdatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setUpdatedBy($user)
                ;
                $this->addFlash('success', 'Modification avec succès.');
            }
           

         
            $entityManager->persist($typeculte);
            $entityManager->flush();
            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'app_typeculte_new' : 'app_typeculte_index';
            if ($nextAction) {
               // $this->addFlash('message', 'Enregistrement avec succès.');
            }

            return $this->redirectToRoute($nextAction);
//            return $this->redirectToRoute('app_typeculte_index', [], Response::HTTP_SEE_OTHER);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('typeculte/new.html.twig', [
                    'typeculte' => $typeculte,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    


    #[Route('/{id}', name: 'app_typeculte_delete', methods: ['POST'])]
    public function delete(Request $request, Typeculte $typeculte, EntityManagerInterface $em): Response
    {


        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        //$this->denyAccessUnlessGranted('typeculte_delete', $typeculte);

        if ($this->isCsrfTokenValid('delete' . $typeculte->getId(), $request->request->get('_token'))) {

          //  $entityManager = $this->getDoctrine()->getManager();

            $typeculte->setDeletedFromIp($this->GetIp());
            $typeculte->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $typeculte->setDeletedBy($user);
            $em->flush();
            if ($request) {
                $this->addFlash('danger', 'Suppression avec succès.');
            }
        }

        return $this->redirectToRoute('app_typeculte_index');
    }
}
