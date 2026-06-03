<?php

namespace App\Form;

use App\Entity\Departement;
use App\Entity\Cotisationpardepartement;
use App\Entity\Cotiserpardepartement;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CotiserpardepartementType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $departement = $options['departement'];
        $cotisationpardepartement = $options['cotisationpardepartement'];
        $builder
                ->add('montantpayer')
                ->add('datecotiser', DateType::class, [
                    // renders it as a single text box
                    'widget' => 'single_text',
                    'required' => true,
                    // this is actually the default format for single_text
                    'format' => 'yyyy-MM-dd',
                ])
                ->add('departement', EntityType::class, array(
                    'choice_label' => function ($departement) {
                        return $departement->getNom();
                    },
                    'class' => Departement::class,
                    'choices' => $departement,
                    'multiple' => false,
                    'attr' => array('class' => 'select2'),
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('c')
                        ->orderBy('c.id', 'ASC');
                    },
                ))
                ->add('cotisationpardepartement', EntityType::class, array(
                    'choice_label' => function ($cotisationpardepartement) {
                        return $cotisationpardepartement->getObjet() . ' Montant ' . $cotisationpardepartement->getMontant();
                    },
                    'class' => Cotisationpardepartement::class,
                    'choices' => $cotisationpardepartement,
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

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefaults([
            'data_class' => Cotiserpardepartement::class,
            'departement' => null,
            'cotisationpardepartement' => null,
        ]);
    }

}
