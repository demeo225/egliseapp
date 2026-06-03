<?php

namespace App\Form;

use App\Entity\Commune;
use App\Entity\Quartier;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuartierType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
       $commune = $options['commune'];
        $builder
                ->add('libelle', TextType::class, [
                    'required' => true,
                ])
                ->add('commune', EntityType::class, [
                    'class' => Commune::class,
                    'choice_label' => 'nom',
                    'choices' => $commune,
                    'placeholder' => 'Choix de la commune',
                    'attr' => array('class' => 'select2'),
                    'required' => true,
                ])
                // ->add('save', SubmitType::class, [
                //     'attr' => [
                //         'value' => 'create'
                //     ]
                // ])
                // ->add('saveAndAdd', SubmitType::class, [
                //     'attr' => [
                //         'value' => 'save-and-add'
                //     ]
                // ])

        ; 
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => Quartier::class,
            'commune' => null,
        ]);
    }

}
