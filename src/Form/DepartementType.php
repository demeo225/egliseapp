<?php

namespace App\Form;

use App\Entity\Departement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class DepartementType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('nom', TextType::class, [
                    'required' => true,
                ])
                ->add('description', TextType::class, [
                    'required' => false,
                ])
                ->add('responsable1', TextType::class, [
                    'required' => false,
                ])
                ->add('responsable2', TextType::class, [
                    'required' => false,
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
            'data_class' => Departement::class,
        ]);
    }

}
