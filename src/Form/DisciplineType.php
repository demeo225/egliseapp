<?php

namespace App\Form;

use App\Entity\Discipline;
use App\Entity\Fidele;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DisciplineType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $fidele = $options['fidele'];
        $builder
                ->add('datedebut', DateType::class, [
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
                        'Incitation à la rebellion' => 'Incitation à la rebellion',
                        'Incitation à la violence' => 'Incitation à la violence',
                        'Refus de repentence' => 'Refus de repentence',
                        'Refus de pardon' => 'Refus de pardon',
                        'Péché sexuel' => 'Péché sexuel',
                        'Vol, abus de confiance ou detournement' => 'Vol, abus de confiance ou detournement',
                        'Scandale dans le foyer' => 'Scandale dans le foyer',
                        'Ivrognerie' => 'Ivrognerie',
                        'Défiance à l\'autorité' => 'Défiance à l\'autorité',
                        'Mensonge avéré' => 'Mensonge avéré',
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
            'data_class' => Discipline::class,
            'fidele' => null,
        ]);
    }

}
