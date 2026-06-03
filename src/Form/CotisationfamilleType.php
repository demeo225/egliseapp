<?php

namespace App\Form;

use App\Entity\Cotisationfamille;
use App\Entity\Famille;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CotisationfamilleType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
     //   $famille = $options['famille'];

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
                // ->add('famille', EntityType::class, [
                //     'class' => Famille::class,
                //     'choices' => $famille,
                    
                //     'attr' => array('class' => 'select2'),
                // ])
                ->add('save', SubmitType::class, [
                    'attr' => [
                        'value' => 'create-cotisation'
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
            'data_class' => Cotisationfamille::class,
         //   'famille' => null,
        ]);
    }

}
