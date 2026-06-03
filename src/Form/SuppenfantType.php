<?php

namespace App\Form;

use App\Entity\Enfant;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SuppenfantType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('nom')
                ->add('sexe')
                ->add('contact')
                ->add('contactwhatssap')
                ->add('email')
                ->add('datenaiss', DateType::class, [
                    // renders it as a single text box
                    'attr' => ['readonly' => true],
                    'widget' => 'single_text',
                    'required' => false,
                    // this is actually the default format for single_text
                    'format' => 'yyyy-MM-dd',
                        ])
                ->add('lieunaiss')
                ->add('numpiece')
                ->add('comptefacebook')
                ->add('niveauetude')
                ->add('classenfant')
                ->add('groupesang')
                ->add('code')
                ->add('nationalite')
                ->add('ethnie')
                ->add('commune')
                ->add('quartier')
                ->add('cellule')
                ->add('famille')
                ->add('save', SubmitType::class, ['label' => 'Supprimer'])

        ;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => Enfant::class,
            'label' => false,
            'attr' => ['readonly' => true],
        ]);
    }

}
