<?php

namespace App\Form;

use App\Entity\Classecodim;
use App\Entity\Cultecodim;
use App\Entity\Enfant;
use App\Entity\Presenceculteecodim;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PresenceculteecodimType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $enfant = $options['enfant'];
        $classecodim = $options['classecodim'];
        $cultecodim = $options['cultecodim'];

        $builder
                ->add('enfant', EntityType::class, array(
                    'choice_label' => function ($enfant) {
                        return  $enfant->getNom();
                    },
                    'class' => Enfant::class,
                    'choices' => $enfant,
                    'multiple' => true,
                    'required' => true,
                    'attr' => array('class' => 'select2'),
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('c')
                        ->orderBy('c.id', 'ASC');
                    },
                ))
                //->add('cultecodim')
                ->add('classecodim')
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
        $formModifier1 = function (FormInterface $form, Classecodim $classecodim = null) {
            $cultecodims = null === $classecodim ? [] : $classecodim->getCultecodims(['deletedAt' => NULL]);

            $form->add('cultecodim', EntityType::class, [
                'class' => Cultecodim::class,
                'placeholder' => $classecodim ? 'Choix de la cotisation' : 'Sélectionnez un classecodim d\'abord ',
                'label' => false,
                'choices' => $classecodim ? $cultecodims : []
            ]);
        };

        $builder->addEventListener(
                FormEvents::PRE_SET_DATA,
                function (FormEvent $event) use ($formModifier1) {
                    // this would be your entity, i.e. ClassecodimMeetup
                    $data = $event->getData();

                    $formModifier1($event->getForm(), $data->getClassecodim());
                }
        );

        $builder->get('classecodim')->addEventListener(
                FormEvents::POST_SUBMIT,
                function (FormEvent $event) use ($formModifier1) {
                    // It's important here to fetch $event->getForm()->getData(), as
                    // $event->getData() will get you the client data (that is, the ID)
                    $classecodim = $event->getForm()->getData();

                    // since we've added the listener to the child, we'll have to pass on
                    // the parent to the callback functions!
                    $formModifier1($event->getForm()->getParent(), $classecodim);
                }
        );
    }

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefaults([
            'data_class' => Presenceculteecodim::class,
            'cultecodim' => null,
            'enfant' => null,
            'classecodim' => null,
        ]);
    }

}
