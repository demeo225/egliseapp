<?php

namespace App\Form;

use App\Entity\Famille;
use App\Entity\Cotisationparfamille;
use App\Entity\Cotiserparfamille;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CotiserparfamilleType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $famille = $options['famille'];
        $cotisationparfamille = $options['cotisationparfamille'];
        $builder
                ->add('montantpayer')
                ->add('datecotiser', DateType::class, [
                    // renders it as a single text box
                    'widget' => 'single_text',
                    'required' => true,
                    // this is actually the default format for single_text
                    'format' => 'yyyy-MM-dd',
                ])
                ->add('famille', EntityType::class, array(
                    'choice_label' => function ($famille) {
                        return $famille->getNom();
                    },
                    'class' => Famille::class,
                    'choices' => $famille,
                    'multiple' => false,
                    'attr' => array('class' => 'select2'),
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('c')
                        ->orderBy('c.id', 'ASC');
                    },
                ))
                ->add('cotisationparfamille', EntityType::class, array(
                    'choice_label' => function ($cotisationparfamille) {
                        return $cotisationparfamille->getObjet() . ' Montant ' . $cotisationparfamille->getMontant();
                    },
                    'class' => Cotisationparfamille::class,
                    'choices' => $cotisationparfamille,
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
            'data_class' => Cotiserparfamille::class,
            'famille' => null,
            'cotisationparfamille' => null,
        ]);
    }

}
