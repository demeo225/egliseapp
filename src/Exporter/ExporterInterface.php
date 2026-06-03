<?php

namespace App\Exporter;

use App\Enum\ExcelFormat;

interface ExporterInterface
{
    public function export(array $columnNames, array $data, ExcelFormat $format): \SplFileInfo;
}