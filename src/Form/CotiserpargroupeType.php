<?php

namespace App\Form;

use App\Entity\Groupe;
use App\Entity\Cotisationpargroupe;
use App\Entity\Cotiserpargroupe;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CotiserpargroupeType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $groupe = $options['groupe'];
        $cotisationpargroupe = $options['cotisationpargroupe'];
        $builder
                ->add('montantpayer')
                ->add('datecotiser', DateType::class, [
                    // renders it as a single text box
                    'widget' => 'single_text',
                    'required' => true,
                    // this is actually the default format for single_text
                    'format' => 'yyyy-MM-dd',
                ])
                ->add('groupe', EntityType::class, array(
                    'choice_label' => function ($groupe) {
                        return $groupe->getNom();
                    },
                    'class' => Groupe::class,
                    'choices' => $groupe,
                    'multiple' => false,
                    'attr' => array('class' => 'select2'),
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('c')
                        ->orderBy('c.id', 'ASC');
                    },
                ))
                ->add('cotisationpargroupe', EntityType::class, array(
                    'choice_label' => function ($cotisationpargroupe) {
                        return $cotisationpargroupe->getObjet() . ' Montant ' . $cotisationpargroupe->getMontant();
                    },
                    'class' => Cotisationpargroupe::class,
                    'choices' => $cotisationpargroupe,
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
            'data_class' => Cotiserpargroupe::class,
            'groupe' => null,
            'cotisationpargroupe' => null,
        ]);
    }

}
