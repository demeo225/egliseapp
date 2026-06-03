<?php

namespace App\Form;

use App\Entity\Cellule;
use App\Entity\Fidele;
use App\Entity\Presencecellule;
use App\Entity\Seancecellule;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PresencecelluleType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $cellule = $options['cellule'];
        $fidele = $options['fidele'];
        $seancecellule = $options['seancecellule'];
        $builder
                ->add('cellule') 
                       ->add('seancecellule', EntityType::class, array(
                    'choice_label' => function ($seancecellule) {
                        return $seancecellule->getObjet();
                    },
                    'class' => Seancecellule::class,
                    'choices' => $seancecellule,
                    'multiple' => false,
                    'attr' => array('class' => 'select2'),
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('c')
                                ->orderBy('c.id', 'ASC');
                    },
                ))
                      ->add('fidele', EntityType::class, array(
                    'choice_label' => function ($fidele) {
                       return $fidele->getCode() . ' ' . $fidele->getNomfidele();
                    },
                    'class' => Fidele::class,
                    'choices' => $fidele,
                    'multiple' => true,
                    'attr' => array('class' => 'select2'),
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('c')
                                ->orderBy('c.id', 'ASC');
                    },
                ))
       ->add('save', SubmitType::class, [
                    'attr' => [
                        'value' => 'create-don'
                    ]
                ])
        ;

//

//        $formModifier = function (FormInterface $form, Cellule $cellule = null) {
//            $fideles = null === $cellule ? [] : $cellule->getFideles(['deletedAt' => NULL]);
//
//            $form->add('fidele', EntityType::class, [
//                'class' => Fidele::class,
//                'placeholder' => $cellule ? 'Choix du fidèle' : 'Sélectionnez une cellule ',
//                'choices' => $cellule ? $fideles : [],
//            ]);
//        };
//
//        $builder->addEventListener(
//                FormEvents::PRE_SET_DATA,
//                function (FormEvent $event) use ($formModifier) {
//            // this would be your entity, i.e. CelluleMeetup
//            $data = $event->getData();
//
//            $formModifier($event->getForm(), $data->getCellule());
//        }
//        );
//
//        $builder->get('cellule')->addEventListener(
//                FormEvents::POST_SUBMIT,
//                function (FormEvent $event) use ($formModifier) {
//            // It's important here to fetch $event->getForm()->getData(), as
//            // $event->getData() will get you the client data (that is, the ID)
//            $cellule = $event->getForm()->getData();
//
//            // since we've added the listener to the child, we'll have to pass on
//            // the parent to the callback functions!
//            $formModifier($event->getForm()->getParent(), $cellule);
//        }
//        );

//



//
//        $formModifier1 = function (FormInterface $form, Cellule $cellule = null) {
//            $seancecellules = null === $cellule ? [] : $cellule->getSeancecellules(['deletedAt' => NULL]);
//
//            $form->add('seancecellule', EntityType::class, [
//                'class' => Seancecellule::class,
//                'placeholder' => $cellule ? 'Choix de la cotisation' : 'Sélectionnez une cellule ',
//                'label' => false,
//                'choices' => $cellule ? $seancecellules : []
//            ]);
//        };
//
//        $builder->addEventListener(
//                FormEvents::PRE_SET_DATA,
//                function (FormEvent $event) use ($formModifier1) {
//            // this would be your entity, i.e. CelluleMeetup
//            $data = $event->getData();
//
//            $formModifier1($event->getForm(), $data->getCellule());
//        }
//        );
//
//        $builder->get('cellule')->addEventListener(
//                FormEvents::POST_SUBMIT,
//                function (FormEvent $event) use ($formModifier1) {
//            // It's important here to fetch $event->getForm()->getData(), as
//            // $event->getData() will get you the client data (that is, the ID)
//            $cellule = $event->getForm()->getData();
//
//            // since we've added the listener to the child, we'll have to pass on
//            // the parent to the callback functions!
//            $formModifier1($event->getForm()->getParent(), $cellule);
//        }
//        );
    }

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefaults([
            'data_class' => Presencecellule::class,
            'cellule' => null,
            'fidele' => null,
            'seancecellule' => null,
        ]);
    }

}
