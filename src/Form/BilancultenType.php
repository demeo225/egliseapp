<?php

namespace App\Form;

use App\Entity\Region;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BilancultenType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $region = $options['region'];
        $builder
                ->add('dateDebut', DateType::class, [
                    // renders it as a single text box
                    'widget' => 'single_text',
                    'required' => true,
                    // this is actually the default format for single_text
                    'format' => 'yyyy-MM-dd',
                        ]
                )
                ->add('dateFin', DateType::class, [
                    // renders it as a single text box
                    'widget' => 'single_text',
                    'required' => true,
                    // this is actually the default format for single_text
                    'format' => 'yyyy-MM-dd',
                        ]
                )
                    ->add('region', EntityType::class, [
                    'class' => Region::class,
                    'choice_label' => 'libelle',
                    'choices' => $region,
                    'placeholder' => 'Choix de la region',
                    'required' => false,
                    'mapped' => true,
                    'attr' => array('class' => 'select2'),
                ])
                ->add('typeculte', ChoiceType::class, [
                    'required' => false,
                    'multiple' => false,
                    'placeholder' => '-- Categorie culte --',
                    'expanded' => false,
                    'choices' => [
                        'Culte ordinaire' => 'Culte ordinaire',
                        'Culte du soir' => 'Culte du soir',
                        'Prière du matin' => 'Prière du matin',
                        'Veillée de prière' => 'Veillée de prière',
                        'Seminaire' => 'Seminaire',
                        'Autre' => ' Autre',
                    ],
                ])
                ->add('Rechercher', SubmitType::class)

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefaults([
                // Configure your form options here
            'region' => null,
        ]);
    }

}
