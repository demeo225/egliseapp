<?php

namespace App\Form;

use App\Entity\Couple;
use App\Entity\Fidele;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CoupleType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $epouse = $options['epouse'];
        $epoux = $options['epoux'];
        $builder
       
                ->add('epoux', EntityType::class, array(
                    'choice_label' => function ($fidele) {
                        return $fidele->getNomfidele();
                    },
                    'class' => Fidele::class,
                    'choices' => $epoux,
                    'multiple' => false,
                    'attr' => array('class' => 'select2'),
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('c')
                                ->orderBy('c.id', 'ASC');
                    },
                ))
                ->add('epouse', EntityType::class, array(
                    'choice_label' => function ($fidele) {
                        return $fidele->getNomfidele();
                    },
                    'class' => Fidele::class,
                    'choices' => $epouse,
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => Couple::class,
            'epouse' => null,
            'epoux' => null,
        ]);
    }

}
