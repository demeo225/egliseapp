<?php

/*
 * Formulaire de recherche multiple de fidèles.
 * Test de recherche
 * and open the template in the editor.
 */

namespace App\Form;

use App\Entity\Bapteme;
use App\Entity\Cellule;
use App\Entity\Commune;
use App\Entity\Ethnie;
use App\Entity\Famille;
use App\Entity\Fonction;
use App\Entity\Quartier;
use App\Entity\Zone;
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
class SearchfideleType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $quartier = $options['quartier'];
        $cellule = $options['cellule'];
        $zone = $options['zone'];
        $ethnie = $options['ethnie'];
        $famille = $options['famille'];
        $fonction = $options['fonction'];
        $commune = $options['commune'];

        $builder
                ->add('sexe', ChoiceType::class, [
                    'required' => false,
                    'choices' => [
                        'Homme' => 'Homme',
                        'Femme' => 'Femme',
                        'Autre' => 'Autre',
                    ],
                ])
                ->add('zone', EntityType::class, [
                    'class' => Zone::class,
                    'choice_label' => 'nom',
                    'choices' => $zone,
                    'placeholder' => 'Choix de la zone',
                    'required' => false,
                    'mapped' => true,
                    'attr' => array('class' => 'select2'),
                ])
                ->add('nationalite', CountryType::class, array('label' => 'Pays de naissance*',
                    'preferred_choices' => array('CI'),
                     'placeholder' => 'Choix du pays',
                     'required' => false,
                    'choice_translation_locale' => null
                ))
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
                    'placeholder' => '-- Commune --',
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
                    'required' => false,
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
                ->add('permis', ChoiceType::class, [
                    'required' => false,
                    'multiple' => false,
                    'expanded' => false,
                    'choices' => [
                        'Aucun' => 'Aucun',
                        'A' => 'A',
                        'B' => 'B',
                        'Toute categorie' => 'Toute categorie',
                    ],
                ])
                ->add('emploi', ChoiceType::class, [
                    'required' => false,
                    'multiple' => false,
                    'expanded' => false,
                    'choices' => [
                        'Sans emploi' => 'Sans emploi',
                        'Au chomage' => 'Au chomage',
                        'Fonctionnaire' => 'Fonctionnaire',
                        'Epmloyé du privé' => 'Epmloyé du privé',
                        'Employeur' => 'Employeur',
                        'Reteraité d\'Etat' => 'Rteraité d\'Etat',
                        'Retraité du privé' => 'retraité du privé',
                    ],
                ])
                ->add('domaineactivite', ChoiceType::class, [
                    'required' => false,
                    'multiple' => false,
                    'expanded' => false,
                    'placeholder' => '-- Aucun --',
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
                        'BTP' => 'BTP',
                        'Etudiant' => 'Etudiant',
                        'Autres' => 'Autres',
                    ],
                ])
                ->add('statutmatri', ChoiceType::class, [
                    'required' => false,
                    'multiple' => false,
                    'expanded' => false,
                    'placeholder' => '-- Choix du statut --',
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
                    'attr' => array('class' => 'select2'),
                ])
                ->add('bapteme', EntityType::class, [
                    'class' => Bapteme::class,
                    'choice_label' => 'promotion',
                    'placeholder' => '-- Promotion baptème --',
                    'attr' => array('class' => 'select2'),
                    'required' => false,
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
                ->add('typefidele', ChoiceType::class, [
                    'required' => false,
                    'multiple' => false,
                    'expanded' => false,
                    'choices' => [
                        'Fidèle' => 'Oui',
                        'Serviteur' => 'Non',
                    ],
                ])
                ->add('stutbapteme', ChoiceType::class, [
                    'required' => false,
                    'multiple' => false,
                    'expanded' => false,
                    'choices' => [
                        'Oui' => 'Oui',
                        'Non' => 'Non',
                    ],
                ])
                ->add('vieseul', ChoiceType::class, [
                    'required' => false,
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
                    'required' => false,
                    'multiple' => false,
                    'expanded' => false,
                    'choices' => [
                        'Premier culte' => true,
                        'Deuxième culte' => '2',
                        'Troisième culte' => '3',
                        'Quartrième culte' => '4',
                        'Cinquième culte' => '5',
                        'Sixième culte' => '6',
                    ],
                ])
                ->add('cultefamille', ChoiceType::class, [
                    'required' => false,
                    'multiple' => false,
                    'expanded' => false,
                    'choices' => [
                        'Non' => false,
                        'Oui' => true,
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
                ->add('priere', ChoiceType::class, [
                    'required' => false,
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
                    'required' => false,
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
                    'required' => false,
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
                    'required' => false,
                    'multiple' => false,
                    'expanded' => false,
                    'choices' => [
                        'Non' => 'Non',
                        'Oui mais sans diplôme' => 'Ouisansdiplome',
                        'Oui avec  certificat' => 'Certificat',
                    ],
                ])
                ->add('etude', ChoiceType::class, [
                    'required' => false,
                    'multiple' => false,
                    'expanded' => false,
                    'choices' => [
                        'Aucun' => 'Aucun',
                        'CEPE' => 'CEPE',
                        'BEPC' => 'BEPC',
                        'BAC' => 'BAC',
                        'BTS' => 'BTS',
                        'DUG 1' => 'DUG 1',
                        'DUG 2' => 'DUG 2',
                        'LICENCE' => 'LICENCE',
                        'MASTEUR ' => 'MASTEUR',
                        'DOCTORAT' => 'DOCTORAT',
                        'MAITRISE' => 'MAITRISE',
                        'DESS' => 'DESS',
                        'CAP' => 'CAP',
                        'BEP' => 'BEP',
                        'BT' => 'BT',
                        'FPA' => 'FPA',
                        'BP' => 'BP',
                        'INGENIEUR' => 'INGENIEUR',
                        'AUTRE' => 'AUTRE',
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
                    'required' => false,
                    'multiple' => false,
                    'expanded' => false,
                    'choices' => [
                        'Normal' => false,
                        'Handicapé(e)' => true,
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
                        'Autres' => 'Autres',
                    ],
                ])
                ->add('etatvieparent', ChoiceType::class, [
                    'required' => false,
                    'multiple' => false,
                    'expanded' => false,
                    'choices' => [
                        'Normale' => 'Oui',
                        'Anormale' => 'Non',
                    ],
                ])
                ->add('dateDebut', DateType::class, ['widget' => 'single_text', 'required' => false, 'format' => 'yyyy-MM-dd'])
                ->add('dateFin', DateType::class, ['widget' => 'single_text', 'required' => false, 'format' => 'yyyy-MM-dd'])
                ->add('Rechercher', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
//            'data_class' => 'App\Entity\Fidele',
            'quartier' => null,
            'zone' => null,
            'cellule' => null,
            'ethnie' => null,
            'famille' => null,
            'fonction' => null,
            'commune' => null,
        ]);
    }

}
