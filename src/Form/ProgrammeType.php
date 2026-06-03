<?php

namespace App\Form;

use App\Entity\Programme;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProgrammeType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('title', TextType::class, [
                    'required' => true,
                ])
                ->add('start', DateTimeType::class, [
                    'date_widget' => 'single_text'
                ])
                ->add('end', DateTimeType::class, [
                    'date_widget' => 'single_text',
                    'required' => true,
                ])
                ->add('description')
                ->add('all_day', ChoiceType::class, [
                    'required' => true,
                    'multiple' => false,
                    'expanded' => true,
                    'choices' => [
                        'Non' => '0',
                        'Oui' => '1',
                    ],
                ])
                ->add('background_color', ColorType::class, [
                    'required' => true,
                ])
                ->add('border_color', ColorType::class, [
                    'required' => true,
                ])
                ->add('text_color', ColorType::class, [
                    'required' => true,
                ])
                ->add('save', SubmitType::class, [
                    'attr' => [
                        'value' => 'create'
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
            'data_class' => Programme::class,
        ]);
    }

}
