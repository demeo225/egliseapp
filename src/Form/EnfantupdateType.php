<?php

namespace App\Form;

use App\Entity\Cellule;
use App\Entity\Commune;
use App\Entity\Enfant;
use App\Entity\Ethnie;
use App\Entity\Famille;
use App\Entity\Fidele;
use App\Entity\Nationalite;
use App\Entity\Quartier;
use App\Entity\Zone;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class EnfantupdateType extends AbstractType{

    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $quartier = $options['quartier'];
        $cellule = $options['cellule'];
        $zone = $options['zone'];
        $ethnie = $options['ethnie'];
        $famille = $options['famille'];
        $nationalite = $options['nationalite'];
        $commune = $options['commune'];
        $merembre = $options['merembre'];
        $peremembre = $options['peremembre'];
        $builder
                ->add('nom', TextType::class, ['required' => true,
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Veuillez écrire un message',
                                ]),
                        new Length([
                            'min' => 6,
                            'minMessage' => 'Message trop court',
                            // max length allowed by Symfony for security reasons
                            'max' => 100,
                                ]),
                    ],
                ])
                ->add('sexe', ChoiceType::class, [
                    'required' => true,
                    'multiple' => false,
                    'expanded' => false,
                    'choices' => [
                        'Garçon' => 'Garçon',
                        'Fille' => 'Fille',
                    ],
                ])
                ->add('contact', TextType::class, ['required' => true, 'constraints' => [
                        new Length([
                            'min' => 10,
                            'minMessage' => 'Taille du numero',
                            // max length allowed by Symfony for security reasons
                            'max' => 14,
                                ]),
                    ],])
                ->add('contactwhatssap', TextType::class, ['required' => false, 'constraints' => [
                        new Length([
                            'min' => 10,
                            'minMessage' => 'Taille numéro whattssap',
                            // max length allowed by Symfony for security reasons
                            'max' => 10,
                                ]),
                    ],])
                ->add('email', EmailType::class, ['required' => false,])
                ->add('datenaiss', DateType::class, [
                    // renders it as a single text box
                    'widget' => 'single_text',
                    'required' => true,
                    // this is actually the default format for single_text
                    'format' => 'yyyy-MM-dd',
                ])
                ->add('lieunaiss', TextType::class, ['required' => false,])
                ->add('pere', TextType::class, ['required' => false,])
                ->add('mere', TextType::class, ['required' => false,])
                ->add('numpiece', TextType::class, ['required' => false,])
                ->add('comptefacebook', TextType::class, ['required' => false,])
                ->add('niveauetude', ChoiceType::class, [
                    'required' => true,
                    'multiple' => false,
                    'expanded' => false,
                    'choices' => [
                        'Aucun' => 'Aucun',
                        'Primaire' => 'Primaire',
                        'Secondaire' => 'Secondaire',
                        'Superieur' => 'Superieur',
                    ],
                ])
                ->add('classenfant', TextType::class, ['required' => false,])
                ->add('groupesang', ChoiceType::class, [
                    'required' => true,
                    'multiple' => false,
                    'expanded' => false,
                    'choices' => [
                        'Inconnu' => 'Inconnu',
                        'A+' => 'A+',
                        'A-' => 'A-',
                        'B+' => 'B+',
                        'B-' => 'B-',
                        'AB+' => 'AB+',
                        'AB-' => 'AB-',
                        'O+' => 'O+',
                        'O-' => 'O-',
                    ],
                ])
                ->add('photo', FileType::class, [
                    'label' => 'Image profil',
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
                ->add('peremembre', EntityType::class, [
                    'class' => Fidele::class,
                    'choices' => $peremembre,
                    'required' => false,
                    'placeholder' => 'Choix du père',
                    'attr' => array('class' => 'select2'),
                    'mapped' => true,
                ])
                ->add('merembre', EntityType::class, [
                    'class' => Fidele::class,
                    'choice_label' => 'nomfidele',
                    'choices' => $merembre,
                    'placeholder' => 'Choix de la mère',
                    'attr' => array('class' => 'select2'),
                    'required' => false,
                    'mapped' => true,
                ])
                ->add('nationalite', EntityType::class, [
                    'class' => Nationalite::class,
                    'choice_label' => 'libelle',
                    'choices' => $nationalite,
                    'placeholder' => 'Choix de la nationalité',
                    'attr' => array('class' => 'select2'),
                    'required' => true,
                ])
                ->add('ethnie', EntityType::class, [
                    'class' => Ethnie::class,
                    'choice_label' => 'libelle',
                    'choices' => $ethnie,
                    'placeholder' => '--Choix ethnie--',
                    'attr' => array('class' => 'select2'),
                    'required' => false,
                ])
                ->add('famille', EntityType::class, [
                    'class' => Famille::class,
                    'choice_label' => 'nom',
                    'choices' => $famille,
                    'placeholder' => '-- Famille --',
                    'attr' => array('class' => 'select2'),
                    'required' => false,
                ])
                ->add('commune', EntityType::class, [
                    'class' => Commune::class,
                    'choice_label' => 'nom',
                    'placeholder' => 'Commune',
                    'choices' => $commune,
                    'required' => true,
                    'mapped' => false,
                ])
//                ->add('cellule', EntityType::class, [
//                    'class' => Cellule::class,
//                    'choices' => $cellule,
//                    'placeholder' => '-- Cellule --',
//                    'attr' => array('class' => 'select2'),
//                    'mapped' => true,
//                    'required' => false,
//                ])
                ->add('zone', EntityType::class, [
                    'class' => Zone::class,
                    'choices' => $zone,
                    'placeholder' => '-- Zone --',
                    'attr' => array('class' => 'select2'),
                    'mapped' => true,
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

        $builder->get('commune')->addEventListener(
                FormEvents::POST_SUBMIT,
                function (FormEvent $event) {
            $form = $event->getForm();
            $this->addQuartierField($form->getParent(), $form->getData());
        }
        );

        $builder->addEventListener(
                FormEvents::POST_SET_DATA,
                function (FormEvent $event) {
            $data = $event->getData();
            /* @var $cellule Cellule */
            $cellule = $data->getCellule();
            $form = $event->getForm();
            if ($cellule) {
                // On récupère le quartier et la commune
                $quartier = $cellule->getQuartier();
                $commune = $quartier->getCommune();
                // On crée les 2 champs supplémentaires
                $this->addQuartierField($form, $commune);
                $this->addCelluleField($form, $quartier);
                // On set les données
                $form->get('commune')->setData($commune);
                $form->get('quartier')->setData($quartier);
            } else {
                // On crée les 2 champs en les laissant vide (champs utilisé pour le JavaScript)
                $this->addQuartierField($form, null);
                $this->addCelluleField($form, null);
            }
        }
        );
    }

    private function addQuartierField(FormInterface $form, ?Commune $commune) {
        $builder = $form->getConfig()->getFormFactory()->createNamedBuilder(
                'quartier',
                EntityType::class,
                null,
                [
                    'class' => Quartier::class,
                    'placeholder' => $commune ? 'Sélectionnez votre quartier' : 'Sélectionnez votre commune',
                    'mapped' => false,
                    'required' => false,
                    'auto_initialize' => false,
                    'choices' => $commune ? $commune->getQuartiers() : []
                ]
        );
        $builder->addEventListener(
                FormEvents::POST_SUBMIT,
                function (FormEvent $event) {
            $form = $event->getForm();
            $this->addCelluleField($form->getParent(), $form->getData());
        }
        );
        $form->add($builder->getForm());
    }

    private function addCelluleField(FormInterface $form, ?Quartier $quartier) {
        $form->add('cellule', EntityType::class, [
            'class' => Cellule::class,
            'placeholder' => $quartier ? 'Sélectionnez votre cellule' : 'Sélectionnez votre quartier',
            'choices' => $quartier ? $quartier->getCellules() : []
        ]);
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => Enfant::class,
            'quartier' => null,
            'zone' => null,
            'cellule' => null,
            'ethnie' => null,
            'famille' => null,
            'nationalite' => null,
            'commune' => null,
            'merembre' => null,
            'peremembre' => null,
//            'enfant' => null,
        ]);
    }

}
