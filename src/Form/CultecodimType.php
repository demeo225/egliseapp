<?php

namespace App\Form;

use App\Entity\Classecodim;
use App\Entity\Cultecodim;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;

class CultecodimType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
       $classecodim = $options['classecodim'];
        $builder
                ->add('dateculte', DateType::class, [
                    // renders it as a single text box
                    'widget' => 'single_text',
                    'required' => true,
                    // this is actually the default format for single_text
                    'format' => 'yyyy-MM-dd',
                ])
                ->add('nbrefille')
                ->add('nbregarcon')
                ->add('moniteur1', TextType::class, [
                    'required' => false,
                ])
                ->add('moniteur2', TextType::class, [
                    'required' => false,
                  ])
                ->add('note', CKEditorType::class, [
                    'required' => false,
                ])
                ->add('versets', TextType::class, [
                    'required' => false,
                ])
                ->add('versetretenir', TextType::class, [
                    'required' => false,
                ])
                ->add('offrande')
                ->add('classecodim')
                     ->add('classecodim', EntityType::class, [
                    'class' => Classecodim::class,
                    'choice_label' => 'nom',
                         'choices'=>$classecodim,
                    'required' => true,
                    'attr' => array('class' => 'select2'),
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
            'data_class' => Cultecodim::class,
            'classecodim'=>null,
        ]);
    }

}
