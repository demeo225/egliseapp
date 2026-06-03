<?php

namespace App\Form;

use App\Entity\Eglise;
use App\Entity\Paiement;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaiementType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
                ->add('datepaiement', DateType::class,
                        ['widget' => 'single_text',
                            'required' => true,]
                )
                ->add('montant')
                ->add('dateecheance', DateType::class,
                        ['widget' => 'single_text',
                            'required' => true,])
                              ->add('eglise', EntityType::class, array(
                    'choice_label' => function ($eglise) {
                        return $eglise->getCode() . ' ' . $eglise->getDenomination();
                    },
                    'class' => Eglise::class,
                    'multiple' => false,
                    'attr' => array('class' => 'select2'),
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('c')
                                ->where('c.etat = 1')
//                                ->setParameter(1, $er)
                                ->orderBy('c.id', 'ASC');
                    },
                ))
      ->add('save', SubmitType::class, [
                    'attr' => [
                        'value' => 'create'
                    ]
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefaults([
            'data_class' => Paiement::class,
        ]);
    }

}
