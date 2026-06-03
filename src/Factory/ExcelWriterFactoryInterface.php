<?php

namespace App\Factory;

use App\Enum\ExcelFormat;
use OpenSpout\Writer\WriterInterface;

interface ExcelWriterFactoryInterface
{
    public function fromFormat(ExcelFormat $format): WriterInterface;
}