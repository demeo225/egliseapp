<?php

namespace App\Form;

use App\Entity\Communaute;
use App\Entity\Eglise;
use App\Entity\Region;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class EglisevalidationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('denomination')
                  ->add('photo', FileType::class, [
                    'label' => 'Logo',
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
     
            ->add('contact1')
            ->add('contact2')
            ->add('adresse')
            ->add('quartier')
            ->add('texte')
            ->add('agrement')
            ->add('sigle')
            ->add('facebook')
            ->add('verset')
          //  ->add('arrete')
            ->add('commune')
            ->add('code', TextType::class, [
                'attr' => ['readonly' => true],
            ])
            ->add('congregation')
            ->add('administrateur')
                ->add('communaute', EntityType::class, [
                    'class' => Communaute::class,
                    'choice_label' => 'libelle',
                    'placeholder' => 'Choix de la communaute',
                    'attr' => array('class' => 'select2'),
                    'required' => true,
                ])
                
                       ->add('region', EntityType::class, [
                    'class' => Region::class,
                    'choice_label' => 'libelle',
                    'placeholder' => 'Choix de la communaute',
                    'attr' => array('class' => 'select2'),
                    'required' => true,
                ])
                ->add('regionpastorale', TextType::class, ['required' => false,])
 
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Eglise::class,
        ]);
    }
}
