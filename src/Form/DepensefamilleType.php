<?php

namespace App\Form;

use App\Entity\Famille;
use App\Entity\Depensefamille;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DepensefamilleType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options): void {
      //  $famille = $options['famille'];

        $builder
                ->add('datedepense', DateType::class, [
                    // renders it as a single text box
                    'widget' => 'single_text',
                    'required' => true,
                    // this is actually the default format for single_text
                    'format' => 'yyyy-MM-dd',
                ])
                ->add('objet', ChoiceType::class, [
                    'required' => true,
                    'multiple' => false,
                    'expanded' => false,
                    'choices' => [
                        'Versement au temple' => 'Versement au temple',
                        'Soutien' => 'Soutien',
                        'Autres' => 'Autres',
                    ],
                ])
                // ->add('famille', EntityType::class, [
                //     'class' => Famille::class,
                //     'choices' => $famille,
                 
                //     'attr' => array('class' => 'select2'),
                // ])
                ->add('montant')
                ->add('detail', TextType::class)
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

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefaults([
            'data_class' => Depensefamille::class,
          //  'famille' => null,
        ]);
    }

}
