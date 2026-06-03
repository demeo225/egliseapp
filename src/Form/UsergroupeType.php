<?php

namespace App\Form;

use App\Entity\Groupe;
use App\Entity\User;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UsergroupeType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $user = $options['user'];

        $builder
                ->add('nom', TextType::class, [
                     'attr' => ['readonly' => true],
                ])
                ->add('description', TextType::class, [
                     'attr' => ['readonly' => true],
                ])
                ->add('responsable1', TextType::class, [
                     'attr' => ['readonly' => true],
                ])
                ->add('responsable2', TextType::class, [
                     'attr' => ['readonly' => true],
                ])
                 ->add('users', EntityType::class, [
                'class' => User::class,
                'choices' => $user,
                'choice_label' => function (User $user) {
                    return $user->getNomuser().' '.$user->getPrenom();
                },
                'multiple' => true,
                'expanded' => false,
                'attr' => ['class' => 'select2'],
            ])
                ->add('save', SubmitType::class, [
                    'attr' => [
                        'value' => 'create-don'
                    ]
                ])
         

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefaults([
            'data_class' => Groupe::class,
            'user' => null,
        ]);
    }

}
