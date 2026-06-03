<?php


namespace App\Form;

use App\Entity\Cotisationcellule;
use App\Entity\Cotisercellule;
use App\Entity\Cellule;
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

class CotisercelluleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
       // $cellule = $options['cellule'];
        $fidele = $options['fidele'];
        $cotisationcellule = $options['cotisationcellule'];

        $builder
          
            ->add('fidele', EntityType::class, [
                'choice_label' => function ($fidele) {
                    return $fidele->getNomfidele() . '-' . $fidele->getContact1();
                },
                'class' => Fidele::class,
                'choices' => $fidele,
                'placeholder' => 'Choix fidèle',
                'multiple' => false,
                'attr' => ['class' => 'select2', 'id' => 'cotisercellule_fidele'],
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.id', 'DESC');
                },
            ])
            ->add('cotisationcellule', EntityType::class, [
                'class' => Cotisationcellule::class,
                'choices' => $cotisationcellule,
                'required' => true,
                'placeholder' => 'Choix cotisation',
                'attr' => ['class' => 'select2', 'id' => 'cotisercellule_cotisationcellule'],
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
                    'id' => 'cotisercellule_montantpayer',
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
                'attr' => ['class' => 'form-control', 'id' => 'cotisercellule_datecotiser'],
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
            'data_class' => Cotisercellule::class,
           // 'cellule' => null,
            'fidele' => null,
            'cotisationcellule' => null,
        ]);
    }
}