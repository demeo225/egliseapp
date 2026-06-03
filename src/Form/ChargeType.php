<?php

namespace App\Form;

use App\Entity\Charge;
use App\Entity\Departement;
use App\Entity\Objetcharge;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChargeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $departement = $options['departement'];
        $objetcharge = $options['objetcharge'];
        $builder
       
            ->add('detail')
            ->add('datecharge', DateType::class, [
                // renders it as a single text box
                'widget' => 'single_text',
                'required' => true,
                // this is actually the default format for single_text
                'format' => 'yyyy-MM-dd',
            ])
           
            ->add('montant', IntegerType::class,[

                'required' => true,
                            ])
            // ->add('dixmille')
            // ->add('dixmille')
            // ->add('cinqmille')
            // ->add('deuxmille')
            // ->add('mille')
            // ->add('centbillet')
            // ->add('centpiece')
            // ->add('deuxcent')
            // ->add('cent')
            // ->add('cinquante')
            // ->add('vingtcinq')
            // ->add('dix')
            // ->add('cinq')
            ->add('departement', EntityType::class, array(
                'choice_label' => function ($departement) {
                    return $departement->getNom();
                },
               // 'autocomplete' => true,
                'class' => Departement::class,
                'choices' => $departement,
                'multiple' => false,
                'required' => false,
                'placeholder' => '-- Choix  Objet depense --',
             
            ))


            ->add('objetcharge', EntityType::class, array(
                'choice_label' => function ($objetcharge) {
                    return $objetcharge->getLibelle();
                },
                //'autocomplete' => true,
                'class' => Objetcharge::class,
                'choices' => $objetcharge,
                'multiple' => false,
                'required' => false,
                'placeholder' => '-- Choix  Objet depense --',

            ))


            ->add('save', SubmitType::class, [
                'attr' => [
                    'value' => 'create'
                ]
            ])
            ->add('saveAndAdd', SubmitType::class, [
                'attr' => [
                    'value' => 'save-and-add'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Charge::class,
            'departement' => null,
            'objetcharge' => null,
        ]);
    }
}
