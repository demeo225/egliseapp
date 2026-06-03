<?php

namespace App\Form;

use App\Enum\ExcelFormat;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class ExcelExportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('format', EnumType::class, [
                'class' => ExcelFormat::class,
                'placeholder' => 'Format',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Exporter',
            ])
        ;
    }
}
