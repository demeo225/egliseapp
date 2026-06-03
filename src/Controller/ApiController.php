<?php

namespace App\Controller;

use App\Entity\Programme;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    /**
     * @Route("/api", name="api")
     */
    public function index()
    {
        return $this->render('api/index.html.twig', [
            'controller_name' => 'ApiController',
        ]);
    }

    /**
     * @Route("/api/{id}/edit", name="api_event_edit", methods={"PUT"})
     */
    public function majEvent(?Programme $programme, Request $request)
    {
        // On récupère les données
        $donnees = json_decode($request->getContent());

        if(
            isset($donnees->title) && !empty($donnees->title) &&
            isset($donnees->start) && !empty($donnees->start) &&
            isset($donnees->description) && !empty($donnees->description) &&
            isset($donnees->backgroundColor) && !empty($donnees->backgroundColor) &&
            isset($donnees->borderColor) && !empty($donnees->borderColor) &&
            isset($donnees->textColor) && !empty($donnees->textColor)
        ){
            // Les données sont complètes
            // On initialise un code
            $code = 200;

            // On vérifie si l'id existe
            if(!$programme){
                // On instancie un rendez-vous
                $programme = new Programme;

                // On change le code
                $code = 201;
            }

            // On hydrate l'objet avec les données
            $programme->setTitle($donnees->title);
            $programme->setDescription($donnees->description);
            $programme->setStart(new DateTime($donnees->start));
            if($donnees->allDay){
                $programme->setEnd(new DateTime($donnees->start));
            }else{
                $programme->setEnd(new DateTime($donnees->end));
            }
            $programme->setAllDay($donnees->allDay);
            $programme->setBackgroundColor($donnees->backgroundColor);
            $programme->setBorderColor($donnees->borderColor);
            $programme->setTextColor($donnees->textColor);

            $em = $this->getDoctrine()->getManager();
            $em->persist($programme);
            $em->flush();

            // On retourne le code
            return new Response('Ok', $code);
        }else{
            // Les données sont incomplètes
            return new Response('Données incomplètes', 404);
        }


        return $this->render('api/index.html.twig', [
            'controller_name' => 'ApiController',
        ]);
    }
}
