<?php

namespace App\Form;

use App\Entity\Departement;
use App\Entity\Groupe;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Groupe2Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $departement = $options['departement'];
        $builder
            ->add('nom', TextType::class, [
                    'required' => true,
                ])
            ->add('description', TextType::class, [
                    'required' => false,
                ])
            ->add('responsable1', TextType::class ,[
                'required' => false,
            ])
                ->add('departement', EntityType::class,  [
                    'class' => Departement::class,
                    'choice_label' => 'nom',
                    'choices'=>$departement,
                    'attr' => array('class' => 'select2'),
                    'mapped' => true,
                    'required' => true,
                ])
            ->add('responsable2', TextType::class ,[
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

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Groupe::class,
            'departement'=>null,
        ]);
    }
}
