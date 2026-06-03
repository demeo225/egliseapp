<?php

namespace App\Form;

use App\Entity\Famille;
use App\Entity\Zone;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FamilleType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $zone = $options['zone'];
        $builder
                ->add('zone', EntityType::class, [
                    'class' => Zone::class,
                    'choices' => $zone,
                    'required' => false,
                    'placeholder' => '-- Zone --',
                    'attr' => array('class' => 'select2'),
                ])
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
             

        ;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => Famille::class,
            'zone' => null,
        ]);
    }

}
