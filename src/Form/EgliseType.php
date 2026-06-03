<?php

namespace App\Form;

use App\Entity\Communaute;
use App\Entity\Eglise;
use App\Entity\Region;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\UX\Dropzone\Form\DropzoneType;

class EgliseType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
        ->add('communaute', EntityType::class, [
                    'class' => Communaute::class,
                    'choice_label' => 'libelle',
                    'placeholder' => 'Choix de la Communauté',
                    'required' => true,
                    'mapped' => true,
                    'attr' => array('class' => 'select2'),
                ])
                          ->add('region', EntityType::class, [
                    'class' => Region::class,
                    'choice_label' => 'libelle',
                    'placeholder' => 'Choix de la region',
                    'attr' => array('class' => 'select2'),
                    'required' => true,
                ])
             ->add('annee', IntegerType::class, [
                    'required' => true,
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Année d\'implatation requise',
                                ]),
                        new Length([
                            'min' => 4,
                            'minMessage' => 'L\'année doit contenir 4 chiffres',
                            // max length allowed by Symfony for security reasons
                            'max' => 4,
                                ]),
                    ],
                ])
        ->add('photo', DropzoneType::class, [
                    'required' => false,
                    'mapped' => false,
                    
                    'attr' => [
                    ],
                ])
                ->add('denomination', TextType::class, ['required' => true,])
//                ->add('arrete', TextType::class, ['required' => false,])
                ->add('contact1', TextType::class, ['required' => true,])
                ->add('contact2', TextType::class, ['required' => false,])
                ->add('adresse', TextType::class, ['required' => false,])
//                ->add('logo')
                ->add('quartier', TextType::class, ['required' => true,])
                ->add('verset', TextType::class, ['required' => false,])
                ->add('texte', TextType::class, ['required' => false,])
                ->add('sigle', TextType::class, ['required' => false,])
                ->add('facebook', TextType::class, ['required' => false,])
                ->add('agrement', TextType::class, ['required' => false,])
                ->add('regionpastorale', TextType::class, ['required' => false,])
                ->add('commune', TextType::class, ['required' => true,])
                ->add('regionpastorale', TextType::class, ['required' => false,])

        ;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => Eglise::class,
        ]);
    }

}
