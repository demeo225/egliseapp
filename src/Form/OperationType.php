<?php

namespace App\Form;

use App\Entity\Operation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\UX\Dropzone\Form\DropzoneType;

class OperationType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {


        $builder
                ->add('objet', ChoiceType::class, [
                    'required' => true,
                    'multiple' => false,
                    'expanded' => false,
                    'choices' => [
                        'Dons' => 'Dons',
                        'Facture CIE' => 'Facture CIE',
                        'Facture SODECI' => 'Facture SODECI',
                        'Frais de comission ' => 'Frais de comission',
                        'Facture INTERNET' => 'Facture INTERNET',
                        'Salaire pasteur' => 'Salaire pasteur',
                        'Loyer pasteur' => 'Loyer pasteur',
                        'Loyer du temple' => 'Loyer du temple',
                        'Dîme des dîmes' => 'Dîme des dîmes',
                        'Soutien aux fidèles' => 'Soutien aux fidèles',
                        'Autres' => 'Autres',
                    ],
                ])
   
                       ->add('photo', DropzoneType::class, [
                    'required' => false,
                    'mapped' => false,
                    
                    'attr' => [
                    ],
                ])
                ->add('beneficiaire', TextType::class, ['required' => true,])
                ->add('montant')
                ->add('dateop', DateType::class, [
                    // renders it as a single text box
                    'widget' => 'single_text',
                    'required' => true,
                    // this is actually the default format for single_text
                    'format' => 'yyyy-MM-dd',
                ])
                ->add('numrecu', TextType::class, ['required' => false,])
                ->add('description', TextType::class, ['required' => false,])
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

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => Operation::class,
      
        ]);
    }

}
