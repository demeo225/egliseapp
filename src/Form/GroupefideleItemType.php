<?php

namespace App\Form;

use App\Entity\Groupefidele;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Doctrine\ORM\EntityRepository;

class GroupefideleItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('departement', EntityType::class, [
                'class' => 'App\Entity\Departement',  // Utilisez le chemin complet
                'choices' => $options['departement'],
                'choice_label' => 'nom',
                'placeholder' => 'Choisir une direction',
                'required' => true,
                'label' => 'Direction',
                'attr' => ['class' => 'form-control']
            ])
            ->add('groupe', EntityType::class, [
                'class' => 'App\Entity\Groupe',
                'choices' => $options['groupe'],
                'choice_label' => 'nom',
                'placeholder' => 'Choisir une sous-direction',
                'required' => true,
                'label' => 'Sous-direction',
                'attr' => ['class' => 'form-control']
            ])
            ->add('fidele', EntityType::class, [
                'class' => 'App\Entity\Fidele',
                'choices' => $options['fidele'],
                'choice_label' => function($fidele) {
                    return $fidele->getNomfidele() . ' ' . ($fidele->getContact1() ?? '');
                },
                'placeholder' => 'Choisir un fidèle',
                'required' => true,
                'label' => 'Fidèle',
                'attr' => ['class' => 'form-control']
            ])
            ->add('rolegroupe', ChoiceType::class, [
                'choices' => [
                    'Membre' => 'Membre',
                    'Responsable' => 'Responsable',
                    'Secrétaire' => 'Secrétaire',
                    'Trésorier' => 'Trésorier',
                ],
                'placeholder' => 'Choisir un rôle',
                'required' => true,
                'label' => 'Rôle',
                'attr' => ['class' => 'form-control']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Groupefidele::class,
            'fidele' => [],
            'groupe' => [],
            'departement' => [],
        ]);
    }
}