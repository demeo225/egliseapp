<?php

namespace App\Form;

use App\Entity\Cellule;
use App\Entity\Quartier;
use App\Entity\Zone;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;

class CelluleType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
       $quartier =$options['quartier'];
       $zone = $options['zone'];
        
        $builder
                ->add('nom', TextType::class, [
                    'required' => false,
                ])
                ->add('description', TextareaType::class, [
                    'required' => false,
                ])
                ->add('responsable1', TextType::class, [
                    'required' => false,
                    'constraints' => [
                        new Regex([
                            'pattern' => '/^[0-9a-zA-Z-\s\'ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ]+$/',
                            'match' => true,
                            'message' => 'sont seulement acceptés: les chiffres, les lettres minuscules et majuscules avec ou sans accents, les espaces, les tirets et les apostrophes',
                                ])
                    ],
                        ])
                ->add('responsable2', TextType::class, [
                    'required' => false,
                    'constraints' => [
                        new Regex([
                            'pattern' => '/^[0-9a-zA-Z-\s\'ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ]+$/',
                            'match' => true,
                            'message' => 'sont seulement acceptés: les chiffres, les lettres minuscules et majuscules avec ou sans accents, les espaces, les tirets et les apostrophes',
                                ])
                    ],
                        ])
                ->add('lattitude', TextType::class, [
                    'required' => false,
                ])
                ->add('longitude', TextType::class, [
                    'required' => false,
                ])
                ->add('adresse', TextType::class, [
                    'required' => false,
                ])
                ->add('jour', ChoiceType::class, [
                    'required' => true,
                    'multiple' => false,
                    'expanded' => false,
                    'choices' => [
                        'Lundi' => 'Lundi',
                        'Mardi' => 'Mardi',
                        'Mercredi' => 'Mercredi',
                        'Jeudi' => 'Jeudi',
                        'Vendredi ' => ' Vendredi',
                        'Samedi' => ' Samedi',
                        'Dimanche' => 'Dimanche',
                    ],
                ])
                ->add('heure', TimeType::class, [
                    'required' => false,
                      'placeholder' => [
        'hour' => 'Heure', 'minute' => 'Minute', 'second' => 'Seconde',
    ],
                ])
                ->add('quartier', EntityType::class, [
                    'class' => Quartier::class,
                    'choice_label' => 'libelle',
                    'placeholder' => 'Choix du quartier',
                    'choices'=>$quartier,
                    'required' => false,
                    'expanded' => false,
                    'multiple' => false,
                    'attr' => array('class' => 'select2'),
                ])
                ->add('lieu', TextType::class, [
                    'required' => false,
                ])
                ->add('zone', EntityType::class, [
                    'class' => Zone::class,
                    'choice_label' => 'nom',
                    'placeholder' => 'Choix de la zone',
                    'required' => false,
                    'expanded' => false,
                    'multiple' => false,
                    'choices'=>$zone,
                   'attr' => array('class' => 'select2'),
                ])
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
            'data_class' => Cellule::class,
            'quartier'=>null,
            'zone' =>null,
        ]);
    }

}
