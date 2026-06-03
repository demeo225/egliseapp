<?php

namespace App\Form;

use App\Entity\Fiancaille;
use App\Entity\Fidele;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FiancailleType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $fianceemembre = $options['fianceemembre'];
        $fiancemembre = $options['fiancemembre'];
        $builder
                ->add('datefiancaille', DateType::class, [
                    'widget' => 'single_text',
                    'required' => true,
                    // this is actually the default format for single_text
                    'format' => 'yyyy-MM-dd',
                ])
                ->add('fiancee', TextType::class, [
                    'required' => false,
                ])
                ->add('fiancemembre', EntityType::class, array(
                    'choice_label' => function ($fidele) {
                        return $fidele->getCode() . ' ' . $fidele->getNomfidele();
                    },
                    'class' => Fidele::class,
                    'choices' => $fiancemembre,
                    'multiple' => false,
                    'attr' => array('class' => 'select2'),
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('c')
                                ->orderBy('c.id', 'ASC');
                    },
                ))
                ->add('fianceemembre', EntityType::class, array(
                    'choice_label' => function ($fidele) {
                        return $fidele->getCode() . ' ' . $fidele->getNomfidele();
                    },
                    'class' => Fidele::class,
                    'choices' => $fianceemembre,
                    'multiple' => false,
                    'attr' => array('class' => 'select2'),
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('c')
                                ->orderBy('c.id', 'ASC');
                    },
                ))

//                ->add('fiancemembre', EntityType::class, [
//                    'class' => Fidele::class,
//                    'choices' => $fiancemembre,
//                    'required' => false,
//                    'placeholder' => 'Choix fiancé',
//                    'attr' => array('class' => 'select2'),
//                    'mapped' => true,
//                ])
//                ->add('fianceemembre', EntityType::class, [
//                    'class' => Fidele::class,
//                    'choice_label' => 'nomfidele',
//                    'choices' => $fianceemembre,
//                    'placeholder' => 'Choix fiancee',
//                    'attr' => array('class' => 'select2'),
//                    'required' => false,
//                    'mapped' => true,
//                ])
                ->add('fiance', TextType::class, [
                    'required' => false,
                ])
                ->add('pasteurfiancaille', TextType::class, [
                    'required' => false,
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

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => Fiancaille::class,
            'fianceemembre' => null,
            'fiancemembre' => null,
        ]);
    }

}
