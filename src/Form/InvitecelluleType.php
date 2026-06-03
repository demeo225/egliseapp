<?php

namespace App\Form;

use App\Entity\Invitecellule;
use App\Entity\Seancecellule;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;

class InvitecelluleType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $seancecellule = $options['seancecellule'];

        $builder
                ->add('nom', TextType::class, [
                    'required' => true,
                   
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
                ->add('seancecellule', EntityType::class, [
                    'class' => Seancecellule::class,
                    'choices' => $seancecellule,
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
            'data_class' => Invitecellule::class,
            'seancecellule' => null,
        ]);
    }

}
