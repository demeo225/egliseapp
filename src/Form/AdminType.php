<?php

namespace App\Form;

use App\Entity\Eglise;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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

class AdminType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('roles', ChoiceType::class, [
                    'required' => true,
                    'multiple' => false,
                    'expanded' => false,
                    'choices' => [
                        'Administrateur' => 'ROLE_ADMIN',
                        'President' => 'ROLE_PRESIDENT',
                    ],
                ])
                ->add('nomuser', TextType::class, [
                    'required' => true,
                ])
                ->add('prenom', TextType::class)
//                ->add('eglise', EntityType::class, [
//                    'class' => Eglise::class,
//                    'choice_label' => 'denomination',
//                    'placeholder' => 'Choix Eglise',
//                    'attr' => array('class' => 'select2'),
//                    'required' => true,
//                ])
                ->add('eglise', EntityType::class, array(
                    'choice_label' => function ($eglise) {
                        return $eglise->getCode() . ' ' . $eglise->getDenomination();
                    },
                    'class' => Eglise::class,
                    'multiple' => false,
                    'attr' => array('class' => 'select2'),
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('c')
                                ->where('c.etat = 1')
//                                ->setParameter(1, $er)
                                ->orderBy('c.id', 'ASC');
                    },
                ))
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
                        'placeholder' => 'infos@lynova.tech',
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
//                ->add('save', SubmitType::class, ['label' => 'Valider'])
                ->add('saveAndAdd', SubmitType::class, [
                    'attr' => [
                        'value' => 'save-and-add'
                    ]
                ])
                ->add('save', SubmitType::class, [
                    'attr' => [
                        'value' => 'create-don'
                    ]
                ])
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
