<?php

namespace App\Form;

use App\Entity\Fidele;
use App\Entity\Visite;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class VisiteType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options): void {
                $receptionpar = $options['receptionpar'];

        $builder
                ->add('visiteur', TextType::class, [
                    'required' => true,
                     'attr' => [
                        'placeholder' => 'Nom & prénoms',
                    ],])
                ->add('sexe', ChoiceType::class, [
                    'required' => true,
                    'multiple' => false,
                    'expanded' => false,
                    'choices' => [
                        'Homme' => 'Homme',
                        'Femme' => 'Femme',
                        'Autre' => 'Autre',
                    ],
                ])
                ->add('contact1', TextType::class, [
                    'required' => false,
                    'constraints' => [
                             new Length([
                            'min' => 10,
                            'minMessage' => 'Verifiez la taille du téléphone',
                            // max length allowed by Symfony for security reasons
                            'max' => 16,
                                ]),
                    ],
                ])
                ->add('datevisite', DateType::class, ['widget' => 'single_text', 'format' => 'yyyy-MM-dd', 'required' => true])
                ->add('receptionpar', EntityType::class, array(
                    'choice_label' => function ($fidele) {
                        return $fidele->getNomfidele();
                    },
                    'class' => Fidele::class,
                    'choices' => $receptionpar,
                    'placeholder' => '-- Choix messager --',
                    'required' => false,
                    'multiple' => false,
                    'attr' => array('class' => 'select2'),
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('c')
                        ->orderBy('c.id', 'ASC');
                    },
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

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefaults([
            'data_class' => Visite::class,
            'receptionpar' =>null,
        ]);
    }

}
