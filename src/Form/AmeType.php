<?php

namespace App\Form;

use App\Entity\Ame;
use App\Entity\Evangelisation;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class AmeType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options): void {
               $evangelisation = $options['evangelisation'];

        $builder
                ->add('nom')
                ->add('sexe', ChoiceType::class, [
                    'required' => true,
                    'multiple' => false,
                    'expanded' => false,
                    'choices' => [
                        'Garçon' => 'Garçon',
                        'Fille' => 'Fille',
                    ],
                ])
                ->add('contact', TextType::class, ['required' => false, 'constraints' => [
                        new Length([
                            'min' => 10,
                            'minMessage' => 'Taille du numero',
                            // max length allowed by Symfony for security reasons
                            'max' => 14,
                                ]),
                    ],])
                ->add('habitation')
                ->add('rdv', ChoiceType::class, [
                    'required' => true,
                    'multiple' => false,
                    'expanded' => false,
                    'choices' => [
                        'Oui' => 'Oui',
                        'Non' => 'Non',
                    ],
                ])
                ->add('converti', ChoiceType::class, [
                    'required' => true,
                    'multiple' => false,
                    'expanded' => false,
                    'choices' => [
                        'Oui' => 'Oui',
                        'Non' => 'Non',
                    ],
                ])
                ->add('invitation', ChoiceType::class, [
                    'required' => true,
                    'multiple' => false,
                    'expanded' => false,
                    'choices' => [
                        'Oui' => 'Oui',
                        'Non' => 'Non',
                    ],
                ])
                 ->add('evangelisation', EntityType::class, [
                    'class' => Evangelisation::class,
                    'choices' => $evangelisation,
                    'required' => false,
                    'mapped' => true,
                    'placeholder' => '-- Evangelisation --',
                    'attr' => array('class' => 'select2')
                ])
                ->add('save', SubmitType::class, [
                    'attr' => [
                        'value' => 'create-don'
                    ]
                ])
                ->add('saveAndAdd', SubmitType::class, [
                    'attr' => [
                        'value' => 'save-and-add'
                    ]
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefaults([
            'data_class' => Ame::class,
                        'evangelisation' => null,

        ]);
    }

}
