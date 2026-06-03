<?php

namespace App\Form;

use App\Entity\Cartemembre;
use App\Entity\Timbre;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TimbreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $cartemembre = $options['cartemembre'];
        $builder
            ->add('numero')
            ->add('annee')
            ->add('dateachat',DateType::class, [
                // renders it as a single text box
                'widget' => 'single_text',
                'required' => true,
                // this is actually the default format for single_text
                'format' => 'yyyy-MM-dd',
            ])
            ->add('cartemembre', EntityType::class, array(
                'choice_label' => function ($cartemembre) {
                    return $cartemembre->getNumero().' -- '. $cartemembre->getFidele()->getNomfidele();
                },
                'class' => Cartemembre::class,
                'choices' => $cartemembre,
                'multiple' => false,
                'placeholder' => '--Choix du carte membre  --',
                'required' => true,
                'attr' => array('class' => 'select2'),
          
            ))
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
            'data_class' => Timbre::class,
            'cartemembre' => null,
        ]);
    }
}
