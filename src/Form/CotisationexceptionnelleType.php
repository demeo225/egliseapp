<?php

namespace App\Form;

use App\Entity\Cotisationexceptionnelle;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class CotisationexceptionnelleType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('objet', TextType::class)
                ->add('montant')
                ->add('dixmille')
                ->add('cinqmille')
                ->add('deuxmille')
                ->add('mille')
                ->add('centbillet')
                ->add('centpiece')
                ->add('deuxcent')
                ->add('cent')
                ->add('cinquante')
                ->add('vingtcinq')
                ->add('dix')
                ->add('cinq')
                ->add('beneficiaire')
                ->add('datecotise', DateType::class, [
                    // renders it as a single text box
                    'widget' => 'single_text',
                    'required' => false,
                    // this is actually the default format for single_text
                    'format' => 'yyyy-MM-dd',
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

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => Cotisationexceptionnelle::class,
        ]);
    }

}
