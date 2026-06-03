<?php

namespace App\Form;

use App\Entity\Fidele;
use App\Entity\Pastorale;
use Doctrine\ORM\EntityRepository;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class PastoraleType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $fidele1 = $options['pasteur1'];
        $fidele2 = $options['pasteur2'];
        $builder
                ->add('datepastorale', DateTimeType::class, [
                    'date_widget' => 'single_text',
                    'required' => true,
                ])
                ->add('lieu')
                ->add('datefin', DateTimeType::class, [
                    'date_widget' => 'single_text',
                    'required' => true,
                ])
                ->add('note', CKEditorType::class, [
                    'required' => false,
                ])
                ->add('ordredujour')
                ->add('pasteur1', EntityType::class, array(
                    'choice_label' => function ($fidele1) {
                        return $fidele1->getNomfidele();
                    },
                    'class' => Fidele::class,
                    'choices' => $fidele1,
                    'placeholder' => '-- Choix messager --',
                    'required' => false,
                    'multiple' => false,
                    'attr' => array('class' => 'select2'),
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('c')
                        ->orderBy('c.id', 'ASC');
                    },
                ))
                ->add('pasteur2', EntityType::class, array(
                    'choice_label' => function ($fidele2) {
                        return $fidele2->getNomfidele();
                    },
                    'class' => Fidele::class,
                    'choices' => $fidele2,
                    'placeholder' => '-- Choix messager --',
                    'required' => false,
                    'multiple' => false,
                    'attr' => array('class' => 'select2'),
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('c')
                        ->orderBy('c.id', 'ASC');
                    },
                ))
                ->add('type', ChoiceType::class, [
                    'required' => true,
                    'multiple' => false,
                    'expanded' => false,
                    'choices' => [
                        'Pastorale locale' => 'Pastorale locale',
                        'Assemblée locale' => 'Assemblée locale',
                        'Reunion des baptisés' => 'Reunion des baptisés',
                        'Conseil restreint' => 'Conseil restreint',
                        'Conseil elargi' => 'Conseil elargi',
                        'Autres' => 'Autres',
                    ],
                ])
                ->add('brochure', FileType::class, [
                    'label' => 'Procès verbal',
                    // unmapped means that this field is not associated to any entity property
                    'mapped' => false,
                    // make it optional so you don't have to re-upload the PDF file
                    // every time you edit the Product details
                    'required' => false,
                    // unmapped fields can't define their validation using annotations
                    // in the associated entity, so you can use the PHP constraint classes
                    'constraints' => [
                        new File([
                            'maxSize' => '1024k',
                            'mimeTypes' => [
                                'application/pdf',
                                'application/x-pdf',
                            ],
                            'mimeTypesMessage' => 'Veuillez choisir un fichier au format PDF',
                                ])
                    ],
                ])
                ->add('save', SubmitType::class, [
                    'attr' => [
                        'value' => 'create-don'
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
            'data_class' => Pastorale::class,
            'pasteur1' => null,
            'pasteur2' => null,
        ]);
    }

}
