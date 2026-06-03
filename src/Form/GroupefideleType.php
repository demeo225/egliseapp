<?php

namespace App\Form;

use App\Entity\Groupefidele;
use App\Entity\Departement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class GroupefideleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('groupefideles', CollectionType::class, [
                'entry_type' => GroupefideleItemType::class,
                'entry_options' => [
                    'fidele' => $options['fidele'],
                    'groupe' => $options['groupe'],
                    'departement' => $options['departement'],
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => false,
            ])
         ->add('save', SubmitType::class, [
                'label' => 'Valider',
                'attr' => ['class' => 'btn btn-primary']
            ])
            ->add('saveAndAdd', SubmitType::class, [
                'label' => 'Valider et continuer',
                'attr' => ['class' => 'btn btn-success']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
            'fidele' => [],
            'groupe' => [],
            'departement' => [],
        ]);
    }
}