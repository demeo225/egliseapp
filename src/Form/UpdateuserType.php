<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UpdateuserType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('roles', ChoiceType::class, [
                    'required' => true,
                    'multiple' => false,
                    'expanded' => false,
                    'attr' => array('class' => 'select2'),
                    'choices' => [
                        'Pasteur' => 'ROLE_PASTEUR',
                        'Pasteur Second' => 'ROLE_SECRETAIRE',
                        'Secretaire' => 'ROLE_SECRETAIRE',
                        'Responsable evangelisation' => 'ROLE_RESPONSABLE_EVANGELISATION',
                        'Responsable evangelisation & departement' => 'ROLE_EVANGELISATION_DEPARTEMENT',
                        'Responsable evangelisation & zone' => 'ROLE_EVANGELISATION_ZONE',
                        'Trésorerie' => 'ROLE_RESPONSABLE_TRESORERIE',
                        'Responsable Finance' => 'ROLE_RESPONSABLE_FINANCE',
                        'Responsable Finance Nationale' => 'ROLE_RESPONSABLE_FINANCE_NATIONAL',
                        'Responsable conjugal' => 'ROLE_RESPONSABLE_CONJUGAL',
                        'Responsable du social' => 'ROLE_RESPONSABLE_SOCIAL',
                        'Responsable Cellule' => 'ROLE_RESPONSABLE_CELLULE',
                        'Responsable Departement' => 'ROLE_RESPONSABLE_DEPARTEMENT',
                        'Responsable Famille' => 'ROLE_RESPONSABLE_FAMILLE',
                        'Responsable Groupe' => 'ROLE_RESPONSABLE_GROUPE',
                        'Responsable Groupe & Cellule & Famille' => 'ROLE_GROUPE_CELLULE',
                        'Responsable Zone' => 'ROLE_RESPONSABLE_ZONE',
                        'Moniteur' => 'ROLE_RESPONSABLE_ECODIM',
                        'Ecodim & Departement & Cellule' => 'ROLE_MODERATEUR',
                        'Ecodim & Groupe & Cellule' => 'ROLE_ECODIM_GROUPE_CELLULE',
                        'Ecodim & Groupe ' => 'ROLE_ECODIM_GROUPE',
                        'Departement & Cellule' => 'ROLE_DEPARTEMENT_CELLULE',
                        'Zone & Departement' => 'ROLE_SUPERVISEUR',
                        'Administrateur' => 'ROLE_ADMIN',
                    ],
                ])
                ->add('nomuser', TextType::class)
                ->add('prenom', TextType::class)
                ->add('save', SubmitType::class, ['label' => 'Modifier'])

        ;
        // roles field data transformer
        $builder->get('roles')
                ->addModelTransformer(new CallbackTransformer(
                                function ($rolesArray) {
                                    // transform the array to a string
                                    return count($rolesArray) ? $rolesArray[0] : null;
                                },
                                function ($rolesString) {
                                    // transform the string back to an array
                                    return [$rolesString];
                                }
        ));
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }

}
