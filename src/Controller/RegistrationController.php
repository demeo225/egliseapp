<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AdminType;
use App\Form\RegistrationFormType;
use App\Form\UpdateuserType;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use DateTime;
use function Sodium\bin2hex;

class RegistrationController extends AbstractController {

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/registration", name="registration_index")
     */
    public function index(UserRepository $userRepository): Response {
        $em = $this->getDoctrine()->getManager();
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $user = $userRepository->findBy(['eglise' => $eglise, "etat" => 1]);
        return $this->render('registration/index.html.twig', [
                    'user' => $user,
        ]);
    }
    
    
      
        /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/archive_user", name="registration_archive")
     */
    public function archiveUser(UserRepository $userRepository): Response {
        $em = $this->getDoctrine()->getManager();
        $eglise = $this->getUser()->getEglise();
        $user = $this->getUser();
        $user = $userRepository->findBy(['eglise' => $eglise,"etat" => 0]);
        return $this->render('registration/archive.html.twig', [
                    'user' => $user,
        ]);
    }
    
    
    
    #[Route('/{id}/user', name: 'registration_delete', methods: ['POST'])]

    public function delete(Request $request, User $user): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();

//            function getIp() {
//                if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
//                    $ip = $_SERVER['HTTP_CLIENT_IP'];
//                } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
//                    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
//                } else {
//                    $ip = $_SERVER['REMOTE_ADDR'];
//                }
//                return $ip;
//            }
//
//            $ip = getIp();
//            $user->setDeletedFromIp($ip);
                        $user->setEtat(0);

            $user->setDeletedAt(new DateTime("now"));
            //$user = $this->getUser();
          //  $user->setDeletedBy($user);
             $this->addFlash('warning', 'Fermeture du compte avec succès.');
            $entityManager->flush();
        }

        return $this->redirectToRoute('registration_index');
    }

    #[Route('/register', name: 'register')]
    public function registerAction(Request $request, UserPasswordEncoderInterface $passwordEncoder, SluggerInterface $slugger, string $photoDir = null) {
        // 1) Création du formulaire
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        // 2) handle the submit (will only happen on POST)
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
//      
//            Insertion image de profile
            if ($photo = $form['photo']->getData()) {
                $filename = \bin2hex(random_bytes(6)) . '.' . $photo->guessExtension();
                try {
                    $photo->move($photoDir, $filename);
                } catch (FileException $e) {
                    // unable to upload the photo, give up
                }
                $user->setPhotoFile($filename);
            }
            // 3) Encode the password (you could also do this via Doctrine listener)
            $plainPassword = $form->get('plainPassword')->getData();
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);
            $eglise = $this->getUser()->getEglise();
            $user->setEglise($eglise);
            //on active par défaut
            $user->setEtat(1);
            //$user->addRole("ROLE_ADMIN");
            // 4) save the User!
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            // ... do any other work - like sending them an email, etc
            // maybe set a "flash" success message for the user
            $this->addFlash('success', 'Création du compte avec succès.');
            return $this->redirectToRoute('registration_index');

            //return $this->redirectToRoute('login');
        }
        return $this->render('registration/register.html.twig', ['form' => $form->createView(), 'mainNavRegistration' => true, 'title' => 'Inscription']);
    }

//    /**
//     * @Route("/permute/enabled", name="ser_permute_enabled", methods="GET")
//     */
//    public function permuteEnabled(User $users, Request $request): Response {
//        $users = $this->userManager->getUsers();
//        $this->denyAccessUnlessGranted('back_user_permute_enabled', $users);
//        foreach ($users as $user) {
//            $permute = $user->getEnabled() ? false : true;
//            $user->setEnabled($permute);
//        }
//        $this->getDoctrine()->getManager()->flush();
//        return $this->redirectToRoute('back_user_search');
//    }

    //creation de la fonction update
    #[Route('/{id}/update', name: 'registration_update', methods: ['GET', 'POST'])]
    public function updateUser(Request $request, User $user): Response {
        $form = $this->createForm(UpdateuserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            $roles = $form['roles']->getData();
            $user->setRoles($roles);
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Modification effectuée avec succès.');
            return $this->redirectToRoute('registration_index');
        }

        return $this->render('registration/update.html.twig', [
                    'user' => $user,
                    'form' => $form->createView(),
        ]);
    }

    #[Route('/admin', name: 'admin')]
    public function registerAdmin(Request $request, UserPasswordEncoderInterface $passwordEncoder, SluggerInterface $slugger, string $photoDir = null) {
        // 1) Création du formulaire
        $user = new User();
        $form = $this->createForm(AdminType::class, $user);
        // 2) handle the submit (will only happen on POST)
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
//      
//            Insertion image de profile
            if ($photo = $form['photo']->getData()) {
                $filename = bin2hex(random_bytes(6)) . '.' . $photo->guessExtension();
                try {
                    $photo->move($photoDir, $filename);
                } catch (FileException $e) {
                    // unable to upload the photo, give up
                }
                $user->setPhotoFile($filename);
            }
            // 3) Encode the password (you could also do this via Doctrine listener)
            $plainPassword = $form->get('plainPassword')->getData();
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);

            //on active par défaut
            $user->setEtat(1);
            //$user->addRole("ROLE_ADMIN");
            // 4) save the User!
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            // ... do any other work - like sending them an email, etc
            // maybe set a "flash" success message for the user
            $this->addFlash('success', 'Compte crée avec succès.');
            return $this->redirectToRoute('registration_index');

            //return $this->redirectToRoute('login');
        }
        return $this->render('registration/admin.html.twig', ['form' => $form->createView(), 'mainNavRegistration' => true, 'title' => 'Ouverture de compte']);
    }

    
    
        
    #[Route('/{id}/user/', name: 'registration_active', methods: ['POST'])]

    public function validateUser(Request $request, User $user): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Accès réfusé, vous n\'avez pas les droits d\'accès ici!');
        }
        if ($this->isCsrfTokenValid('active' . $user->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();

            $user->setEtat(1);

            $user->setDeletedAt(NULL);
     
       
             $this->addFlash('success', 'Activation du compte avec succès.');
            $entityManager->flush();
        }

        return $this->redirectToRoute('registration_index');
    }

    

}
