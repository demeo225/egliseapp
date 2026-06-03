<?php

namespace App\Form;

use App\Entity\Courrier;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Dropzone\Form\DropzoneType;

class CourrierType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('expediteur', TextType::class,[
                'required'=> true,
             ])
            ->add('destinataire')
            ->add('dateexpedition', DateType::class, [
                // renders it as a single text box
                'widget' => 'single_text',
                'required' => true,
                // this is actually the default format for single_text
                'format' => 'yyyy-MM-dd',
            ])
            ->add('daterecep', DateType::class, [
                // renders it as a single text box
                'widget' => 'single_text',
                'required' => false,
                // this is actually the default format for single_text
                'format' => 'yyyy-MM-dd',
            ])
            ->add('objet')
            ->add('typecourrier', ChoiceType::class, [
                'required' => true,
                'multiple' => false,
                'expanded' => false,
                'placeholder'=> '-- Choix -- ',
                'choices' => [
                    'Expedition' => 'Expedition',
                    'Reception' => 'Reception',
                ],
            ])
            ->add('photo', DropzoneType::class, [
                'required' => false,
                'mapped' => false,
                
                'attr' => [
                    
                ],
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

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Courrier::class,
        ]);
    }
}
