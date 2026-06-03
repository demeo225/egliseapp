<?php

namespace App\Form;

use App\Entity\Fidele;
use App\Entity\Typeculte;
use Doctrine\ORM\EntityRepository;
use App\DTO\BilanCulteDTO;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class BilanculteType extends AbstractType
{
    private $security;
    
    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $this->security->getUser();
        $eglise = $user ? $user->getEglise() : null;
        
        $builder
            ->add('typeculte', EntityType::class, [
                'class' => Typeculte::class,
                'label' => 'Type de culte',
                'required' => false,
                'placeholder' => 'Tous les types',
                'choice_label' => 'libelle',
                'query_builder' => function (EntityRepository $er) use ($eglise) {
                    if (!$eglise) {
                        return $er->createQueryBuilder('t')->where('1 = 0');
                    }
                    return $er->createQueryBuilder('t')
                        ->where('t.eglise = :eglise')
                        ->andWhere('t.deletedAt IS NULL')
                        ->setParameter('eglise', $eglise)
                        ->orderBy('t.libelle', 'ASC');
                },
            ])
            ->add('messager', EntityType::class, [
                'class' => Fidele::class,
                'label' => 'Messager/Orateur',
                'required' => false,
                'placeholder' => 'Tous les messagers',
                'choice_label' => 'nomfidele',
                'query_builder' => function (EntityRepository $er) use ($eglise) {
                    if (!$eglise) {
                        return $er->createQueryBuilder('f')->where('1 = 0');
                    }
                    return $er->createQueryBuilder('f')
                        ->where('f.eglise = :eglise')
                        ->andWhere('f.deletedAt IS NULL')
                        ->setParameter('eglise', $eglise)
                        ->orderBy('f.nomfidele', 'ASC');
                },
            ])
                ->add('dirigeant', EntityType::class, [
                'class' => Fidele::class,
                'label' => 'Dirigeant',
                'required' => false,
                'placeholder' => 'Tous les dirigeants',
                'choice_label' => 'nomfidele',
                'query_builder' => function (EntityRepository $er) use ($eglise) {
                    return $er->createQueryBuilder('f')
                        ->where('f.eglise = :eglise')
                        ->andWhere('f.deletedAt IS NULL')
                        ->setParameter('eglise', $eglise) // IMPORTANT
                        ->orderBy('f.nomfidele', 'ASC');
                },
            ])
            ->add('dateDebut', DateType::class, [
                'label' => 'Date début',
                'required' => false,
                'widget' => 'single_text',
                'html5' => true,
            ])
            ->add('dateFin', DateType::class, [
                'label' => 'Date fin',
                'required' => false,
                'widget' => 'single_text',
                'html5' => true,
            ])
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => true,
            'data_class' => BilanCulteDTO::class,
            'csrf_field_name' => '_token',
            'allow_extra_fields' => false,
        ]);
    }
}