<?php

namespace App\Form;

use App\Entity\Departement;
use App\Entity\Depensedepartement;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UpdatedepensedepartementType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options): void {
      //  $departement = $options['departement'];
        $builder
                ->add('datedepense', DateType::class, [
                    // renders it as a single text box
                    'widget' => 'single_text',
                    'required' => true,
                    // this is actually the default format for single_text
                    'format' => 'yyyy-MM-dd',
                ])
                ->add('objet')
                ->add('ajout')
                ->add('detail')
                ->add('typeoff', ChoiceType::class, [
                    'required' => true,
                    'multiple' => false,
                    'expanded' => true,
                    'choices' => [
                        'Ajout' => ' 1',
                        'Reduction' => ' 0',
                    ],
                ])
                //      ->add('departement', EntityType::class, [
                //     'class' => Departement::class,
                //     'choices' => $departement,
                //     'required' => true,
                // ])
                ->add('save', SubmitType::class, [
                    'attr' => [
                        'value' => 'create-don'
                    ]
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefaults([
            'data_class' => Depensedepartement::class,
                //        'departement' => null,

        ]);
    }

}
