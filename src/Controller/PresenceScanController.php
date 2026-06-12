<?php

namespace App\Controller;

use App\Entity\Fidele;
use App\Entity\Presenceculte;
use App\Repository\CulteRepository;
use App\Repository\FideleRepository;
use App\Repository\PresenceculteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PresenceScanController extends AbstractController
{
    
    #[Route('/presence/{token}', name: 'presence_scan')]
    public function scan(
        string $token,
        CulteRepository $culteRepository
    ): Response {

        $culte = $culteRepository->findOneBy([
            'tokenPresence' => $token,
            'etat' => 1
        ]);

        if (!$culte) {
            throw $this->createNotFoundException('QR Code invalide');
        }

        return $this->render('presence/scan.html.twig', [
            'culte' => $culte
        ]);
    }

    #[Route(
        '/presence/{token}/save',
        name: 'presence_save',
        methods: ['POST']
    )]
    public function save(
        string $token,
        Request $request,
        EntityManagerInterface $em,
        CulteRepository $culteRepository,
        FideleRepository $fideleRepository,
        PresenceculteRepository $presenceRepository
    ): Response {

        $culte = $culteRepository->findOneBy([
            'tokenPresence' => $token,
            'etat' => 1
        ]);

        if (!$culte) {

            return new Response(
                '<div class="alert alert-danger">
                    QR Code invalide
                </div>'
            );
        }

        if (
            $culte->getDateExpirationQr()
            && new \DateTime() > $culte->getDateExpirationQr()
        ) {

            return new Response(
                '<div class="alert alert-danger">
                    QR Code expiré
                </div>'
            );
        }

        $nom = trim(
            $request->request->get('nomfidele')
        );

        $contact = trim(
            $request->request->get('contact1')
        );

        if (!$contact) {

            return new Response(
                '<div class="alert alert-danger">
                    Téléphone obligatoire
                </div>'
            );
        }

        $fidele = $fideleRepository->findOneBy([
            'contact1' => $contact,
            'eglise' => $culte->getEglise()
        ]);

        if (!$fidele) {

            $fidele = new Fidele();

            $fidele->setNomfidele($nom);
            $fidele->setContact1($contact);
            $fidele->setEglise(
                $culte->getEglise()
            );

            $em->persist($fidele);
            $em->flush();
        }

        $presenceExistante =
            $presenceRepository->findOneBy([
                'fidele' => $fidele,
                'culte' => $culte
            ]);

        if ($presenceExistante) {

            return new Response(
                '<div class="alert alert-warning">
                    Vous avez déjà été enregistré.
                </div>'
            );
        }

        $presence = new Presenceculte();

        $presence->setFidele($fidele);
        $presence->setCulte($culte);
        $presence->setEglise(
            $culte->getEglise()
        );

        $em->persist($presence);
        $em->flush();

        return new Response(
            '<div class="alert alert-success">
                Présence enregistrée avec succès.
            </div>'
        );
    }
}