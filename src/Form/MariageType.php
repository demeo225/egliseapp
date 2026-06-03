<?php

namespace App\Form;

use App\Entity\Fidele;
use App\Entity\Mariage;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class MariageType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {

        $epousemembre = $options['epousemembre'];
        $epouxmembre = $options['epouxmembre'];
        $builder
                ->add('datemariage', DateType::class, [
                    'widget' => 'single_text',
                    'required' => true,
                    // this is actually the default format for single_text
                    'format' => 'yyyy-MM-dd',
                ])
                ->add('lieumariage', TextType::class, [
                    'required' => false,
                ])
                ->add('pasteurmariage', TextType::class, [
                    'required' => false,]
                )
                ->add('epousemembre', EntityType::class, array(
                    'choice_label' => function ($fidele) {
                        return $fidele->getCode() . ' ' . $fidele->getNomfidele();
                    },
                    'class' => Fidele::class,
                    'choices' => $epousemembre,
                    'placeholder' => '-- Choix de la conjointe--',
                    'multiple' => false,
                    'required' => false,
                    'attr' => array('class' => 'select2'),
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('c')
                        ->orderBy('c.id', 'ASC');
                    },
                ))
                ->add('epouxmembre', EntityType::class, [
                    'class' => Fidele::class,
                    'choices' => $epouxmembre,
                    'required' => false,
                    'placeholder' => 'Choix epoux',
                    'attr' => array('class' => 'select2'),
                    'required' => false,
                ])
                ->add('epousemembre', EntityType::class, [
                    'class' => Fidele::class,
                    'choice_label' => 'nomfidele',
                    'choices' => $epousemembre,
                    'placeholder' => 'Choix epouse',
                    'attr' => array('class' => 'select2'),
                    'required' => false,
                    'mapped' => true,
                ])
                ->add('temoinepoux', TextType::class, [
                    'required' => false,
                    'constraints' => [
                        new Regex([
                            'pattern' => '/^[0-9a-zA-Z-\s\'ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ]+$/',
                            'match' => true,
                            'message' => 'sont seulement acceptés: les chiffres, les lettres minuscules et majuscules avec ou sans accents, les espaces, les tirets et les apostrophes',
                                ])
                    ],
                ])
                ->add('temoinepouse', TextType::class, [
                    'required' => false,
                    'constraints' => [
                        new Regex([
                            'pattern' => '/^[0-9a-zA-Z-\s\'ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ]+$/',
                            'match' => true,
                            'message' => 'sont seulement acceptés: les chiffres, les lettres minuscules et majuscules avec ou sans accents, les espaces, les tirets et les apostrophes',
                                ])
                    ],
                ])
                ->add('parrain', TextType::class, [
                    'required' => false,
                    'constraints' => [
                        new Regex([
                            'pattern' => '/^[0-9a-zA-Z-\s\'ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ]+$/',
                            'match' => true,
                            'message' => 'sont seulement acceptés: les chiffres, les lettres minuscules et majuscules avec ou sans accents, les espaces, les tirets et les apostrophes',
                                ])
                    ],
                ])
                ->add('regime', ChoiceType::class, [
                    'required' => true,
                    'multiple' => false,
                    'expanded' => false,
                    'choices' => [
                        'Communauté de biens' => 'Communauté de biens',
                        'Séparation de biens' => 'Séparation de biens',
                    ],
                ])
                ->add('typeregime', ChoiceType::class, [
                    'required' => true,
                    'mapped' => false,
                    'multiple' => false,
                    'placeholder' => '-- Choix du type --',
                    'expanded' => false,
                    'choices' => [
                        'Fiancé membre' => '1',
                        'Fiancée membre' => '2',
                        'Les 2 membres' => '3',
                        'Aucun membre' => '4',
                    ],
                ])
                ->add('photo', FileType::class, [
                    'label' => 'Photo mariage',
                    'required' => false,
                    'data_class' => null,
                    'constraints' => [
                        new File([
                            'maxSize' => '1024k',
                            'mimeTypes' => [
                                'image/png',
                                'image/jpeg',
                                'image/gif',
                                'image/ico',
                            ],
                            'mimeTypesMessage' => 'Veuillez choisir une photo',
                                ]),
                    ],
                ])
                ->add('naturedon', TextareaType::class, [
                    'required' => false,
                ])
                ->add('especedon')
                ->add('epoux')
                ->add('epouse')
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

