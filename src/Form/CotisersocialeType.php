<?php

namespace App\Form;

use App\Entity\Cotisationsociale;
use App\Entity\Cotisersociale;
use App\Entity\Fidele;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CotisersocialeType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $cotisationsociale = $options['cotisationsociale'];
        $fidele = $options['fidele'];
        $builder
                ->add('datecotiser', DateType::class, [
                    // renders it as a single text box
                    'widget' => 'single_text',
                    'required' => true,
                    // this is actually the default format for single_text
                    'format' => 'yyyy-MM-dd',
                ])
                ->add('montantpayer')
                ->add('cotisationsociale', EntityType::class, array(
                    'choice_label' => function ($cotisationsociale) {
                        return $cotisationsociale->getObjet() . ' Montant ' . $cotisationsociale->getMontant();
                    },
                    'class' => Cotisationsociale::class,
                    'choices' => $cotisationsociale,
                    'multiple' => false,
                    'attr' => array('class' => 'select2'),
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('c')
                                ->orderBy('c.id', 'DESC');
                    },
                ))
                ->add('fidele', EntityType::class, array(
                    'choice_label' => function ($fidele) {
                        return  $fidele->getNomfidele();
                    },
                    'class' => Fidele::class,
                    'choices' => $fidele,
                    'multiple' => false,
                    'attr' => array('class' => 'select2'),
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('c')
                                ->orderBy('c.id', 'DESC');
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
            'data_class' => Cotisersociale::class,
            'cotisationsociale' => null,
            'fidele' => null,
        ]);
    }

}
