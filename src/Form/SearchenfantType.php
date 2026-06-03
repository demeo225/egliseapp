<?php

/*
 * Formulaire de recherche multiple de fidèles.
 * Test de recherche
 * and open the template in the editor.
 */

namespace App\Form;

use App\Entity\Cellule;
use App\Entity\Ethnie;
use App\Entity\Famille;
use App\Entity\Fidele;
use App\Entity\Quartier;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Formulaire de recherche multiples sur le fidèle
 *
 * @author Lynova Tech
 */
class SearchenfantType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $quartier = $options['quartier'];
        $cellule = $options['cellule'];
        $ethnie = $options['ethnie'];
        $famille = $options['famille'];
        $merembre = $options['merembre'];
        $peremembre = $options['peremembre'];

        $builder
                ->add('sexe', ChoiceType::class, [
                    'required' => false,
                    'multiple' => false,
                    'expanded' => false,
                    'choices' => [
                        'Garçon' => 'Garçon',
                        'Fille' => 'Fille',
                    ],
                ])
                ->add('niveauetude', ChoiceType::class, [
                    'required' => false,
                    'multiple' => false,
                    'expanded' => false,
                    'choices' => [
                        'Aucun' => 'Aucun',
                        'Primaire' => 'Primaire',
                        'Secondaire' => 'Secondaire',
                        'Superieur' => 'Superieur',
                    ],
                ])
                ->add('lieuvivre', ChoiceType::class, [
                    'required' => false,
                    'multiple' => false,
                    'expanded' => false,
                    'choices' => [
                        'En famille' => 'Famille',
                        'Chez un parent' => 'Chez parent',
                        'Chez tuteur' => 'Tutueur',
                        'Autre' => 'Autre',
                    ],
                ])
                ->add('groupesang', ChoiceType::class, [
                    'required' => false,
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
                ->add('lieuvivre', ChoiceType::class, [
                    'required' => false,
                    'multiple' => false,
                    'expanded' => false,
                    'choices' => [
                        'En famille' => 'Vit en famille',
                        'Chez un parent' => 'Chez parent',
                        'Chez tuteur' => 'Chez tuteur',
                        'Autre' => 'Autre',
                    ],
                ])
                ->add('maladie', ChoiceType::class, [
                    'required' => false,
                    'multiple' => false,
                    'expanded' => false,
                    'placeholder' => '-- Votre choix --',
                    'choices' => [
                        'Maladies dermatologiques' => 'Maladies dermatologiques',
                        'Maladies cardiovasculaires' => 'Maladies cardiovasculaires',
                        'Maladies respiratoires' => 'Maladies respiratoires',
                        'Maladies cancereuses' => 'Maladies cancereuses',
                        'Maladies et troubles oculaires' => 'Maladies et troubles oculaires',
                        'Maladies génétiques' => 'Maladies génétiques',
                        'Maladies infectieuses' => 'Maladies infectieuses',
                        'Maladies mentales' => 'Maladies mentales',
                        'Choléra' => 'Choléra',
                        'Coqueluche' => 'Coqueluche',
                        'COVID-19' => 'COVID-19',
                        'Diarrhée à Escherichia coli entérotoxinogène' => 'Diarrhée à Escherichia coli entérotoxinogène',
                        'Diphtérie' => 'Diphtérie',
                        'Encéphalite japonaise' => 'Encéphalite japonaise',
                        'Fièvre jaune' => 'Fièvre jaune',
                        'Gastroentérite à rotavirus' => 'Gastroentérite à rotavirus',
                        'Grippe' => 'Grippe',
                        'Hépatite A' => 'Hépatite A',
                        'Hépatite B' => 'Hépatite B',
                        'Infections par les virus' => 'Infections par les virus',
                        'Oreillons' => 'Oreillons',
                        'Poliomyélite' => 'Poliomyélite',
                        'Rage' => 'Rage',
                        'Rougeole' => 'Rougeole',
                        'Rubéole' => 'Rubéole',
                        'Tétanos' => 'Tétanos',
                        'Tuberculose' => 'Tuberculose',
                        'Typhoïde' => 'Typhoïde',
                        'Varicelle' => 'Varicelle',
                        'Zona ' => 'Zona ',
                        'Autre ' => 'Autre ',
                    ],
                ])
                ->add('vieparent', ChoiceType::class, [
                    'required' => false,
                    'placeholder' => '-- Preciser --',
                    'multiple' => false,
                    'expanded' => false,
                    'choices' => [
                        'Mère décedée' => 'Mère décedée',
                        'Père décedé' => 'Père décedé',
                        'Parents décedés' => 'Parents décedés',
                        'Mère inconnue' => 'Mère inconnue',
                        'Père inconnu' => 'Père inconnu',
                        'Père inconnu & mère décedée' => 'Père inconnu & mère décedée',
                        'Mère inconnue & père décedé' => 'Mère inconnue & père décedé',
                        'Parents inconnus' => 'Parents inconnus',
                    ],
                ])
                ->add('situation', ChoiceType::class, [
                    'required' => false,
                    'multiple' => false,
                    'expanded' => false,
                    'choices' => [
                        'Non' => 'Non',
                        'Oui' => 'Oui',
                    ],
                ])
                ->add('dateDebut', DateType::class, [
                    // renders it as a single text box
                    'widget' => 'single_text',
                    'required' => false,
                    // this is actually the default format for single_text
                    'format' => 'yyyy-MM-dd',
                        ]
                )
                ->add('dateFin', DateType::class, [
                    // renders it as a single text box
                    'widget' => 'single_text',
                    'required' => false,
                    // this is actually the default format for single_text
                    'format' => 'yyyy-MM-dd',
                        ]
                )
                ->add('situationparent', ChoiceType::class, [
                    'required' => false,
                    'multiple' => false,
                    'expanded' => false,
                    'choices' => [
                        'Oui' => 'Oui',
                        'Non' => 'Non',
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
                ->add('peremembre', EntityType::class, [
                    'class' => Fidele::class,
                    'choices' => $peremembre,
                    'required' => false,
                    'placeholder' => 'Choix du père',
                    'attr' => array('class' => 'select2'),
                ])
                ->add('merembre', EntityType::class, [
                    'class' => Fidele::class,
                    'choice_label' => 'nomfidele',
                    'choices' => $merembre,
                    'placeholder' => 'Choix de la mère',
                    'attr' => array('class' => 'select2'),
                    'required' => false,
                ])
                ->add('nationalite', CountryType::class, array('label' => 'Pays de naissance*',
                    'preferred_choices' => array('CI'),
                    'placeholder' => 'Choix du pays',
                    'required' => false,
                    'choice_translation_locale' => null
                ))
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
                ->add('quartier', EntityType::class, [
                    'class' => Quartier::class,
                    'choice_label' => 'libelle',
                    'choices' => $quartier,
                    'placeholder' => 'Quartier',
                    'attr' => array('class' => 'select2'),
                    'required' => false,
                ])
                ->add('cellule', EntityType::class, [
                    'class' => Cellule::class,
                    'choices' => $cellule,
                    'required' => false,
                    'placeholder' => '-- Cellule --',
                    'attr' => array('class' => 'select2')
                ])
                ->add('Rechercher', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
//            'data_class' => 'App\Entity\Fidele',
            'quartier' => null,
            'cellule' => null,
            'ethnie' => null,
            'famille' => null,
            'fonction' => null,
            'merembre' => null,
            'peremembre' => null,
        ]);
    }

}
