<?php

namespace App\Form;

use App\Entity\Depensezone;
use App\Entity\Zone;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UpdatedepensezoneType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options): void {
      //  $zone = $options['zone'];
        $builder
                ->add('datedepense', DateType::class, [
                    // renders it as a single text box
                    'widget' => 'single_text',
                    'required' => true,
                    // this is actually the default format for single_text
                    'format' => 'yyyy-MM-dd',
                ])
                ->add('objet')
                ->add('ajout')
                ->add('detail')
                ->add('typeoff', ChoiceType::class, [
                    'required' => true,
                    'multiple' => false,
                    'expanded' => true,
                    'choices' => [
                        'Ajout' => ' 1',
                        'Reduction' => ' 0',
                    ],
                ])
                //      ->add('zone', EntityType::class, [
                //     'class' => Zone::class,
                //     'choices' => $zone,
                //     'required' => true,
                // ])
                ->add('save', SubmitType::class, [
                    'attr' => [
                        'value' => 'create-don'
                    ]
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefaults([
            'data_class' => Depensezone::class,
           //             'zone' => null,

        ]);
    }

}
