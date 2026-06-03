<?php

namespace App\Form;

use App\Entity\Sms;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;



class SmsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('destinataire')
            ->add('message', CKEditorType::class, [
                'required'=>true,
                      'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez écrire un message',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Message trop court',
                        // max length allowed by Symfony for security reasons
                        'max' => 180,
                    ]),
                ],
            ])
                    ->add('save', SubmitType::class, [
                    'attr' => [
                        'value' => 'create'
                    ]
                ])
                ->add('saveAndAdd', SubmitType::class, [
                    'attr' => [
                        'value' => 'save-and-add'
                    ]
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sms::class,
        ]);
    }
}
