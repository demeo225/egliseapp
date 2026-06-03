<?php

namespace App\Form;

use App\Entity\Evangelisation;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;

class EvangelisationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateop', DateType::class, [
                    // renders it as a single text box
                    'widget' => 'single_text',
                    'required' => false,
                    // this is actually the default format for single_text
                    'format' => 'yyyy-MM-dd',
                ])
            ->add('lieu', TextType::class, [
                    'required' => true,
               
                        ])

            ->add('responsable1', TextType::class, [
                    'required' => false,
               
                        ])
            ->add('observation', CKEditorType::class, [
                    'required' => false,
                ])
            ->add('responsable2', TextType::class, [
                    'required' => false,
              
                        ])
            ->add('personnes1')
            ->add('personnes2')
            ->add('personnes3')
            ->add('personnes4')
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

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Evangelisation::class,
        ]);
    }
}
