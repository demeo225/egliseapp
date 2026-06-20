<?php

namespace App\Controller;

use App\Entity\Fidele;
use App\Entity\Eglise;
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
    
    // #[Route('/presence/{token}', name: 'presence_scan')]
    // public function scan(
    //     string $token,
    //     CulteRepository $culteRepository
    // ): Response {

    //     $culte = $culteRepository->findOneBy([
    //         'tokenPresence' => $token,
    //         'etat' => 1
    //     ]);

    //     if (!$culte) {
    //         throw $this->createNotFoundException('QR Code invalide');
    //     }

    //     return $this->render('culte/scan.html.twig', [
    //         'culte' => $culte
    //     ]);
    // }

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

    // Vérifier si le token a expiré
    if ($culte->isTokenExpired()) {
        $this->addFlash('error', 'Le QR Code a expiré. Veuillez contacter l\'administrateur.');
        return $this->render('culte/expired.html.twig', [
            'culte' => $culte
        ]);
    }

    return $this->render('culte/scan.html.twig', [
        'culte' => $culte,
        'tempsRestant' => $culte->getTokenRemainingTime()
    ]);
}

        
    #[Route('/presence/{token}/save', name: 'presence_save', methods: ['POST'])]
    public function save(string $token, Request $request, EntityManagerInterface $em, CulteRepository $culteRepository,
        FideleRepository $fideleRepository,
        PresenceculteRepository $presenceRepository
    ): Response {
        
        $culte = $culteRepository->findOneBy([
            'tokenPresence' => $token,
            'etat' => 1
        ]);

        if (!$culte) {
            return new Response(
                '<div class="alert alert-danger">QR Code invalide</div>',
                400
            );
        }

        if ($culte->isTokenExpired()) {
        return new Response(
            '<div class="alert alert-danger">QR Code expiré</div>',
            400
        );
     }

    //     if ($culte->getDateExpirationQr() && new \DateTime() > $culte->getDateExpirationQr()) {
    //         return new Response(
    //             '<div class="alert alert-danger">QR Code expiré</div>',
    //             400
    //         );
    //     }

        $nom = trim($request->request->get('nomfidele', ''));
        $contact = trim($request->request->get('contact1', ''));

       

        $code = $this->generateFideleCode($culte->getEglise(), $nom);

        if (!$contact) {
            return new Response(
                '<div class="alert alert-danger">Le numéro de téléphone est obligatoire</div>',
                400
            );
        }

        // Rechercher ou créer le fidèle
        $fidele = $fideleRepository->findOneBy([
            'contact1' => $contact,
            'eglise' => $culte->getEglise(),
            'deletedAt' => null
        ]);

        if (!$fidele) {
            $fidele = new Fidele();
            $fidele->setNomfidele($nom ?: 'Anonyme');
            $fidele->setContact1($contact);
            $fidele->setEglise($culte->getEglise());
             $fidele->setEtatfidele(0);
             $fidele->setDatenaiss(new \DateTime());
             $fidele->setDateconversion(new \DateTime());
             $fidele->setDatearriver(new \DateTime());
             $fidele->setCode($code);

          //  $fidele->setCreatedBy(0);
            $fidele->setCreateAt(new \DateTime());
            
            $em->persist($fidele);
            $em->flush();
        }

        // Vérifier si la présence existe déjà
        $presenceExistante = $presenceRepository->findOneBy([
            'fidele' => $fidele,
            'culte' => $culte
        ]);

        if ($presenceExistante) {
            return new Response(
                '<div class="alert alert-warning">
                    <i class="fas fa-check-circle"></i> Vous avez déjà été enregistré pour ce culte.
                </div>',
                200
            );
        }

        // Créer la présence
        $presence = new Presenceculte();
        $presence->setFidele($fidele);
        $presence->setCulte($culte);
        $presence->setEglise($culte->getEglise());
       // $presence->setCreatedBy(0);
        $presence->setCreateAt(new \DateTime());

        $em->persist($presence);
        $em->flush();

        return new Response(
            '<div class="alert alert-success">
                <i class="fas fa-check-circle"></i> 
                Présence enregistrée avec succès pour ' . htmlspecialchars($fidele->getNomfidele()) . '.
            </div>',
            200
        );
    }

    
/**
 * Génère un code unique pour le fidèle
 */
private function generateFideleCode(Eglise $eglise, string $nom): string
{
    $year = date('Y');

    $initial = strtoupper(substr($nom, 0, 1));

    $numero = $eglise->getLastFideleNumber() + 1;

    $eglise->setLastFideleNumber($numero);

    $numeroFormat = str_pad($numero, 5, '0', STR_PAD_LEFT);

    return $year.'-'.$initial.'-'.$numeroFormat;
}



}