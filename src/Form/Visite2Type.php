<?php

namespace App\Form;

use App\Entity\Fidele;
use App\Entity\Visite2;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Visite2Type extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $fidele = $options['fidele'];

        $builder
                ->add('lieu')
                ->add('personne', TextType::class, [
                    'required' => false,
                ])
                ->add('datevisite', DateType::class, ['widget' => 'single_text', 'format' => 'yyyy-MM-dd'])
                      ->add('heure', TimeType::class, [
                    'required' => false,
                    'placeholder' => [
                        'hour' => 'Heure', 'minute' => 'Minute', 'second' => 'Seconde',
                    ],
                ])
                ->add('verset')
                ->add('observation', TextareaType::class, [
                    'required' => false,
                ])
                ->add('responsable')
                ->add('fidele', EntityType::class, array(
                    'choice_label' => function ($fidele) {
                        return $fidele->getNomfidele();
                    },
                    'class' => Fidele::class,
                    'choices' => $fidele,
                    'multiple' => false,
                    'required' => false,
                    'placeholder' => '-- Choix fidèle --',
                    'attr' => array('class' => 'select2'),
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('c')
                        ->orderBy('c.id', 'ASC');
                    },
                ))
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
            'data_class' => Visite2::class,
            'fidele' => null,
        ]);
    }

}
