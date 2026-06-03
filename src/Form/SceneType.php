<?php

namespace App\Form;

use App\Entity\Fidele;
use App\Entity\Scene;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SceneType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $fidele1 = $options['pasteur1'];
        $fidele2 = $options['pasteur2'];

        $builder
                ->add('datescene', DateType::class, ['widget' => 'single_text', 'format' => 'yyyy-MM-dd'])
                ->add('detail')
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
                        return $fidele2->getNomfidele() ;
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
            'data_class' => Scene::class,
            'pasteur1'=>null,
            'pasteur2'=>null,
            
        ]);
    }

}
