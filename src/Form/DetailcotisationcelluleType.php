<?php

namespace App\Form;

use App\Entity\Cotisercellule;
use App\Entity\Detailcotisationcellule;
use App\Entity\Fidele;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DetailcotisationcelluleType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $fidele = $options['fidele'];
        $cotisercellule = $options['cotisercellule'];
        $builder
                ->add('datedetail', DateType::class, [
                    // renders it as a single text box
                    'widget' => 'single_text',
                    'required' => true,
                    // this is actually the default format for single_text
                    'format' => 'yyyy-MM-dd',
                ])
                ->add('ajout', IntegerType::class, [
                    'required' => true,
                    'mapped' => false,
                ])
                ->add('montantpayer', IntegerType::class, [
                    'attr' => ['readonly' => true],
                ])
                ->add('reste', IntegerType::class, [
                    'attr' => ['readonly' => true],
                ])
                ->add('typeoff', ChoiceType::class, [
                    'required' => true,
                    'multiple' => false,
                    'mapped' => false,
                    'expanded' => true,
                    'choices' => [
                        'Ajout' => ' 1',
                        'Reduction' => ' 0',
                    ],
                ])
                ->add('fidele', EntityType::class, array(
                    'choice_label' => function ($fidele) {
                        return $fidele->getNomfidele() . ' ' . $fidele->getContact1();
                    },
                    'class' => Fidele::class,
                    'choices' => $fidele,
                    'required' => false,
                    'multiple' => false,
                    'attr' => ['readonly' => true],
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('c')
                                ->orderBy('c.id', 'ASC');
                    },
                ))
                ->add('cotisercellule', EntityType::class, [
                    'class' => Cotisercellule::class,
                    'attr' => ['readonly' => true],
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
            'cotisercellule' => null,
        ]);
    }

}
