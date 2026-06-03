<?php

namespace App\Form;

use App\Entity\Cellule;
use App\Entity\Cotisationparcellule;
use App\Entity\Cotiserparcellule;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CotiserparcelluleType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $cellule = $options['cellule'];
        $cotisationparcellule = $options['cotisationparcellule'];
        $builder
                ->add('montantpayer')
                ->add('datecotiser', DateType::class, [
                    // renders it as a single text box
                    'widget' => 'single_text',
                    'required' => true,
                    // this is actually the default format for single_text
                    'format' => 'yyyy-MM-dd',
                ])
                ->add('cellule', EntityType::class, array(
                    'choice_label' => function ($cellule) {
                        return $cellule->getNom();
                    },
                    'class' => Cellule::class,
                    'choices' => $cellule,
                    'multiple' => false,
                    'attr' => array('class' => 'select2'),
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('c')
                        ->orderBy('c.id', 'ASC');
                    },
                ))
                ->add('cotisationparcellule', EntityType::class, array(
                    'choice_label' => function ($cotisationparcellule) {
                        return $cotisationparcellule->getObjet() . ' Montant ' . $cotisationparcellule->getMontant();
                    },
                    'class' => Cotisationparcellule::class,
                    'choices' => $cotisationparcellule,
                    'multiple' => false,
                    'attr' => array('class' => 'select2'),
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('c')
                        ->orderBy('c.id', 'ASC');
                    },
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

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefaults([
            'data_class' => Cotiserparcellule::class,
            'cellule' => null,
            'cotisationparcellule' => null,
        ]);
    }

}
