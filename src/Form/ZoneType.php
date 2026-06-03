<?php

namespace App\Form;

use App\Entity\Zone;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ZoneType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
             ->add('nom', TextType::class, [
                 'attr' =>[
                     'placeholder' => 'Entrer le nom de la zone'
                 ]
             ])
            ->add('description', TextareaType::class, [
                    'required' => false,
                   'attr' =>[
                     'placeholder' => 'Details de la zone'
                 ]
                ])
            ->add('responsable1', TextType::class ,[
                'required' => false,
                   'attr' =>[
                     'placeholder' => 'Nom du responsable'
                 ]
            ])
            ->add('responsable2', TextType::class ,[
                'required' => false,
                   'attr' =>[
                     'placeholder' => 'Nom du 2nd responsable'
                 ]
            ])
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
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Zone::class,
        ]);
    }
}
