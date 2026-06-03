<?php


namespace App\Form;

use App\Entity\Cotisationgroupe;
use App\Entity\Cotisergroupe;
use App\Entity\Groupe;
use App\Entity\Fidele;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CotisergroupeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
       // $groupe = $options['groupe'];
        $fidele = $options['fidele'];
        $cotisationgroupe = $options['cotisationgroupe'];

        $builder
            // ->add('groupe', EntityType::class, [
            //     'class' => Groupe::class,
            //     'choices' => $groupe,
            //     'required' => true,
            //     'attr' => ['class' => 'select2', 'id' => 'groupe_select'],
            // ])
            ->add('fidele', EntityType::class, [
                'choice_label' => function ($fidele) {
                    return $fidele->getNomfidele() . '-' . $fidele->getContact1();
                },
                'class' => Fidele::class,
                'choices' => $fidele,
                'placeholder' => 'Choix fidèle',
                'multiple' => false,
                'attr' => ['class' => 'select2', 'id' => 'cotisergroupe_fidele'],
              
            ])
            ->add('cotisationgroupe', EntityType::class, [
                'class' => Cotisationgroupe::class,
                'choices' => $cotisationgroupe,
                'required' => true,
                'placeholder' => 'Choix cotisation',
                'attr' => ['class' => 'select2', 'id' => 'cotisergroupe_cotisationgroupe'],
            ])
            ->add('montant_a_payer', TextType::class, [
                'required' => false,
                'label' => 'Montant à payer (FCFA)',
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'montant_a_payer',
                    'readonly' => true,
                ],
                'mapped' => false,
            ])
            ->add('montantpayer', IntegerType::class, [
                'required' => true,
                'label' => 'Montant payé (FCFA)',
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'cotisergroupe_montantpayer',
                    'min' => 0,
                ],
            ])
            ->add('reste', TextType::class, [
                'required' => false,
                'label' => 'Reste à payer (FCFA)',
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'reste_a_payer',
                    'readonly' => true,
                ],
                'mapped' => false,
            ])
            ->add('datecotiser', DateType::class, [
                'widget' => 'single_text',
                'required' => true,
                'format' => 'yyyy-MM-dd',
                'attr' => ['class' => 'form-control', 'id' => 'cotisergroupe_datecotiser'],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Valider',
                'attr' => ['class' => 'btn btn-primary'],
            ])
            ->add('saveAndAdd', SubmitType::class, [
                'label' => 'Valider et continuer',
                'attr' => ['class' => 'btn btn-secondary'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Cotisergroupe::class,
           // 'groupe' => null,
            'fidele' => null,
            'cotisationgroupe' => null,
        ]);
    }
}