<?php

namespace App\Form;

use App\Entity\Cellule;
use App\Entity\Fidele;
use App\Entity\Seancecellule;
use Doctrine\ORM\EntityRepository;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Dropzone\Form\DropzoneType;

class SeancecelluleType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $fidele = $options['fidele'];
        
        $builder
            ->add('datesuper', DateType::class, ['widget' => 'single_text', 'format' => 'yyyy-MM-dd', 'required' => true])
            ->add('lieu', TextType::class, ['required' => false])
            ->add('theme', TextType::class, ['required' => false])
            ->add('nbrepresent')
            ->add('femme')
            ->add('enfant')
            ->add('objet', TextType::class, ['required' => false])
            ->add('versets', TextType::class, ['required' => false])
            ->add('resume', CKEditorType::class, ['required' => false])
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
                'attr' => ['placeholder' => 'Nom et Prénoms']
            ])
            ->add('fidele', EntityType::class, [
                'choice_label' => function ($fidele) {
                    return $fidele->getNomfidele() . ' ' . $fidele->getContact1();
                },
                'class' => Fidele::class,
                'choices' => $fidele,
                'placeholder' => '-- Choix messager --',
                'required' => false,
                'multiple' => false,
                'attr' => ['class' => 'select2'],
            ])
            // Supprimer le champ 'cellule' car il est déjà assigné dans le controller
            // ->add('cellule', ...)  // À supprimer
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
            ])
            ->add('save', SubmitType::class, [
                'attr' => ['value' => 'create-seancecellule']
            ])
            ->add('saveAndAdd', SubmitType::class, [
                'attr' => ['value' => 'save-and-add']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => Seancecellule::class,
            'fidele' => null,
        ]);
    }
}