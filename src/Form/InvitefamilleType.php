<?php

namespace App\Form;

use App\Entity\Invitefamille;
use App\Entity\Seancefamille;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;

class InvitefamilleType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $seancefamille = $options['seancefamille'];

        $builder
                ->add('nom', TextType::class, [
                    'required' => false,
                                ])
                ->add('contact', TextType::class, [
                    'required' => false,
                    'constraints' => [
                        new RegEx("#^[0-9/? ?]{10,16}$#")
                    ],
                ])
                ->add('habitation')
                ->add('sexe', ChoiceType::class, [
                    'required' => true,
                    'multiple' => false,
                    'expanded' => false,
                    'choices' => [
                        'Homme' => 'Homme',
                        'Femme' => 'Femme',
                    ],
                ])
                ->add('fonction')
                ->add('seancefamille', EntityType::class, [
                    'class' => Seancefamille::class,
                    'choices' => $seancefamille,
                    'placeholder' => '-- Choix de la date --',
                    'attr' => array('class' => 'select2'),
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
            'data_class' => Invitefamille::class,
            'seancefamille' => null,
        ]);
    }

}
