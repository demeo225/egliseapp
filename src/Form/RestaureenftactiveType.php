<?php

namespace App\Form;

use App\Entity\Enfant;
use App\Entity\Enfantactivite;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RestaureenftactiveType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('createAt', DateType::class, [
                    // renders it as a single text box
                    'widget' => 'single_text',
                    'required' => true,
                    // this is actually the default format for single_text
                    'format' => 'yyyy-MM-dd',
                    'attr' => ['readonly' => true],
                ])
                ->add('updateAt', DateType::class, [
                    // renders it as a single text box
                    'widget' => 'single_text',
                    'required' => true,
                    // this is actually the default format for single_text
                    'format' => 'yyyy-MM-dd',
                    'attr' => ['readonly' => true],
                ])
                ->add('montantpayer')
                ->add('reste')
                ->add('enfant', EntityType::class, [
                    'class' => Enfant::class,
                ])
                ->add('ecodimactivite')
                ->add('save', SubmitType::class, ['label' => 'Restaurer'])

        ;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => Enfantactivite::class,
        ]);
    }

}