//        $builder
//                ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
//                    $form = $event->getForm();
//                    $data = $event->getData();
//
//                    if (!$data) {
//                        return;
//                    }
//
//                    if ($data->getTyperegime() === 1) {
//                        $form->add('epouxmembre', EntityType::class, [
//                            'class' => Fidele::class,
//                            'choice_label' => 'nom',
//                            'choices' => $epouxmembre,
//                            'placeholder' => 'Choix du fiancé',
//                            'attr' => array('class' => 'select2'),
//                            'required' => true,
//                        ])
//                        ->add('epouse', TextType::class, [
//                            'required' => true,
//                            'mapped' => false,
//                            'constraints' => [
//                                new Regex([
//                                    'pattern' => '/^[0-9a-zA-Z-\s\'ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ]+$/',
//                                    'match' => true,
//                                    'message' => 'sont seulement acceptés: les chiffres, les lettres minuscules et majuscules avec ou sans accents, les espaces, les tirets et les apostrophes',
//                                        ])
//                            ],
//                        ])
//
//                        ;
//                    }
//
//                    if ($data->getTyperegime() === 2) {
//                        $form->add('epousemembre', EntityType::class, [
//                            'class' => Fidele::class,
//                            'choice_label' => 'nom',
//                            'choices' => $epousemembre,
//                            'placeholder' => 'Choix de la faincée',
//                            'attr' => array('class' => 'select2'),
//                            'required' => true,
//                        ])
//                        ->add('epoux', TextType::class, [
//                            'required' => true,
//                            'constraints' => [
//                                new Regex([
//                                    'pattern' => '/^[0-9a-zA-Z-\s\'ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ]+$/',
//                                    'match' => true,
//                                    'message' => 'sont seulement acceptés: les chiffres, les lettres minuscules et majuscules avec ou sans accents, les espaces, les tirets et les apostrophes',
//                                        ])
//                            ],
//                        ])
//
//                        ;
//                    }
//
//                    if ($data->getTyperegime() === 3) {
//                        $form->add('epousemembre', EntityType::class, [
//                            'class' => Fidele::class,
//                            'choice_label' => 'nom',
//                            'choices' => $epousemembre,
//                            'placeholder' => 'Choix de la faincée',
//                            'attr' => array('class' => 'select2'),
//                            'required' => true,
//                        ])
//                        ->add('epouxmembre', EntityType::class, [
//                            'class' => Fidele::class,
//                            'choice_label' => 'nom',
//                            'choices' => $epouxmembre,
//                            'placeholder' => 'Choix du fiancé',
//                            'attr' => array('class' => 'select2'),
//                            'required' => true,
//                        ])
//                        ;
//                    } if ($data->getTyperegime() === 4) {
//                        $form->add('epouse', TextType::class, [
//                            'required' => true,
//                            'constraints' => [
//                                new Regex([
//                                    'pattern' => '/^[0-9a-zA-Z-\s\'ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ]+$/',
//                                    'match' => true,
//                                    'message' => 'sont seulement acceptés: les chiffres, les lettres minuscules et majuscules avec ou sans accents, les espaces, les tirets et les apostrophes',
//                                        ])
//                            ],
//                        ])
//                        ->add('epoux', TextType::class, [
//                            'required' => true,
//                            'constraints' => [
//                                new Regex([
//                                    'pattern' => '/^[0-9a-zA-Z-\s\'ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ]+$/',
//                                    'match' => true,
//                                    'message' => 'sont seulement acceptés: les chiffres, les lettres minuscules et majuscules avec ou sans accents, les espaces, les tirets et les apostrophes',
//                                        ])
//                            ],
//                        ])
//                        ;
//                    }
//                });
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => Mariage::class,
            'epousemembre' => null,
            'epouxmembre' => null,
        ]);
    }

}
