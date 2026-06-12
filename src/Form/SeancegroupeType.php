<?php

namespace App\Form;

use App\Entity\Fidele;
use App\Entity\Groupe;
use App\Entity\Seancegroupe;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Dropzone\Form\DropzoneType;

class SeancegroupeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        
        $builder
            ->add('datesuper', DateType::class, [
                'widget' => 'single_text',
            ])

            ->add('lieu', TextType::class, [
                'required' => false,
            ])

            ->add('theme', TextType::class, [
                'required' => false,
            ])

            ->add('femme')
            ->add('enfant')
            ->add('nbrepresent')

            ->add('objet', TextType::class, [
                'required' => false,
            ])

            ->add('versets', TextType::class, [
                'required' => false,
            ])

            ->add('resume', CKEditorType::class, [
                'required' => false,
            ])

            ->add('offrande')

            ->add('heuredebut', TimeType::class, [
                'widget' => 'choice',
                'required' => false,
            ])

            ->add('heurefin', TimeType::class, [
                'widget' => 'choice',
                'required' => false,
            ])

            // ✅ Fidèles filtrés par groupe (passés depuis le controller)
            ->add('fidele', EntityType::class, [
                'class' => Fidele::class,
                'choices' => $options['fideles'],
                'choice_label' => static fn (Fidele $fidele): string => $fidele->getNomfidele(),
                'placeholder' => '-- Choix messager --',
                'required' => false,
                'multiple' => false,
                'attr' => [
                    'class' => 'select2',
                ],
            ])
            ->add('typeofficiant', TextType::class, [
                'required' => false,
                'attr' => ['placeholder' => 'Nom et Prénoms',]
            ])

            // // ✅ Groupe unique du responsable
            // ->add('groupe', EntityType::class, [
            //     'class' => Groupe::class,
            //     'choices' => $options['groupes'],
            //     'choice_label' => 'nom',
            
            //     'required' => true,
            //     'multiple' => false,
            //     'attr' => [
            //         'class' => 'select2',
            //     ],
            // ])

            ->add('typeactivite', ChoiceType::class, [
                'choices' => [
                    'En présentiel' => 'En présentiel',
                    'En ligne' => 'En ligne',
                ],
                'placeholder' => '-- Choix --',
                'required' => true,
            ])

            ->add('photo', DropzoneType::class, [
                'mapped' => false,
                'required' => false,
            ])

            ->add('save', SubmitType::class)
            ->add('saveAndAdd', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Seancegroupe::class,
            'fideles' => [],   // ✅ tableau de Fidele
           // 'groupes' => [],   // ✅ tableau de Groupe
        ]);

        // 🔒 Sécurise le type des options
        $resolver->setAllowedTypes('fideles', 'array');
      //  $resolver->setAllowedTypes('groupes', 'array');
    }
}
