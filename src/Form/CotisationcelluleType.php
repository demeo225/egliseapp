<?php

namespace App\Form;

use App\Entity\Cellule;
use App\Entity\Cotisationcellule;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CotisationcelluleType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
       // $cellule = $options['cellule'];
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
                // ->add('cellule', EntityType::class, array(
                //     'choice_label' => function ($cellule) {
                //         return $cellule->getNom();
                //     },
                //     'class' => Cellule::class,
                //     'choices' => $cellule,
                //     'multiple' => false,
                //     'required' => true,
                    
                //     'attr' => array('class' => 'select2'),
                //     'query_builder' => function (EntityRepository $er) {
                //         return $er->createQueryBuilder('c')
                //                 ->orderBy('c.id', 'ASC');
                //     },
                // ))
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

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => Cotisationcellule::class,
           // 'cellule' => null,
        ]);
    }

}
