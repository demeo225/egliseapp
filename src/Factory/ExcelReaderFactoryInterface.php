<?php

namespace App\Factory;

use App\Enum\ExcelFormat;
use OpenSpout\Reader\ReaderInterface;

interface ExcelReaderFactoryInterface
{
    public function fromFormat(ExcelFormat $format): ReaderInterface;
}