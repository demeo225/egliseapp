<?php

namespace App\Form;

use App\Entity\Deces;
use App\Entity\Fidele;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DecesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $fidele = $options['fidele'];
        $builder
            ->add('datedeces', DateType::class, [
                    // renders it as a single text box
                    'widget' => 'single_text',
                    'required' => true,
                    // this is actually the default format for single_text
                    'format' => 'yyyy-MM-dd',
                ])
            ->add('lieudeces', TextType::class, [
                    'required' => false,
                    'attr'=>[
                        'placeholder' =>'Entrer le lieu du décès'
                    ]
                ])

            ->add('raisondeces', TextareaType::class, [
                    'required' => false,
                    'attr'=>[
                        'placeholder' =>'Entrer la cause du décès'
                    ]
                ])
      
                    ->add('fidele', EntityType::class, array(
                    'choice_label' => function ($fidele) {
                        return $fidele->getCode() . ' ' . $fidele->getNomfidele();
                    },
                    'class' => Fidele::class,
                    'choices' => $fidele,
                    'multiple' => false,
                    'attr' => array('class' => 'select2'),
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('c')
                                ->orderBy('c.id', 'ASC');
                    },
                ))
        ->add('save', SubmitType::class, [
                    'attr' => [
                        'value' => 'create-deces'
                    ]
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Deces::class,
                        'fidele'=>null,

        ]);
    }
}
