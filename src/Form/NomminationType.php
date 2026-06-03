<?php

namespace App\Form;

use App\Entity\Fidele;
use App\Entity\Nommination;
use Doctrine\ORM\EntityRepository;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NomminationType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $fidele = $options['fidele'];

        $builder
                ->add('datenomination', DateType::class, [
                    // renders it as a single text box
                    'widget' => 'single_text',
                    'required' => true,
                    // this is actually the default format for single_text
                    'format' => 'yyyy-MM-dd',
                ])
                ->add('fidele', EntityType::class, array(
                    'choice_label' => function ($fidele) {
                        return $fidele->getNomfidele();
                    },
                    'class' => Fidele::class,
                    'choices' => $fidele,
                    'multiple' => true,
//                    'mapped' => false,
                    'required' => true,
                    'attr' => array('class' => 'select2'),
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('c')
                        ->orderBy('c.id', 'ASC');
                    },
                ))
                ->add('decret', TextType::class, [
                    'required' => true,
                ])
                ->add('deteil', CKEditorType::class, [
                    'required' => true,
                ])
                ->add('save', SubmitType::class, [
                    'attr' => [
                        'value' => 'create-bapteme'
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
            'data_class' => Nommination::class,
            'fidele' => null,
        ]);
    }

}
