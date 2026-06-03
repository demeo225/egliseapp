<?php

namespace App\Form;

use App\Entity\Inscrire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RestaureinscrireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $builder
            ->add('dateinscrire', DateType::class, [
                    // renders it as a single text box
                    'attr' => ['readonly' => true],
                    'widget' => 'single_text',
                    'required' => false,
                    // this is actually the default format for single_text
                    'format' => 'yyyy-MM-dd',
                        ])
            ->add('createAt', DateType::class, [
                    // renders it as a single text box
                    'attr' => ['readonly' => true],
                    'widget' => 'single_text',
                    'required' => false,
                    // this is actually the default format for single_text
                    'format' => 'yyyy-MM-dd',
                        ])
            ->add('raisondelete')
                                ->add('save', SubmitType::class,)

     

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Inscrire::class,
              'attr' => ['readonly' => true],
        ]);
    }
}
