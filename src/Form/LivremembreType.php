<?php

namespace App\Form;

use App\Entity\Livremembre;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LivremembreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('numero')
            ->add('datelivret')
            ->add('etat')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('createdFromIp')
            ->add('updatedFromIp')
            ->add('deletedAt')
            ->add('deletedFromIp')
            ->add('editable')
            ->add('fidele')
            ->add('eglise')
            ->add('createdBy')
            ->add('updatedBy')
            ->add('deletedBy')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Livremembre::class,
        ]);
    }
}
