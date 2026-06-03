<?php

namespace App\Form;

use App\Entity\Zone;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RecherchezoneType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
                $zone = $options['zone'];

        $builder
            ->add('dateDebut', DateType::class, [
                    // renders it as a single text box
                    'widget' => 'single_text',
                    'required' => false,
                    // this is actually the default format for single_text
                    'format' => 'yyyy-MM-dd',
                        ]
                )
                ->add('dateFin', DateType::class, [
                    // renders it as a single text box
                    'widget' => 'single_text',
                    'required' => false,
                    // this is actually the default format for single_text
                    'format' => 'yyyy-MM-dd',
                        ]
                )
                ->add('zone', EntityType::class, [
                    'class' => Zone::class,
                    'choices' => $zone,
                    'required' => false,
                    'placeholder' => '-- Choix de zone --',
                    'attr' => array('class' => 'select2')
                ])
                ->add('rechercher', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
                      'zone' => null,

        ]);
    }
}
