<?php

namespace App\Form;

use App\Entity\Culte;
use App\Entity\Fidele;
use App\Entity\Typeculte;
use Doctrine\ORM\EntityRepository;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\UX\Dropzone\Form\DropzoneType;

class CulteType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $fidele = $options['fidele'];
        $typeculte = $options['typeculte'];
        $builder
                ->add('dateculte', DateType::class, [
                    // renders it as a single text box
                    'widget' => 'single_text',
                    'required' => true,
                    // this is actually the default format for single_text
                    'format' => 'yyyy-MM-dd',
                ])
//                ->add('dirigeant', TextType::class, [
//                    'required' => true,
//                ])
                ->add('orateur', TextType::class, [
                    'required' => false,
                 
                        ])
                ->add('theme', TextType::class, [
                    'required' => false,
                ])
                ->add('reference', TextType::class, [
                    'required' => false,
                ])
                    ->add('invite')
                    ->add('ame')

                ->add('messager', EntityType::class, array(
                    'choice_label' => function ($fidele) {
                        return $fidele->getNomfidele();
                    },
                    'class' => Fidele::class,
                    'choices' => $fidele,
                    'multiple' => false,
                    'placeholder' => '--Choix du messager--',
                    'required' => false,
                    'attr' => array('class' => 'select2'),
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('c')
                                ->orderBy('c.id', 'ASC');
                    },
                ))
                ->add('dirigeant', EntityType::class, array(
                    'choice_label' => function ($fidele) {
                        return $fidele->getNomfidele();
                    },
                    'class' => Fidele::class,
                    'choices' => $fidele,
                    'placeholder' => '--Choix du dirigeant--',
                    'required' => false,
                    'multiple' => false,
                    'attr' => array('class' => 'select2'),
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('c')
                                ->orderBy('c.id', 'ASC');
                    },
                ))
                      ->add('typeculte', EntityType::class, array(
                    'choice_label' => function ($typeculte) {
                        return $typeculte->getLibelle();
                    },
                    'class' => Typeculte::class,
                    'choices' => $typeculte,
                    'placeholder' => '--Type culte--',
                    'required' => false,
                    'multiple' => false,
                    'attr' => array('class' => 'select2'),
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('c')
                                ->orderBy('c.id', 'ASC');
                    },
                ))
                ->add('message', CKEditorType::class, [
                    'required' => false,
                                ])
                ->add('nmbrehomme')
                ->add('nobrefemme')
                ->add('nbrefant')
                ->add('categorieculte', ChoiceType::class, [
                    'required' => false,
                    'multiple' => false,
                    'expanded' => false,
                    'placeholder' => '--Choix categorie--',
                    'choices' => [
                        'Culte ordinaire' => 'Culte ordinaire',
                        'Culte du soir' => 'Culte du soir',
                        'Prière du matin' => 'Prière du matin',
                        'Veillée de prière' => 'Veillée de prière',
                        'Seminaire' => 'Seminaire',
                        'Autre' => ' Autre',
                    ],
                ])
                       ->add('photo', DropzoneType::class, [
                    'required' => false,
                    'mapped' => false,
                    
                    'attr' => [
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

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => Culte::class,
            'fidele' => null,
            'typeculte' => null,
        ]);
    }

}
