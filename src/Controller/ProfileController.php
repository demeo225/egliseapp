<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\EditProfileType;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use function Sodium\bin2hex;

class ProfileController extends AbstractController
{
    /**
     * @Route("/profil", name="profil")
     */
    public function homeAction()
    {
        // Recupere l'utilisateur courant
        $user = $this->getUser();

        if (null === $user) {
          return $this->redirectToRoute('home');
        }
        
        return $this->render('profile/index.html.twig');
    }

    
    
    /**
     * @Route("/profile/editpass", name="edit_pass")
     */
    public function editPassAction(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        if($request->isMethod('POST')){
            $em = $this->getDoctrine()->getManager();
            $user = $this->getUser();
        if($request->request->get('pass') == $request->request->get('pass2'));
            $user->setPassword($passwordEncoder->encodePassword($user, $request->request->get('pass')));
            $em->flush();
            return $this->redirectToRoute('app_logout');
            $this->addFlash('success', 'Modification avec succès');
        } else {
               $this->addFlash(
                    'error',
                    'Les 2 mots de passe ne correspondent pas.'
                );  
        }
        return $this->render('profile/editpass.html.twig');     
           $em->flush();
           $this->addFlash('message', 'Changement de mot de passe avec succès');
    }
    
    
    
    /**
     * @Route("/profile/editprofile", name="edit_profil")
     */
    public function editerProfilAction(Request $request, SluggerInterface $slugger, string $photoDir = null)
    {
        // Recupere l'utilisateur courant
        $user = $this->getUser();

        if (null === $user) {
            return $this->redirectToRoute('home');
        }

        // Creation du formulaire d'edition
        $editUserForm = $this->createForm(EditProfileType::class, $user);

        // On indique au formulaire de prendre en charge le contenu de la requete
        // Il va mapper les different champs soumis avec le contenu de l entite $user
        $editUserForm->handleRequest($request);

        if ($request->isMethod('POST')) {
        if ($editUserForm->isSubmitted()) {
                
                //            Insertion image de profile
            if ($photo = $editUserForm['photo']->getData()) {
                $filename = bin2hex(random_bytes(6)) . '.' . $photo->guessExtension();
                try {
                    $photo->move($photoDir, $filename);
                } catch (FileException $e) {
                    // unable to upload the photo, give up
                }
                $user->setPhotoFile($filename);
            }
                
                $em = $this->getDoctrine()->getManager();

////                // Region
//                $region = $em->getRepository(Region::class)->findOneBy(['id' => $editUserForm['user_region']->getData()]);
//                $user->setUserRegion($region);
////
////                // Department
//                $department = $em->getRepository(Department::class)->findOneBy(['id' => $editUserForm['user_department']->getData()]);
//                $user->setUserDepartment($department);
////
////                // Level
//                $level = $em->getRepository(Level::class)->findOneBy(['id' => $editUserForm['user_level']->getData()]);
//                $user->setUserLevel($level);
//
//                // Encodage du mot de passe
//                $user->setPassword($passwordEncoder->encodePassword(
//                    $user,
//                    $user->getPassword()
//                ));
                // definition du role ROLE_USER
//                $user->setRoles(['ROLE_USER']);

      
                $token = rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
//                $user->setUserConfirmationToken($token);
                $em->persist($user);
                $em->flush();

                // TO DO ENVOYER UN MAIL DE CONFIRMATION POUR ACTIVER LE COMPTE

                $this->addFlash(
                    'success',
                    'Vos modifications ont bien etes enregistrees.'
                );

                return $this->redirectToRoute('home');
            }
        }

        // Affichage
        return $this->render('profile/editprofile.html.twig', [
            'form' => $editUserForm->createView(),
            'libAction' => 'Modifier'
        ]);
    }
    
}

