<?php

namespace App\Form;

use App\Entity\Eglise;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\UX\Dropzone\Form\DropzoneType;

class EglisedemandeType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('congregation', TextType::class, ['required' => true,])
                ->add('photo', DropzoneType::class, [
                    'required' => false,
                    'mapped' => false,
                    
                    'attr' => [
                    ],
                ])
                ->add('annee', IntegerType::class, [
                    'required' => true,
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Année d\'implatation requise',
                                ]),
                        new Length([
                            'min' => 4,
                            'minMessage' => 'L\'année doit contenir 4 chiffres',
                            // max length allowed by Symfony for security reasons
                            'max' => 4,
                                ]),
                    ],
                ])
                ->add('administrateur', EmailType::class, ['required' => true,])
                ->add('denomination', TextType::class, ['required' => true,])
                //->add('arrete', TextType::class, ['required' => false,])
                ->add('contact1', TextType::class, [
                    'required' => false,
                    'constraints' => [
                        new Regex("#^[0-9/? ?]{10,16}$#")
                    ],
                ])
                ->add('contact2', TextType::class, [
                    'required' => false,
                    'constraints' => [
                        new Regex("#^[0-9/? ?]{10,16}$#")
                    ],
                ])
                ->add('adresse', TextType::class, ['required' => false,])
                ->add('texte', TextType::class, ['required' => false,])
//                ->add('logo')
                ->add('quartier', TextType::class, ['required' => true,])
                ->add('verset', TextType::class, ['required' => false,])
                ->add('sigle', TextType::class, ['required' => false,])
                ->add('facebook', TextType::class, ['required' => false,])
                ->add('agrement', TextType::class, ['required' => false,])
                ->add('commune', TextType::class, ['required' => true,])
                ->add('regionpastorale', TextType::class, ['required' => false,])
                ->add('nomuser', TextType::class, [
                    'mapped' => false,
                    'required' => true,
                ])
                ->add('prenom', TextType::class, [
                    'mapped' => false,
                ])
                ->add('photo1', DropzoneType::class, [
                    'required' => false,
                    'mapped' => false,
                    
                    'attr' => [
                    ],
                ])
                ->add('email', EmailType::class, [
                    'label' => 'Adresse email',
                    'mapped' => false,
                    'required' => true,
                    'attr' => [
                        'placeholder' => 'votre@domaine.com',
                    ],
                    'constraints' => [
                        new NotBlank(),
                        new Email(),
                    ],
                ])
                ->add('plainPassword', RepeatedType::class, [
                    'type' => PasswordType::class,
                    'mapped' => false,
                    'first_options' => ['label' => 'Mot de passe'],
                    'second_options' => ['label' => 'Confirmation mot de passe'],
                    'invalid_message' => 'Les champs mot de passe doivent être identiques',
                    'constraints' => [
                        new NotBlank(),
                        new Length([
                            'min' => 6,
                            'minMessage' => 'Doit comporter 6 caractères ou plus',
                                ]),
                    ],
                ])


        ;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => Eglise::class,
        ]);
    }

}
