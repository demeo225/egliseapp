<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
               ->add('roles', ChoiceType::class, [
                    'required' => true,
                    'multiple' => false,
                    'expanded' => false,
                    'attr' => array('class' => 'select2'),
                    'choices' => [
                        'Pasteur' => 'ROLE_PASTEUR',
                        'Pasteur Second' => 'ROLE_SECRETAIRE',
                        'Secretaire' => 'ROLE_SECRETAIRE',
                        'Responsable evangelisation' => 'ROLE_RESPONSABLE_EVANGELISATION',
                        'Responsable evangelisation & departement' => 'ROLE_EVANGELISATION_DEPARTEMENT',
                        'Responsable evangelisation & zone' => 'ROLE_EVANGELISATION_ZONE',
                        'Trésorerie' => 'ROLE_RESPONSABLE_TRESORERIE',
                        'Responsable Finance' => 'ROLE_RESPONSABLE_FINANCE',
                        'Responsable Finance National' => 'ROLE_RESPONSABLE_FINANCE_NATIONAL',
                        'Responsable conjugal' => 'ROLE_RESPONSABLE_CONJUGAL',
                        'Responsable du social' => 'ROLE_RESPONSABLE_SOCIAL',
                        'Responsable Cellule' => 'ROLE_RESPONSABLE_CELLULE',
                        'Responsable Departement' => 'ROLE_RESPONSABLE_DEPARTEMENT',
                        'Responsable Famille' => 'ROLE_RESPONSABLE_FAMILLE',
                        'Responsable Groupe' => 'ROLE_RESPONSABLE_GROUPE',
                        'Responsable Groupe & Cellule' => 'ROLE_GROUPE_CELLULE',
                        'Responsable Zone' => 'ROLE_RESPONSABLE_ZONE',
                        'Moniteur' => 'ROLE_RESPONSABLE_ECODIM',
                        'Ecodim & Departement & Cellule' => 'ROLE_MODERATEUR',
                        'Ecodim & Groupe & Cellule' => 'ROLE_ECODIM_GROUPE_CELLULE',
                        'Ecodim & Groupe ' => 'ROLE_ECODIM_GROUPE',
                        'Departement & Cellule' => 'ROLE_DEPARTEMENT_CELLULE',
                        'Zone & Departement' => 'ROLE_SUPERVISEUR',
                        'Administrateur' => 'ROLE_ADMIN',
                        
                    ],
                ])
                ->add('nomuser', TextType::class, [
                    'required' => true,
                ])
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
                ->add('email', EmailType::class, [
                    'label' => 'Adresse email',
                    'required' => true,
                    'attr' => [
                        'placeholder' => 'infos@lynovatechcom',
                    ],
                    'constraints' => [
                        new NotBlank(),
                        new Email(),
                    ],
                ])
                ->add('plainPassword', RepeatedType::class, [
                    'type' => PasswordType::class,
                    'first_options' => ['label' => 'Mot de passe'],
                    'second_options' => ['label' => 'Confirmation mot de passe'],
                    'invalid_message' => 'Les champs mot de passe doivent être identiques',
                    'constraints' => [
                        new NotBlank(),
                        new Length([
                            'min' => 4,
                            'minMessage' => 'Doit comporter 4 caractères ou plus',
                                ]),
                    ],
                ])
                ->add('save', SubmitType::class, ['label' => 'Valider'])
        ;
        // roles field data transformer
           $builder->get('roles')
                ->addModelTransformer(new CallbackTransformer(
                                function ($rolesArray) {
// transform the array to a string
                                    return count($rolesArray) ? $rolesArray[0] : null;
                                },
                                function ($rolesString) {
// transform the string back to an array
                                    return [$rolesString];
                                }
        ));
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }

}
