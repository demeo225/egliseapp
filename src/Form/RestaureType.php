<?php

namespace App\Form;

//use Symfony\Component\Form\Extension\Core\Type\EntityType;


use App\Entity\Fidele;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RestaureType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('typefidele', ChoiceType::class, [
                    'attr' => ['readonly' => true],
                    'required' => true,
                    'multiple' => false,
                    'expanded' => false,
                    'choices' => [
                        'Fidèle' => 'Fidèle',
                        'Serviteur' => 'Serviteur',
                    ],
                ])
                ->add('nomfidele', TextType::class, ['attr' => ['readonly' => true],])
                ->add('contact1', TextType::class, ['required' => false,
                    'attr' => ['readonly' => true],
                ])
                ->add('quartier')
                ->add('deletedBy')
                ->add('contactwhatssap', TelType::class, ['required' => false,
                    'attr' => ['readonly' => true],
                ])
                ->add('email', EmailType::class, ['required' => false,
                    'attr' => ['readonly' => true],
                ])
                ->add('nbrenfant', TextType::class, ['attr' => ['readonly' => true],])
                ->add('statutmatri', ChoiceType::class, [
                    'required' => true,
                    'multiple' => false,
                    'expanded' => false,
                    'attr' => ['readonly' => true],
                    'choices' => [
                        'Célibataire' => 'Célibataire',
                        'Marié(e)' => 'Marié(e)',
                        'Veuf(ve)' => 'Veuf(ve)',
                        'Divorcé(e)' => 'Divorcé(e)',
                    ],
                ])
                ->add('datenaiss', DateType::class, [
                    // renders it as a single text box
                    'attr' => ['readonly' => true],
                    'widget' => 'single_text',
                    'required' => false,
                    // this is actually the default format for single_text
                    'format' => 'yyyy-MM-dd',
                        ]
                )
                ->add('deletedAt', DateType::class, [
                    // renders it as a single text box
                    'attr' => ['readonly' => true],
                    'widget' => 'single_text',
                    'required' => false,
                    // this is actually the default format for single_text
                    'format' => 'yyyy-MM-dd',
                        ]
                )
                ->add('numpiece', TextType::class, ['required' => false,
                    'attr' => ['readonly' => true],
                ])
                ->add('typepiece', ChoiceType::class, [
                    'required' => true,
                    'multiple' => false,
                    'expanded' => false,
                    'attr' => ['readonly' => true],
                    'choices' => [
                        'Aucun' => 'Aucun',
                        'CNI' => 'CNI',
                        'Attestation' => 'Attestation',
                        'Passport' => 'Passport',
                        'Permis' => 'Permis',
                        'Carte professionnelle' => 'Carte professionnelle',
                        'Autre' => 'Autre',
                    ],
                ])
                ->add('comptefacebook', TextType::class, ['required' => false,
                    'attr' => ['readonly' => true],
                ])
                ->add('domaineactivite', ChoiceType::class, [
                    'attr' => ['readonly' => true],
                    'required' => true,
                    'multiple' => false,
                    'expanded' => false,
                    'choices' => [
                        'Sante' => 'Sante',
                        'Justice' => 'Justice',
                        'Enseignement' => 'Enseignement',
                        'Média & TIC' => 'Média & TIC',
                        'Securité' => 'Sécurité',
                        'Sport' => 'Sport',
                        'Agricutrue & elevage' => 'Agricutrue & elevage',
                        'Entrepreneuriat' => 'Entrepreneuriat',
                        'Machiniste & conducteur' => 'Machiniste & conducteur',
                        'Autres' => 'Autres',
                        'Aucun' => 'Aucun',
                    ],
                ])
                ->add('profession', TextType::class, ['required' => false,
                    'attr' => ['readonly' => true],
                ])
                ->add('groupesang', ChoiceType::class, [
                    'attr' => ['readonly' => true],
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
                    'attr' => ['readonly' => true],
                    'widget' => 'single_text',
                    'required' => false,
                    // this is actually the default format for single_text
                    'format' => 'yyyy-MM-dd',
                ])
                ->add('stutbapteme', ChoiceType::class, [
                    'attr' => ['readonly' => true],
                    'required' => false,
                    'multiple' => false,
                    'expanded' => false,
                    'choices' => [
                        'Oui' => '1',
                        'Non' => '2',
                    ],
                ])
                ->add('datebapteme', DateType::class, [
                    'attr' => ['readonly' => true],
                    'widget' => 'single_text',
                    'required' => false,
                    // this is actually the default format for single_text
                    'format' => 'yyyy-MM-dd',
                ])
                ->add('lieubapteme', TextType::class, ['required' => false, 'attr' => ['readonly' => true],])
                ->add('sexe', TextType::class, ['required' => false, 'attr' => ['readonly' => true],])
                ->add('cellule')
                ->add('famille')
                ->add('ethnie')
                ->add('nationalite')
                ->add('pasteurbapteme', TextType::class, ['required' => false, 'attr' => ['readonly' => true],])
                ->add('anciennecommunaute', TextType::class, ['required' => false, 'attr' => ['readonly' => true],])
                ->add('zone')
                ->add('code', TextType::class, ['required' => false,
                    'attr' => ['readonly' => true],])

//                ->add('bapteme')
                ->add('save', SubmitType::class, ['label' => 'Restaurer'])

        ;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => Fidele::class,
            'label' => false,
            'attr' => ['readonly' => true],
        ]);
    }

}
