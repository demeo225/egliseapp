<?php

namespace App\Form;

use App\Entity\Cotisation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class SuppcotisationType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('montant')
                ->add('objet')
                ->add('typecotisation')
                ->add('periodecotisation')
                ->add('save', SubmitType::class, ['label' => 'Supprimer'])

        ;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => Cotisation::class,
            'label' => false,
            'attr' => ['readonly' => true],
        ]);
    }

}
