<?php

namespace App\Form;

use App\Entity\Conge;
use App\Entity\Fidele;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CongeType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $fidele = $options['fidele'];
        $builder
                ->add('dateconge', DateType::class, [
                    // renders it as a single text box
                    'widget' => 'single_text',
                    'required' => true,
                    // this is actually the default format for single_text
                    'format' => 'yyyy-MM-dd',
                ])
                ->add('datefin', DateType::class, [
                    // renders it as a single text box
                    'widget' => 'single_text',
                    'required' => false,
                    // this is actually the default format for single_text
                    'format' => 'yyyy-MM-dd',
                ])
                ->add('detail', CKEditorType::class, [
                    'required' => false,
                ])
                ->add('cause', ChoiceType::class, [
                    'required' => true,
                    'multiple' => false,
                    'expanded' => false,
                    'attr' => array('class' => 'select2'),
                    'choices' => [
                        'Congé annuel' => 'Congé annuel',
                        'Repos maladie' => 'Repos maladie',
                        'Congé de maternité' => 'Congé de maternité',
                        'Voyage' => 'Voyage',
                        'Autre' => 'Autre',
                    ],
                ])
                ->add('fidele', EntityType::class, [
                    'class' => Fidele::class,
                    'choice_label' => 'nomfidele',
                    'choices' => $fidele,
                    'placeholder' => 'Choix  fidele',
                    'attr' => array('class' => 'select2'),
                    'required' => true,
                ])
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

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefaults([
            'data_class' => Conge::class,
            'fidele' => null,
        ]);
    }

}
