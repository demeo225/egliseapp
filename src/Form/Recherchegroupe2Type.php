<?php

namespace App\Form;

use App\Entity\Groupe;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Recherchegroupe2Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
         $groupe = $options['groupe'];
        $builder
            ->add('groupe', EntityType::class, [
                'class' => Groupe::class,
                'choices' =>$groupe,
                'required' => false,
                'placeholder' => '-- Choix du groupe --',
                'attr' => ['class' => 'form-control select2']
            ])
            ->add('dateDebut', DateType::class, [
                'widget' => 'single_text',
                'required' => false,
                'attr' => ['class' => 'form-control']
            ])
            ->add('dateFin', DateType::class, [
                'widget' => 'single_text',
                'required' => false,
                'attr' => ['class' => 'form-control']
            ])
            ->add('rechercher', SubmitType::class, [
                'label' => 'Rechercher',
                'attr' => ['class' => 'btn btn-primary']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
                'groupe' => null,
        ]);
    }
}