<?php

namespace App\Form;

use App\Entity\Ecodimactivite;
use App\Entity\Enfant;
use App\Entity\Enfantactivite;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EnfantactiviteType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $ecodimactivite = $options['ecodimactivite'];
        $enfant = $options['enfant'];
        $builder
                ->add('montantpayer')
                ->add('enfant', EntityType::class, array(
                    'choice_label' => function ($enfant) {
                        return $enfant->getNom();
                    },
                    'class' => Enfant::class,
                    'choices' => $enfant,
                    'multiple' => false,
                    'attr' => array('class' => 'select2'),
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('c')
                        ->orderBy('c.id', 'ASC');
                    },
                ))
                ->add('datecotiser', DateType::class, [
                    // renders it as a single text box
                    'widget' => 'single_text',
                    'required' => true,
                    // this is actually the default format for single_text
                    'format' => 'yyyy-MM-dd',
                ])
                ->add('ecodimactivite', EntityType::class, array(
                    'choice_label' => function ($ecodimactivite) {
                        return $ecodimactivite->getNomactivite() . ' Montant ' . $ecodimactivite->getParticipation();
                    },
                    'class' => Ecodimactivite::class,
                    'choices' => $ecodimactivite,
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
            'data_class' => Enfantactivite::class,
            'enfant' => null,
            'ecodimactivite' => null,
        ]);
    }

}
