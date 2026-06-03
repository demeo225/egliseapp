<?php

namespace App\Form;

use App\Entity\Cotisationzone;
use App\Entity\Zone;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CotisationzoneType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options): void {
      //  $zone = $options['zone'];
        $builder
                ->add('datefin', DateType::class, [
                    // renders it as a single text box
                    'widget' => 'single_text',
                    'required' => true,
                    // this is actually the default format for single_text
                    'format' => 'yyyy-MM-dd',
                ])
                ->add('objet')
                ->add('montant', IntegerType::class,
                        [
                            'required' => true
                        ]
                )
                // ->add('zone', EntityType::class, [
                //     'class' => Zone::class,
                //     'choices' => $zone,
                //     'required' => true,
                    
                //     'attr' => array('class' => 'select2'),
                // ])
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
            'data_class' => Cotisationzone::class,
          //  'zone' => null,
        ]);
    }

}
