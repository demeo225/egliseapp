<?php

namespace App\Enum;

enum ExcelFormat
{
    case CSV;
    case ODS;
    case XLSX;

    public static function fromExtension(string $extension): ExcelFormat
    {
        return match($extension) {
            'csv' => ExcelFormat::CSV,
            'ods' => ExcelFormat::ODS,
            'xlsx' => ExcelFormat::XLSX,
        };
    }

    public function contentType(): string
    {
        return match($this) {
            ExcelFormat::CSV => 'text/csv',
            ExcelFormat::ODS => 'application/vnd.oasis.opendocument.spreadsheet',
            ExcelFormat::XLSX => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        };
    }

    public function extension(): string
    {
        return match($this) {
            ExcelFormat::CSV => 'csv',
            ExcelFormat::ODS => 'ods',
            ExcelFormat::XLSX => 'xlsx',
        };
    }
}