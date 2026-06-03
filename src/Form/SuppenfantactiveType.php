<?php

namespace App\Form;

use App\Entity\Ecodimactivite;
use App\Entity\Enfant;
use App\Entity\Enfantactivite;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SuppenfantactiveType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
          
            ->add('montantpayer')
            ->add('reste')
            ->add('enfant', EntityType::class,
                    [
                        'class' => Enfant::class,
                    ])
            ->add('ecodimactivite')
                ->add('save', SubmitType::class, ['label' => 'Supprimer'])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Enfantactivite::class,
              'label' => false,
            'attr' => ['readonly' => true],
        ]);
    }
}
