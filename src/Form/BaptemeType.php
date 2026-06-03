<?php

namespace App\Form;

use App\Entity\Bapteme;
use App\Entity\Fidele;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BaptemeType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $fidele = $options['fidele'];
        $builder
                ->add('pasteurofficient', TextType::class, [
                    'required' => true,
                ])
                ->add('promotion', TextType::class, [
                    'required' => true,
                ])
                     ->add('parrain', TextType::class, [
                    'required' => false,
                ])
                ->add('datebapteme', DateType::class, [
                    // renders it as a single text box
                    'widget' => 'single_text',
                    'required' => false,
                    // this is actually the default format for single_text
                    'format' => 'yyyy-MM-dd',
                ])
                ->add('lieubapteme', TextType::class, [
                    'required' => false,
                ])

                ->add('fidele', EntityType::class, array(
                    'choice_label' => function ($fidele) {
                        return $fidele->getCode() . ' ' . $fidele->getNomfidele();
                    },
                    'class' => Fidele::class,
                    'choices' => $fidele,
                    'multiple' => true,
                    'required' => true,
                    'attr' => array('class' => 'select2'),
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('c')
                                ->orderBy('c.id', 'ASC');
                    },
                ))
                ->add('save', SubmitType::class, [
                    'attr' => [
                        'value' => 'create-bapteme'
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
            'data_class' => Bapteme::class,
            'fidele' => null,
        ]);
    }

}
