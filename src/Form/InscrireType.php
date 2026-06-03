<?php

namespace App\Form;

use App\Entity\Classecodim;
use App\Entity\Enfant;
use App\Entity\Inscrire;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InscrireType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {

        $classecodim = $options['classecodim'];
        $enfant = $options['enfant'];
        $builder
                ->add('dateinscrire', DateType::class, [
                    // renders it as a single text box
                    'widget' => 'single_text',
                    'required' => true,
                    // this is actually the default format for single_text
                    'format' => 'yyyy-MM-dd',
                ])
                ->add('enfant', EntityType::class, array(
                    'choice_label' => function ($enfant) {
                        return $enfant->getCode() . ' ' . $enfant->getNom();
                    },
                    'class' => Enfant::class,
                    'choices' => $enfant,
                    'multiple' => false,
                    'placeholder' => 'Choisir un enfant',
                    'attr' => array('class' => 'select2'),
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('c')
                        ->orderBy('c.id', 'ASC');
                    },
                ))
                ->add('classecodim', EntityType::class, [
                    'class' => Classecodim::class,
                    'choice_label' => 'nom',
                    'required' => true,
                    'placeholder' => 'Choisir une classe',
                    'choices' => $classecodim,
                    'attr' => array('class' => 'select2'),
                ])
                ->add('save', SubmitType::class, [
                    'attr' => [
                        'value' => 'create'
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
            'data_class' => Inscrire::class,
            'enfant' => null,
            'classecodim' => null,
        ]);
    }

}
