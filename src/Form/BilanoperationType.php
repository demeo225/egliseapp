<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BilanoperationType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options): void {
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
                ->add('objet', ChoiceType::class, [
                    'required' => false,
                    'multiple' => false,
                    'expanded' => false,
                    'placeholder' => '-- Objet --',
                    'choices' => [
                        'Dons' => 'Dons',
                        'Facture CIE' => 'Facture CIE',
                        'Facture SODECI' => 'Facture SODECI',
                        'Frais de comission ' => 'Frais de comission',
                        'Facture INTERNET' => 'Facture INTERNET',
                        'Salaire pasteur' => 'Salaire pasteur',
                        'Loyer pasteur' => 'Loyer pasteur',
                        'Loyer du temple' => 'Loyer du temple',
                        'Dîme des dîmes' => 'Dîme des dîmes',
                        'Soutien aux fidèles' => 'Soutien aux fidèles',
                        'Autres' => 'Autres',
                    ],
                ])
                ->add('Rechercher', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefaults([
                // Configure your form options here
        ]);
    }

}
