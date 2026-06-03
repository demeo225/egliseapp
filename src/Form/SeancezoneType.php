<?php

namespace App\Form;

use App\Entity\Fidele;
use App\Entity\Seancezone;
use App\Entity\Zone;
use Doctrine\ORM\EntityRepository;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Dropzone\Form\DropzoneType;

class SeancezoneType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options): void {
      //  $zone = $options['zone'];
        $fidele = $options['fidele'];
        $builder
                ->add('datesuper', DateType::class, ['widget' => 'single_text', 'format' => 'yyyy-MM-dd'])
                ->add('lieu', TextType::class, [
                    'required' => true,
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
                    'required' => false,
                    'placeholder' => [
                        'hour' => 'Heure', 'minute' => 'Minute', 'second' => 'Seconde',
                    ],
                ])
                ->add('heurefin', TimeType::class, [
                    'required' => false,
                    'placeholder' => [
                        'hour' => 'Heure', 'minute' => 'Minute', 'second' => 'Seconde',
                    ],
                ])
                ->add('typeofficiant', TextType::class, [
                    'required' => false,
                    'attr' => ['placeholder' => 'Nom et Prénoms',]
                ])
                ->add('fidele', EntityType::class, array(
                    'choice_label' => function ($fidele) {
                        return $fidele->getNomfidele() . ' ' . $fidele->getContact1();
                    },
                    'class' => Fidele::class,
                    'choices' => $fidele,
                    'multiple' => false,
                    'placeholder' => '-- Choix messager --',
                    'required' => false,
                    //'attr' => array('class' => 'select2'),
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('c')
                        ->orderBy('c.id', 'ASC');
                    },
                ))
                  ->add('typeactivite', ChoiceType::class, [
                    'required' => true,
                    'multiple' => false,
                    'expanded' => false,
                    'placeholder' => '-- Choix --',
                    'choices' => [
                        'En presentielle' => 'En presentielle',
                        'En ligne' => 'En ligne',
                    ],
                ])
          
               ->add('photo', DropzoneType::class, [
                    'required' => false,
                    'mapped' => false,
                    
                    'attr' => [
                    ],
                ])
                // ->add('zone', EntityType::class, [
                //     'class' => Zone::class,
                //     'choices' => $zone,
                   
                //     'attr' => array('class' => 'select2'),
                // ])
                ->add('save', SubmitType::class, [
                    'attr' => [
                        'value' => 'create-seancezone'
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
            'data_class' => Seancezone::class,
           // 'zone' => null,
            'fidele' => null,
        ]);
    }

}
