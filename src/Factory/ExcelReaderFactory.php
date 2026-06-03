<?php

namespace App\Factory;

use App\Enum\ExcelFormat;
use OpenSpout\Reader\CSV\Reader as CSVReader;
use OpenSpout\Reader\ODS\Reader as ODSReader;
use OpenSpout\Reader\ReaderInterface;
use OpenSpout\Reader\XLSX\Reader as XLSXReader;

class ExcelReaderFactory implements ExcelReaderFactoryInterface
{

    public function fromFormat(ExcelFormat $format): ReaderInterface
    {
        return match($format) {
            ExcelFormat::CSV => new CSVReader(),
            ExcelFormat::ODS => new ODSReader(),
            ExcelFormat::XLSX => new XLSXReader(),
        };
    }
}