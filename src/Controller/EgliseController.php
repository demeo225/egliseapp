<?php

namespace App\Controller;

use App\Entity\Eglise;
use App\Entity\Solde;
use App\Entity\User;
use App\Form\EglisedemandeType;
use App\Form\EgliseType;
use App\Form\EglisevalidationType;
use App\Repository\EgliseRepository;
use App\Traits\ClientIp;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use function Sodium\bin2hex;

//#[IsGranted('ROLE_SUPER_ADMIN')]
#[Route('/eglise')]
class EgliseController extends AbstractController {

    use ClientIp;

    #[Route('/', name: 'eglise', methods: ['GET'])]
    public function index(EgliseRepository $egliseRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $egliseRepository->findBy(['etat' => 1, 'editable' => 1]);
        return $this->render('eglise/index.html.twig', [
                    'eglises' => $eglise,
        ]);
    }

    #[Route('/listesup', name: 'eglise_supp', methods: ['GET'])]
    public function index2(EgliseRepository $egliseRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = $egliseRepository->findBy(['etat' => 0, 'editable' => 1]);
        return $this->render('eglise/sup.html.twig', [
                    'eglises' => $eglise,
        ]);
    }

    #[Route('/new', name: 'eglise_new', methods: ['GET', 'POST'])]
    public function new(EntityManagerInterface $entityManager, Request $request, string $photoDir = null, EgliseRepository $egliseRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $eglise = new Eglise();
        $form = $this->createForm(EgliseType::class, $eglise);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            //Adresse ip de l'utilisateur

            $eglise->setCreatedFromIp($this->GetIp());
            //            AJout image
            if ($photo = $form['photo']->getData()) {
                $filename = bin2hex(random_bytes(6)) . '.' . $photo->guessExtension();
                try {
                    $photo->move($photoDir, $filename);
                } catch (FileException $e) {
                    // unable to upload the photo, give up
                }
                $eglise->setLogo($filename);
            }
            $user = $this->getUser();
//            $eglise->setUpdatedBy($user);
            $eglise->setCreatedBy($user);
            // Etablir le lien entre le Code et l'ID de l'Eglise
            $listeeglise = $egliseRepository->findBy(array(), array('id' => 'desc'), 1, 0);
            $id = 0;
            foreach ($listeeglise as $value) {
                $id = $value->getId();
            }
            $val = $id + 1;
            $eglisenom = $form['denomination']->getData();
            $denomoniation = substr($eglisenom, 0, 3);
            $an = $form['annee']->getData();
            $code1 = $denomoniation . $an . $val;

            $eglise->setCode($code1);
            $eglise->setEtat(1);
                $soldeEglise = new Solde;
            $soldeEglise->setEglise($eglise);
            $soldeEglise->setMontant(0);
            
            $entityManager->persist($soldeEglise);
            $entityManager->persist($eglise);
            $entityManager->flush();
//            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'eglise_new' : 'eglise';
//            if ($nextAction) {
//                $this->addFlash('success', 'Enregistrement avec succès.');
//            }
            return $this->redirectToRoute('eglise');
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('eglise/new.html.twig', [
                    'eglise' => $eglise,
                    'form' => $form->createView(),
                        ],);
    }

    #[Route('/demande', name: 'eglise_demande', methods: ['GET', 'POST'])]
    public function demande(EntityManagerInterface $entityManager, Request $request, UserPasswordEncoderInterface $passwordEncoder, SluggerInterface $slugger, string $photoDir = null, EgliseRepository $egliseRepository): Response {

        $eglise = new Eglise();
        $form = $this->createForm(EglisedemandeType::class, $eglise);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {



            if ($photo = $form['photo']->getData()) {
                $filename = bin2hex(random_bytes(6)) . '.' . $photo->guessExtension();
                try {
                    $photo->move($photoDir, $filename);
                } catch (FileException $e) {
                    // unable to upload the photo, give up
                }
                $eglise->setLogo($filename);
            }

            $listeeglise = $egliseRepository->findBy(array(), array('id' => 'desc'), 1, 0);
            $id = 0;
            foreach ($listeeglise as $value) {
                $id = $value->getId();
            }
            $val = $id + 1;
            $eglisenom = $form['denomination']->getData();
            $denomoniation = substr($eglisenom, 0, 3);

            $an = $form['annee']->getData();
            $code1 = $denomoniation . $an . $val;

            $eglise->setCode($code1);
            $eglise->setEtat(0);
            $eglise->setEditable(0);

            $nomuser = $form->get('nomuser')->getData();
            $prenom = $form->get('prenom')->getData();

            $email = $form->get('email')->getData();

            // 3) Encode the password (you could also do this via Doctrine listener)

            $user = new User();
            $user->setNomuser($nomuser);
            $user->setPrenom($prenom);
            $user->setEmail($email);
            $plainPassword = $form->get('plainPassword')->getData();

            // hash the password (based on the security.yaml config for the $user class)
            $hashedPassword = $passwordEncoder->encodePassword(
                    $user,
                    $plainPassword
            );
            $user->setPassword($hashedPassword);

            $user->setRoles(['ROLE_ADMIN']);
            $user->setEglise($eglise);

            if ($photo = $form['photo1']->getData()) {
                $filename = \bin2hex(random_bytes(6)) . '.' . $photo->guessExtension();
                try {
                    $photo->move($photoDir, $filename);
                } catch (FileException $e) {
                    // unable to upload the photo, give up
                }
                $user->setPhotoFile($filename);
            }
            $soldeEglise = new Solde;
            $soldeEglise->setEglise($eglise);
            $soldeEglise->setMontant(0);
            
            $entityManager->persist($soldeEglise);
            $entityManager->persist($eglise);
            $entityManager->persist($user);

            $entityManager->flush();

            return $this->redirectToRoute('eglise_success');
        }
        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('eglise/demande.html.twig', [
                    'eglise' => '$eglise',
                    'form' => $form->createView(),
                        ],);
    }

