<?php

namespace App\Factory;

use App\Enum\ExcelFormat;
use OpenSpout\Writer\CSV\Writer as CSVWriter;
use OpenSpout\Writer\ODS\Writer as ODSWriter;
use OpenSpout\Writer\WriterInterface;
use OpenSpout\Writer\XLSX\Writer as XLSXWriter;


class ExcelWriterFactory implements ExcelWriterFactoryInterface
{
    public function fromFormat(ExcelFormat $format): WriterInterface
    {
        return match($format) {
            ExcelFormat::CSV => new CSVWriter(),
            ExcelFormat::ODS => new ODSWriter(),
            ExcelFormat::XLSX => new XLSXWriter(),
        };
    }
}