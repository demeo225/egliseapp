<?php

namespace App\Form;

use App\Entity\Inscrire;
use App\Entity\Enfant;
use App\Entity\Classecodim;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SuppinscrireType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $classecodim = $options['classecodim'];
//        $enfant = $options['enfant'];
        $builder
                ->add('raisondelete', TextType::class, [
                    'required' => true,
                ])
                ->add('datefin', DateType::class, [
                    // renders it as a single text box
                    'widget' => 'single_text',
                    'required' => true,
                    // this is actually the default format for single_text
                    'format' => 'yyyy-MM-dd',
                ])
//                ->add('enfant', EntityType::class, [
//                    'class' => Enfant::class,
//                    'choice_label' => 'nom',
//                    'placeholder' => 'Choix enfant',
//                    'required' => true,
//                    'expanded' => false,
//                    'multiple' => false,
//                    'choices' => $enfant,
//                    'attr' => ['readonly' => true],
//                ])
//                ->add('classecodim', EntityType::class, [
//                    'class' => Classecodim::class,
//                    'choice_label' => 'nom',
//                    'required' => true,
//                    'placeholder' => 'Choisir une classe',
//                    'choices' => $classecodim,
//                    'attr' => ['readonly' => true],
//                ])
                ->add('save', SubmitType::class, ['label' => 'Confirmer'])

        ;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => Inscrire::class,
//            'enfant' => null,
            'classecodim' => null,
            'label' => false,
        
        ]);
    }

}
