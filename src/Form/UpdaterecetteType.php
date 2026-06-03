<?php

namespace App\Form;

use App\Entity\Objetrecette;
use App\Entity\Recette;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UpdaterecetteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $objetrecette = $options['objetrecette'];
        $builder

        ->add('objetrecette', EntityType::class,  [
            'class' => Objetrecette::class,
            'choice_label' => 'libelle',
            'choices'=>$objetrecette,
            'attr' => array('class' => 'select2'),
            'mapped' => true,
            'required' => true,
            'placeholder' => '-- Choix Objet recette --',

        ])
        ->add('daterecette' , DateType::class, [
            // renders it as a single text box
            'widget' => 'single_text',
            'required' => true,
            // this is actually the default format for single_text
            'format' => 'yyyy-MM-dd',
        ])

        ->add('typeoff', ChoiceType::class, [
            'required' => true,
            'multiple' => false,
            'mapped' => false,
            'expanded' => true,
            'choices' => [
                'Ajout' => ' 1',
                'Reduction' => ' 0',
            ],
        ])
        ->add('ajout')
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

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recette::class,
            'objetrecette'=>null,

        ]);
    }
}
