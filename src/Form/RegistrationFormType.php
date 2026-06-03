<?php

namespace App\Form;

use App\Entity\Cellule;
use App\Entity\Departement;
use App\Entity\Famille;
use App\Entity\Groupe;
use App\Entity\User;
use App\Entity\Zone;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use XMLDiff\File;

class RegistrationFormType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('email', EmailType::class, [
                    'label' => 'Email',
                    // unmapped means that this field is not associated to any entity property
                    'mapped' => false,
                    // make it optional so you don't have to re-upload the PDF file
                    // every time you edit the Product details
                    'required' => true]
                )
                         ->add('roles', ChoiceType::class, [
                    'required' => true,
                    'multiple' => false,
                    'expanded' => false,
                    'choices' => [
                        'Aministrateur' => 'ROLE_ADMIN',
                        'Pasteur' => 'ROLE_PASTEUR',
                        'Secretaire' => 'ROLE_SECRETAIRE',
                        'Responsable evangelisation' => 'ROLE_RESPONSABLE_EVANGELISATION',
                        'Responsable Finance' => 'ROLE_RESPONSABLE_FINANCE',
                        'Responsable conjugal' => 'ROLE_RESPONSABLE_CONJUGAL',
                        'Responsable du social' => 'ROLE_RESPONSABLE_SOCIAL',
                        'Responsable Cellule' => 'ROLE_RESPONSABLE_CELLULE',
                        'Responsable Departement' => 'ROLE_RESPONSABLE_DEPARTEMENT',
                        'Responsable Groupe' => 'ROLE_RESPONSABLE_GROUPE',
                        'Responsable Famille' => 'ROLE_RESPONSABLE_FAMILLE',
                        'Moderateur de groupe' => 'ROLE_SUPERVISEUR_GROUPE',
                        'Responsable Zone' => 'ROLE_RESPONSABLE_ZONE',
                        'Moniteur' => 'ROLE_RESPONSABLE_ECODIM',
                        'Responsable Departement & Famille' => 'ROLE_DEPARTEMENT_FAMILLE',
                        'Superviseur' => 'ROLE_ECODIM_CELLULE_GROUPE',
                    ],
                ])
                ->add('nomuser', TextType::class)
                ->add('prenom', TextType::class)
                ->add('photo', FileType::class, [
                    'label' => 'Image profil',
                    'required' => false,
                    'data_class' => null,
                    'constraints' => [
                        new File([
                            'maxSize' => '1024k',
                            'mimeTypes' => [
                                'image/png',
                                'image/jpeg',
                                'image/gif',
                                'image/ico',
                            ],
                            'mimeTypesMessage' => 'Veuillez choisir une photo',
                                ]),
                    ],
                ])
                ->add('plainPassword', RepeatedType::class, [
                    'type' => PasswordType::class,
                    'invalid_message' => 'Les mots de passe ne correspondent pas.',
                    'required' => true,
                    'first_options' => ['label' => 'Password'],
                    'second_options' => ['label' => 'Repeat Password'],
                    // instead of being set onto the object directly,
                    // this is read and encoded in the controller
                    'mapped' => false,
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Please enter a password',
                                ]),
                        new Length([
                            'min' => 6,
                            'minMessage' => 'Your password should be at least {{ limit }} characters',
                            // max length allowed by Symfony for security reasons
                            'max' => 4096,
                                ]),
                    ],
                ])
                ->add('cellule', EntityType::class, [
                    'class' => Cellule::class,
                    'choice_label' => 'nom',
                    'placeholder' => ' -- Cellule --',
                    'required' => false,
                    'attr' => array('class' => 'select2'),
                ])
                ->add('famille', EntityType::class, [
                    'class' => Famille::class,
                    'choice_label' => 'nom',
                    'placeholder' => '-- Tribu --',
                    'required' => false,
                    'attr' => array('class' => 'select2'),
                ])
                ->add('groupe', EntityType::class, [
                    'class' => Groupe::class,
                    'choice_label' => 'nom',
                    'placeholder' => '-- Groupe --',
                    'required' => false,
                    'attr' => array('class' => 'select2'),
                ])
                ->add('departement', EntityType::class, [
                    'class' => Departement::class,
                    'choice_label' => 'nom',
                    'placeholder' => '-- Departement --',
                    'required' => false,
                    'attr' => array('class' => 'select2'),
                ])
                ->add('zone', EntityType::class, [
                    'class' => Zone::class,
                    'choice_label' => 'nom',
                    'placeholder' => 'Choix de la zone',
                    'required' => false,
                    'attr' => array('class' => 'select2'),
                ])
                ->add('save', SubmitType::class, ['label' => 'Valider'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }

}
