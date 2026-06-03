<?php

namespace App\Controller;

use App\Entity\Invite;
use App\Form\InviteType;
use App\Repository\CulteRepository;
use App\Repository\FideleRepository;
use App\Repository\InviteRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Traits\ClientIp;

#[Route('/invite')]
class InviteController extends AbstractController {

    use ClientIp;

    #[Route('/', name: 'invite')]
    public function index(InviteRepository $inviteRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $invite = $inviteRepository->findBy(['eglise' => $eglise, "deletedBy" => NULL]);
        return $this->render('invite/index.html.twig', [
                    'invite' => $invite,
        ]);
    }

    #[Route('/{id}/detail', name: 'invite_detail', methods: ['GET', 'POST'])]
    public function detail(Invite $invite): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        return $this->render('invite/detail.html.twig', [
                    'invite' => $invite,
        ]);
    }

    #[Route('/{id}/update', name: 'invite_update', methods: ['GET', 'POST'])]
    #[Route('/add', name: 'invite_add', methods: ['GET', 'POST'])]
    public function add(EntityManagerInterface $entityManager, Request $request, CulteRepository $culteRepository, FideleRepository $fideleRepository, ?Invite $invite = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();
        $type = $invite === null ? 'add' : 'update';
        $invite = $invite === null ? new Invite() : $invite;
        $eglise = $this->getUser()->getEglise();
        $culte = $culteRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $fidele = $fideleRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);
        $form = $this->createForm(InviteType::class, $invite, ['culte' => $culte, 'fidele' => $fidele]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {


            if ($type === 'add') {
                $invite->setCreatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setEglise($user->getEglise())
                        ->setCreatedBy($user)
                ;
            } else {
                $invite->setUpdatedFromIp($this->GetIp()) // remplacement de la function par le trait
                        ->setUpdatedBy($user)
                ;
            }
            $entityManager->persist($invite);
            $entityManager->flush();
            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'invite_add' : 'invite';
            if ($nextAction) {
                $this->addFlash('success', 'Action effectuée avec succès.');
            }
            return $this->redirectToRoute($nextAction);
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('invite/add.html.twig', [
                    'invite' => $invite,
                    'form' => $form->createView(),
                    'response' => $response,
                        ], $response);
    }

    #[Route('invite/print', name: 'invite_print', methods: ['GET', 'POST'])]
    public function printinvite(InviteRepository $inviteRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $eglise = $this->getUser()->getEglise()->getId();
        $user = $this->getUser();
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        $invite = $inviteRepository->findBy(['eglise' => $eglise, "deletedAt" => NULL]);

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('invite/print.html.twig', [
            'invite' => $invite,
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (force download)
        $dompdf->stream("ekkllesia.pdf", [
            "Attachment" => false
        ]);
    }

    #[Route('/{id}', name: 'invite_delete', methods: ['POST'])]
    public function delete(Request $request, Invite $invite): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SECRETAIRE')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($this->isCsrfTokenValid('delete' . $invite->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();

            $invite->setDeletedFromIp($this->GetIp());
            $invite->setDeletedAt(new DateTime("now"));
            $user = $this->getUser();
            $invite->setDeletedBy($user);
            $this->addFlash('danger', 'Suppression avec succès.');

            $entityManager->flush();
        }

        return $this->redirectToRoute('invite');
    }

}
