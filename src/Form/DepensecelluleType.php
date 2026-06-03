<?php

namespace App\Form;

use App\Entity\Cellule;
use App\Entity\Depensecellule;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DepensecelluleType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options): void {
       // $cellule = $options['cellule'];

        $builder
                ->add('datedepense', DateType::class, [
                    // renders it as a single text box
                    'widget' => 'single_text',
                    'required' => true,
                    // this is actually the default format for single_text
                    'format' => 'yyyy-MM-dd',
                ])
                ->add('objet', ChoiceType::class, [
                    'required' => true,
                    'multiple' => false,
                    'expanded' => false,
                    'choices' => [
                        'Versement au temple' => 'Versement au temple',
                        'Soutien' => 'Soutien',
                        'Autres' => 'Autres',
                    ],
                ])
                // ->add('cellule', EntityType::class, [
                //     'class' => Cellule::class,
                //     'choices' => $cellule,
                //     'required' => true,
                //     'attr' => array('class' => 'select2'),
                // ])
                ->add('montant')
                ->add('detail', TextType::class)
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
            'data_class' => Depensecellule::class,
           // 'cellule' => null,
        ]);
    }

}