    #[Route('/success', name: 'eglise_success', methods: ['GET', 'POST'])]
    public function success(): Response {

        return $this->render('eglise/succes.html.twig', [
        ]);
    }

    #[Route('/{id}/validation', name: 'eglise_validation', methods: ['GET', 'POST'])]
    public function validation(Request $request, Eglise $eglise, string $photoDir = null, EgliseRepository $egliseRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $data = json_decode($request->getContent(), true);

        $form = $this->createForm(EglisevalidationType::class, $eglise,);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //Adresse ip de l'utilisateur

            $eglise->setCreatedFromIp($this->GetIp());
            //            Modification image
            if ($photo = $form['photo']->getData()) {
                $filename = bin2hex(random_bytes(6)) . '.' . $photo->guessExtension();
                try {
                    $photo->move($photoDir, $filename);
                } catch (FileException $e) {
                    // unable to upload the photo, give up
                }
                $eglise->setLogo($filename);
            }
            $user = $this->getUser();
            $eglise->setUpdatedBy($user);
            $eglise->setCreatedBy($user);
            $eglise->setDeleted2At(NULL);

            $eglise->setEtat(1);
            $eglise->setEditable(1);
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('eglise');
        }

        return $this->render('eglise/validation.html.twig', [
                    'eglise' => $eglise,
                    'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/detaildemande', name: 'eglise_detaildemande', methods: ['GET', 'POST'])]
    public function detaildemande(Eglise $eglise): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        return $this->render('eglise/detaildemande.html.twig', [
                    'eglise' => $eglise,
        ]);
    }

    #[Route('/listedemande', name: 'eglise_listedemande', methods: ['GET', 'POST'])]
    public function indexdemande(EgliseRepository $egliseRepository): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        return $this->render('eglise/listedemande.html.twig', [
                    'eglises' => $egliseRepository->findBy(['etat' => 0, 'editable' => 0]),
        ]);
    }

    #[Route('/{id}', name: 'eglise_show', methods: ['GET'])]
    public function show(Eglise $eglise): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        return $this->render('eglise/show.html.twig', [
                    'eglise' => $eglise,
        ]);
    }

    #[Route('/{id}/edit', name: 'eglise_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Eglise $eglise, string $photoDir = null): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        $form = $this->createForm(EgliseType::class, $eglise);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //Adresse ip de l'utilisateur

            $eglise->setUpdatedFromIp($this->GetIp());
            //            Modification image
            if ($photo = $form['photo']->getData()) {
                $filename = bin2hex(random_bytes(6)) . '.' . $photo->guessExtension();
                try {
                    $photo->move($photoDir, $filename);
                } catch (FileException $e) {
                    // unable to upload the photo, give up
                }
                $eglise->setLogo($filename);
            }
            $user = $this->getUser();
            $eglise->setUpdatedBy($user);
//            $eglise->setCreatedBy($user);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('eglise');
        }

        return $this->render('eglise/edit.html.twig', [
                    'eglise' => $eglise,
                    'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'eglise_delete', methods: ['POST'])]
    public function delete(Request $request, Eglise $eglise): Response {
        if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($this->isCsrfTokenValid('delete' . $eglise->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();

            $eglise->setDeletedFromIp($this->GetIp());
            $eglise->setDeleted2At(new DateTime("now"));
            $user = $this->getUser();
            $eglise->setEtat(0);
            $eglise->setDeletedBy($user);
            $this->addFlash('message', 'Fermeture du compte avec succès.');

            $entityManager->flush();
        }

        return $this->redirectToRoute('eglise');
    }

    #[Route('/{id}/eglise/', name: 'eglise_active', methods: ['POST'])]
    public function activeEglise(Request $request, Eglise $eglise): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($this->isCsrfTokenValid('active' . $eglise->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();

            $eglise->setDeletedFromIp($this->GetIp());
            $eglise->setDeleted2At(NULL);
            $eglise->setEtat(1);
            $eglise->setEditable(1);

            //$user = $this->getUser();
            $this->addFlash('message', 'Activation du compte avec succès.');
            $entityManager->flush();
        }

        return $this->redirectToRoute('eglise');
    }

}
