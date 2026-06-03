<?php

namespace App\Form;

use App\Entity\Fidele;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BilandisciplineType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $fidele = $options['fidele'];
        $builder
                ->add('dateDebut1', DateType::class, [
                    // renders it as a single text box
                    'widget' => 'single_text',
                    'required' => true,
                    // this is actually the default format for single_text
                    'format' => 'yyyy-MM-dd',
                        ]
                )
                ->add('dateFin', DateType::class, [
                    // renders it as a single text box
                    'widget' => 'single_text',
                    'required' => true,
                    // this is actually the default format for single_text
                    'format' => 'yyyy-MM-dd',
                        ]
                )
                ->add('fidele', EntityType::class, array(
                    'choice_label' => function ($fidele) {
                        return $fidele->getCode() . ' ' . $fidele->getNomfidele();
                    },
                    'class' => Fidele::class,
                    'choices' => $fidele,
                    'required' => false,
                    'placeholder' => '--Choix du fidèle--',
                    'attr' => array('class' => 'select2'),
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('c')
                        ->orderBy('c.id', 'ASC');
                    },
                ))
                ->add('Rechercher', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefaults([
            'fidele' => null,
        ]);
    }

}
