<?php

namespace App\Form;

use App\Entity\Cellule;
use App\Entity\Commune;
use App\Entity\Ethnie;
use App\Entity\Famille;
use App\Entity\Fidele;
use App\Entity\Fonction;
use App\Entity\Nationalite;
use App\Entity\Quartier;
use App\Entity\Zone;
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

class ValidationType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options): void {

        $quartier = $options['quartier'];
        $cellule = $options['cellule'];
        $zone = $options['zone'];
        $ethnie = $options['ethnie'];
        $famille = $options['famille'];
        $nationalite = $options['nationalite'];
        $fonction = $options['fonction'];
        $commune = $options['commune'];
        $builder
             ->add('typefidele', ChoiceType::class, [
                    'required' => true,
                    'multiple' => false,
                    'expanded' => false,
                    'choices' => [
                        'Fidèle' => 'Oui',
                        'Serviteur' => 'Non',
                    ],
                ])
                ->add('nomfidele', TextType::class, [
                    'required' => true,
                ])
                ->add('contact1', TextType::class, [
                    'required' => true,
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Numéro requis',
                                ]),
                        new Length([
                            'min' => 10,
                            'minMessage' => 'Verifiez la taille du téléphone',
                            // max length allowed by Symfony for security reasons
                            'max' => 10,
                                ]),
                    ],
                ])
                ->add('contactwhatssap', TextType::class, ['required' => false,
                    'constraints' => [
                        new Length([
                            'min' => 10,
                            'minMessage' => 'Verifiez la taille du téléphone',
                            // max length allowed by Symfony for security reasons
                            'max' => 10,
                                ]),
                    ],])
                ->add('email', EmailType::class, ['required' => false,])
                ->add('nbrenfant')
                ->add('sexe', ChoiceType::class, [
                    'required' => true,
                    'multiple' => false,
                    'expanded' => false,
                    'choices' => [
                        'Homme' => 'Homme',
                        'Femme' => 'Femme',
                    ],
                ])
               ->add('statutmatri', ChoiceType::class, [
                    'required' => true,
                    'multiple' => false,
                    'expanded' => false,
                    'choices' => [
                        'Célibataire' => 'Célibataire',
                        'Concubinage avec dot' => 'Concubinage avec dot',
                        'Concubinage sans dot' => 'Concubinage sans dot',
                        'Concubinage avec côcô' => 'Concubinage avec côcô',
                        'Concubinage sans côcô' => 'Concubinage sans côcô',
                        'Fiancé(e) avec dot' => 'Fiancé(e) avec dot',
                        'Fiancé(e) avec dot' => 'Fiancé(e) avec dot',
                        'Marié(e)' => 'Marié(e)',
                        'Veuf(ve)' => 'Veuf(ve)',
                        'Divorcé(e)' => 'Divorcé(e)',
                    ],
                ])
           ->add('vieseul', ChoiceType::class, [
                    'required' => true,
                    'multiple' => false,
                    'expanded' => false,
                    'choices' => [
                        'Vis chez moi' => 'Vis chez moi',
                        'Vis en famille' => 'Vis en famille',
                        'Vis en communauté' => 'Vis en communauté',
                        'Vis chez tuteur' => 'Vis chez tuteur',
                        'Autre' => 'Autre',
                    ],
                ])
                ->add('langue', ChoiceType::class, [
                    'required' => false,
                    'multiple' => false,
                    'expanded' => false,
                    'placeholder' => 'Aucune',
                    'choices' => [
                        'Anglais' => 'Anglais',
                        'Espagnol' => 'Espagnol',
                        'Allemand' => 'Allemand',
                        'Italien' => 'Italien',
                        'Arabe' => 'Arabe',
                        'Autre' => 'Autre',
                    ],
                ])
                ->add('choiculte', ChoiceType::class, [
                    'required' => true,
                    'multiple' => false,
                    'expanded' => false,
                    'choices' => [
                        'Premier culte' => '1',
                        'Deuxièeme culte' => '2',
                        'Troisième culte' => '3',
                        'Quartrième culte' => '4',
                        'Cinquième culte' => '5',
                        'Sixième culte' => '6',
                    ],
                ])
                ->add('cultefamille', ChoiceType::class, [
                    'required' => true,
                    'multiple' => false,
                    'expanded' => false,
                    'choices' => [
                        'Non' => '0',
                        'Oui' => '1',
                    ],
                ])
                ->add('priere', ChoiceType::class, [
                    'required' => true,
                    'multiple' => false,
                    'expanded' => false,
                    'choices' => [
                        'Quotidien' => 'Quotidien',
                        'Souvent' => 'Souvent',
                        'De temps à autre' => 'De temps à autre',
                        'Aucune prière' => 'Aucune prière',
                    ],
                ])
                ->add('lecture', ChoiceType::class, [
                    'required' => true,
                    'multiple' => false,
                    'expanded' => false,
                    'choices' => [
                        'Quotidien' => 'Quotidien',
                        'Souvent' => 'Souvent',
                        'De temps à autre' => 'Temps',
                        'Je sais pas lire' => 'Non',
                    ],
                ])
                ->add('temoignage', ChoiceType::class, [
                    'required' => true,
                    'multiple' => false,
                    'expanded' => false,
                    'choices' => [
                        'Quotidien' => 'Quotidien',
                        'Souvent' => 'Souvent',
                        'De temps à autre' => 'Temps',
                        'Difficile pour moi' => 'Non',
                    ],
                ])
                ->add('bibleformation', ChoiceType::class, [
                    'required' => true,
                    'multiple' => false,
                    'expanded' => false,
                    'choices' => [
                        'Non' => 'Non',
                        'Oui mais sans diplôme' => 'Ouisansdiplome',
                        'Oui avec  certificat' => 'Certificat',
                    ],
                ])
                ->add('etatparent', ChoiceType::class, [
                    'required' => false,
                    'multiple' => false,
                    'expanded' => false,
                    'placeholder' => '-- Votre choix --',
                    'choices' => [
                        'Mère décedée' => 'Mère décedée',
                        'Père décedé' => 'Père décedé',
                        'Parents décedés' => 'Parents décedés',
                        'Mère inconnue & père décedé' => 'Mère inconnue & père décedé',
                        'Père inconnu & mère décedée' => 'Père inconnu & mère décedée',
                        'Mère inconnue' => 'Mère inconnue',
                        'Père inconnu' => 'Père inconnu',
                        'Parents inconnus' => 'Parents inconnus',
                    ],
                ])
                
                
                ->add('situation', ChoiceType::class, [
                    'required' => true,
                    'multiple' => false,
                    'expanded' => false,
                    'choices' => [
                        'Normal' => '0',
                        'Handicapé(e)' => '1',
                    ],
                ])
                ->add('handicap', ChoiceType::class, [
                    'required' => false,
                    'multiple' => false,
                    'expanded' => false,
                    'placeholder' => '-- Handicape --',
                    'choices' => [
                        'Handicap moteur' => 'Handicap moteur',
                        'Mal voyant' => 'Mal voyant',
                        'Sourd' => 'Sourd',
                        'Sourd-muet(te)' => 'Sourd-muet(te)',
                        'Stigmatisé(e)' => 'Stigmatisé(e)',
                        'Autres' => 'Autre',
                    ],
                ])
                ->add('etatvieparent', ChoiceType::class, [
                    'required' => true,
                    'multiple' => false,
                    'expanded' => false,
                    'choices' => [
                        'Normal' => '0',
                        'Anormal' => '1',
                    ],
                ])
                ->add('datenaiss', DateType::class, [
                    // renders it as a single text box
                    'widget' => 'single_text',
                    'required' => true,
                    // this is actually the default format for single_text
                    'format' => 'yyyy-MM-dd',
                        ]
                )
                ->add('datearriver', DateType::class, [
                    // renders it as a single text box
                    'widget' => 'single_text',
                    'required' => true,
                    // this is actually the default format for single_text
                    'format' => 'yyyy-MM-dd',
                        ]
                )
                ->add('numpiece', TextType::class, ['required' => false,])
                ->add('typepiece', ChoiceType::class, [
                    'required' => true,
                    'multiple' => false,
                    'expanded' => false,
                    'choices' => [
                        'Aucun' => 'Aucun',
                        'CNI' => 'CNI',
                        'Attestation' => 'Attestation',
                        'Pastport' => 'Pastport',
                        'Permis' => 'Permis',
                        'Carte professionnelle' => 'Carte professionnelle',
                        'Autre' => 'Autre',
                    ],
                ])
                ->add('comptefacebook', TextType::class, ['required' => false,])
                ->add('domaineactivite', ChoiceType::class, [
                    'required' => true,
                    'multiple' => false,
                    'expanded' => false,
                    'choices' => [
                           'Sante' => 'Sante',
                        'Justice' => 'Justice',
                        'Média & TIC' => 'Média & TIC',
                        'Securité' => 'Sécurité',
                        'Sport' => 'Sport',
                        'Assurance' => 'Assurance',
                        'Employé au privé' => 'Employé au privé',
                        'Mécanique' => 'Mécanique',
                        'Administration' => 'Administration',
                        'Transport & Transit' => 'Transport & Transit',
                        'Agricutrue & elevage' => 'Agricutrue & elevage',
                        'Entrepreneuriat' => 'Entrepreneuriat',
                        'Sport' => 'Sport',
                        'Serviteurs & Responsables des Eglises' => 'Serviteurs & Responsables des Eglises',
                        'Commerce' => 'Commerce',
                        'Construction ' => 'Construction',
                        'Mecanique' => 'Mecanique',
                        'Export/Import' => 'Export/Import',
                        'Finance' => 'Finance',
                        'Machiniste & conducteur' => 'Machiniste & conducteur',
                        'Enseignement' => 'Enseignement',
                        'Elève' => 'Elève',
                        'Etudiant' => 'Etudiant',
                        'Autres' => 'Autres',
                    ],
                ])
                ->add('profession', TextType::class, ['required' => false,])
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
                ->add('dateconversion', DateType::class, [
                    'widget' => 'single_text',
                    'required' => true,
                    // this is actually the default format for single_text
                    'format' => 'yyyy-MM-dd',
                ])
                ->add('stutbapteme', ChoiceType::class, [
                    'required' => true,
                    'multiple' => false,
                    'expanded' => true,
                    'choices' => [
                        'Oui' => '1',
                        'Non' => '0',
                    ],
                ])
                ->add('datebapteme', DateType::class, [
                    'widget' => 'single_text',
                    'required' => false,
                    // this is actually the default format for single_text
                    'format' => 'yyyy-MM-dd',
                ])
                ->add('lieubapteme', TextType::class, ['required' => false,])
                ->add('pasteurbapteme', TextType::class, ['required' => false,])
                ->add('anciennecommunaute', TextType::class, ['required' => false,])
                ->add('lieumariage', TextType::class, ['required' => false,])
                ->add('pasteurmariage', TextType::class, ['required' => false,])
                ->add('nommariage', TextType::class, ['required' => false,])
                ->add('datemariage', DateType::class, [
                    // renders it as a single text box
                    'widget' => 'single_text',
                    'required' => false,
                    // this is actually the default format for single_text
                    'format' => 'yyyy-MM-dd',
                        ]
                )
                ->add('zone', EntityType::class, [
                    'class' => Zone::class,
                    'choice_label' => 'nom',
                    'choices' => $zone,
                    'placeholder' => 'Choix de la zone',
                    'required' => false,
                    'mapped' => true,
                    'attr' => array('class' => 'select2'),
                ])
                ->add('nationalite', EntityType::class, [
                    'class' => Nationalite::class,
                    'choice_label' => 'libelle',
                    'choices' => $nationalite,
                    'placeholder' => 'Choix de la nationalité',
                    'attr' => array('class' => 'select2'),
                    'required' => true,
                    'mapped' => true,
                ])
                ->add('fonction', EntityType::class, [
                    'class' => Fonction::class,
                    'choice_label' => 'libelle',
                    'choices' => $fonction,
                    'placeholder' => '--Fonction--',
                    'attr' => array('class' => 'select2'),
                    'required' => false,
                    'mapped' => true,
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
                    'mapped' => true,
                ])
                ->add('commune', EntityType::class, [
                    'class' => Commune::class,
                    'choice_label' => 'nom',
                    'placeholder' => 'Commune',
                    'choices' => $commune,
                    'required' => false,
                    'mapped' => true,
                    'attr' => array('class' => 'select2'),
                ])
                ->add('quartier', EntityType::class, [
                    'class' => Quartier::class,
                    'choice_label' => 'libelle',
                    'choices' => $quartier,
                    'placeholder' => 'Quartier',
                    'attr' => array('class' => 'select2'),
                    'required' => true,
                    'mapped' => true,
                ])
                ->add('cellule', EntityType::class, [
                    'class' => Cellule::class,
                    'choices' => $cellule,
                    'required' => false,
                    'mapped' => true,
                    'placeholder' => '-- Cellule --',
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
        
//        
//        $formModifier = function (FormInterface $form, Zone $zone = null) {
//            $cellules = null === $zone ? [] : $zone->getCellules(['deletedAt' => NULL]);
//
//            $form->add('cellule', EntityType::class, [
//                'class' => Cellule::class,
//                'placeholder' => 'Choisir une cellule',
//                'choices' => $cellules,
//            ]);
//        };
//
//        $builder->addEventListener(
//                FormEvents::PRE_SET_DATA,
//                function (FormEvent $event) use ($formModifier) {
//            // this would be your entity, i.e. ZoneMeetup
//            $data = $event->getData();
//
//            $formModifier($event->getForm(), $data->getZone());
//        }
//        );
//
//        $builder->get('zone')->addEventListener(
//                FormEvents::POST_SUBMIT,
//                function (FormEvent $event) use ($formModifier) {
//            // It's important here to fetch $event->getForm()->getData(), as
//            // $event->getData() will get you the client data (that is, the ID)
//            $zone = $event->getForm()->getData();
//
//            // since we've added the listener to the child, we'll have to pass on
//            // the parent to the callback functions!
//            $formModifier($event->getForm()->getParent(), $zone);
//        }
//        );
//
//        $formModifier1 = function (FormInterface $form, Commune $commune = null) {
//            $quartiers = null === $commune ? [] : $commune->getQuartiers(['deletedAt' => NULL]);
//
//            $form->add('quartier', EntityType::class, [
//                'class' => Quartier::class,
//                'placeholder' => 'Choisir un quartier',
//                'choices' => $quartiers,
//            ]);
//        };
//
//        $builder->addEventListener(
//                FormEvents::PRE_SET_DATA,
//                function (FormEvent $event) use ($formModifier1) {
//            // this would be your entity, i.e. CommuneMeetup
//            $data = $event->getData();
//
//            $formModifier1($event->getForm(), $data->getCommune());
//        }
//        );
//
//        $builder->get('commune')->addEventListener(
//                FormEvents::POST_SUBMIT,
//                function (FormEvent $event) use ($formModifier1) {
//            // It's important here to fetch $event->getForm()->getData(), as
//            // $event->getData() will get you the client data (that is, the ID)
//            $commune = $event->getForm()->getData();
//
//            // since we've added the listener to the child, we'll have to pass on
//            // the parent to the callback functions!
//            $formModifier1($event->getForm()->getParent(), $commune);
//        }
//        );
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => Fidele::class,
            'quartier' => null,
            'zone' => null,
            'cellule' => null,
            'ethnie' => null,
            'famille' => null,
            'nationalite' => null,
            'fonction' => null,
            'commune' => null,
        ]);
    }

}
