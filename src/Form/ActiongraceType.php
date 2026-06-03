<?php

namespace App\Form;

use App\Entity\Actiongrace;
use App\Entity\Fidele;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ActiongraceType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $fidele = $options['fidele'];

        $builder
                ->add('dixmille')
                ->add('cinqmille')
                ->add('deuxmille')
                ->add('mille')
                ->add('centbillet')
                ->add('centpiece')
                ->add('deuxcent')
                ->add('cent')
                ->add('cinquante')
                ->add('vingtcinq')
                ->add('dix')
                ->add('cinq')
                ->add('dateactiongrace', DateType::class, [
                    // renders it as a single text box
                    'widget' => 'single_text',
                    'required' => true,
                    // this is actually the default format for single_text
                    'format' => 'yyyy-MM-dd',
                ])
                ->add('fidele', EntityType::class, array(
                    'choice_label' => function ($fidele) {
                        return $fidele->getCode()  ;
                    },
                    'class' => Fidele::class,
                    'choices' => $fidele,
                    'multiple' => false,
                    'attr' => array('class' => 'select2'),
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('c')
                                ->orderBy('c.id', 'ASC');
                    },
                ))
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
                ->getForm();

        ;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => Actiongrace::class,
            'fidele' => null,
        ]);
    }

}
