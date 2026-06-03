<?php

namespace App\Form;

use App\Entity\Cellule;
use App\Entity\Cotisationcellule;
use App\Entity\Detailcotisationcellule;
use App\Entity\Fidele;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UpdatedetailcelluleType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $fidele = $options['fidele'];
        $cotisationcellule = $options['cotisationcellule'];
        $cellule = $options['cellule'];

        $builder
                ->add('datedetail', ['widget' => 'single_text', 'format' => 'yyyy-MM-dd', 'required' => true,])
                ->add('montant')
                ->add('montantpayer')
                ->add('fidele', EntityType::class, array(
                    'choice_label' => function ($fidele) {
                        return $fidele->getCode();
                    },
                    'class' => Fidele::class,
                    'choices' => $fidele,
                    'multiple' => false,
                    'attr' => array('class' => 'select2'),
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('c')
                        ->orderBy('c.id', 'ASC');
                    },
                ))
                ->add('cotisationcellule', EntityType::class, array(
                    'choice_label' => function ($cotisationcellule) {
                        return $cotisationcellule->getObjet() . ' Montant ' . $cotisationcellule->getMontant();
                    },
                    'class' => Cotisationcellule::class,
                    'choices' => $cotisationcellule,
                    'multiple' => false,
                    'attr' => array('class' => 'select2'),
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('c')
                        ->orderBy('c.id', 'ASC');
                    },
                ))
                ->add('ajout', ChoiceType::class, [
                    'required' => true,
                    'multiple' => false,
                    'multiple' => false,
                    'mapped' => false,
                    'choices' => [
                        'Ajout' => '1',
                        'Retrait' => '0',
                    ],
                ])
                ->add('cellule', EntityType::class, [
                    'class' => Cellule::class,
                    'choices' => $cellule,
                    'placeholder' => '-- Cellule --',
                    'attr' => array('class' => 'select2'),
                ])
                ->add('save', SubmitType::class, [
                    'attr' => [
                        'value' => 'create-don'
                    ]
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefaults([
            'data_class' => Detailcotisationcellule::class,
            'fidele' => null,
            'cotisationcellule' => null,
            'cellule' => null,
        ]);
    }

}
