<?php

namespace App\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Dropzone\Form\DropzoneType;

class ExcelImportType extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
       

        $builder
            ->add('file', DropzoneType::class, [
                'required' => true,
                
                'attr' => [
                    '',
                ],
            ])
      
       
            ->add('save', SubmitType::class);
    }
    

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => null,
       
        ]);
    }
}