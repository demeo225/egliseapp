<?php

namespace App\Form;

use App\Entity\Culte;
use App\Entity\Fidele;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BilaninviteType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options): void {

        $fideles = $options['fidele'];

        $cultes = $options['culte'];
        $builder
                ->add('dateDebut', DateType::class, [
                    // renders it as a single text box
                    'widget' => 'single_text',
                    'required' => true,
                    // this is actually the default format for single_text
                    'format' => 'yyyy-MM-dd',
                        ]
                )
                ->add('dateFin', DateType::class, [
                    // renders it as a single text box
                    'widget' => 'single_text',
                    'required' => true,
                    // this is actually the default format for single_text
                    'format' => 'yyyy-MM-dd',
                        ]
                )
                ->add('sexe', ChoiceType::class, [
                    'required' => false,
                    'multiple' => false,
                    'expanded' => false,
                    'placeholder' =>'-- Choix de sexe--',
                    'choices' => [
                        'Homme' => 'Homme',
                        'Femme' => 'Femme',
                        'Autre' => 'Autre',
                    ],
                ])
                ->add('fidele', EntityType::class, array(
                    'choice_label' => function ($fideles) {
                        return $fideles->getNomfidele() ;
                    },
                    'class' => Fidele::class,
                    'choices' => $fideles,
                    'multiple' => false,
                    'required' => false,
                    'placeholder' => '--Choix du fidèle --',
                    'attr' => array('class' => 'select2'),
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('c')
                        ->orderBy('c.id', 'ASC');
                    },
                ))
                ->add('culte', EntityType::class, array(
                    'choice_label' => function ($cultes) {
                        return $cultes->getDateculte()->format('d-m-Y');
                    },
                    'class' => Culte::class,
                    'choices' => $cultes,
                    'multiple' => false,
                    'placeholder' => '--Choix du culte  --',
                    'required' => false,
                    'attr' => array('class' => 'select2'),
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('c')
                        ->orderBy('c.id', 'DESC');
                    },
                ))
                ->add('Rechercher', SubmitType::class)

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefaults([
                                               'culte' => null,
                                              'fidele' => null,


        ]);
    }

}
