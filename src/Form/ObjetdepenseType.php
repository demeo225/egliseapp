<?php

namespace App\Form;

use App\Entity\Objetdepense;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ObjetdepenseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('laclasse')
            ->add('intitule')
            ->add('compte')
            ->add('libelle')
            ->add('type', ChoiceType::class, [
                'required' => true,
                'multiple' => false,
                'expanded' => false,
                'placeholder' => '-- Choix option --',
                'choices' => [
                    'Recette' => 'Recette',
                    'Charge' => 'Charge',
                ],
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Objetdepense::class,
        ]);
    }
}
