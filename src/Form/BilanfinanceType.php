<?php

namespace App\Form;

use App\Entity\Departement;
use App\Entity\Objetcharge;
use App\Entity\Objetrecette;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BilanfinanceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $departement = $options['departement'];
        $objetcharge = $options['objetcharge'];
        $objetrecette = $options['objetrecette'];
        $builder
        ->add('dateDebut', DateType::class, [
            // renders it as a single text box
            'widget' => 'single_text',
            'required' => true,
            // this is actually the default format for single_text
            'format' => 'yyyy-MM-dd',
                ]
        )
        ->add('dateFin', DateType::class, [
            // renders it as a single text box
            'widget' => 'single_text',
            'required' => true,
            // this is actually the default format for single_text
            'format' => 'yyyy-MM-dd',
                ]
        )
        ->add('objetrecette', EntityType::class, array(
            'choice_label' => function ($objetrecette) {
                return $objetrecette->getLibelle();
            },
            'class' => Objetrecette::class,
            'choices' => $objetrecette,
            'required' => false,
            'placeholder' => '--Choix objet recette --',
            'attr' => array('class' => 'select2'),
          
        ))
        ->add('objetcharge', EntityType::class, array(
            'choice_label' => function ($objetcharge) {
                return   $objetcharge->getLibelle();
            },
            'class' => Objetcharge::class,
            'choices' => $objetcharge,
            'required' => false,
            'placeholder' => '--Choix du objet depense--',
            'attr' => array('class' => 'select2'),
        
        ))
        ->add('departement', EntityType::class, array(
            'choice_label' => function ($departement) {
            return   $departement->getNom();
            },
            'class' => Departement::class,
            'choices' => $departement,
            'required' => false,
            'placeholder' => '--Choix du département--',
            'attr' => array('class' => 'select2'),
        
        ))
     
        ->add('Rechercher', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'departement' => null,
            'objetrecette' => null,
            'objetcharge' => null,

           
        ]);
    }
}
