<?php

namespace App\Form;

use App\Entity\Groupe;
use App\Entity\Groupefidele;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SuppgroupefideleType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $groupe = $options['groupe'];
        $builder
                ->add('rolegroupe')
                ->add('createAt', DateType::class, [
                    // renders it as a single text box
                    'widget' => 'single_text',
                    'label' => 'Date ajout',
                    'required' => false,
                    // this is actually the default format for single_text
                    'format' => 'yyyy-MM-dd',
                ])
                ->add('fidele')
                ->add('groupe', EntityType::class, [
                    'class' => Groupe::class,
                    'choice_label' => 'nom',
                    'placeholder' => 'Choix du groupe',
                    'choices' => $groupe,
                   
                ])
                
                 ->add('save', SubmitType::class, ['label' => 'Retirer'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => Groupefidele::class,
            'label' => false,
            'groupe' => null,
            'attr' => ['readonly' => true],
        ]);
    }

}
