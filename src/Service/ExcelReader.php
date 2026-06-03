<?php
// src/Service/ExcelReader.php

namespace App\Service;

use PhpOffice\PhpSpreadsheet\IOFactory;

class ExcelReader
{
    public function readData($filePath)
    {
        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();

        $data = [];
        foreach ($worksheet->getRowIterator() as $row) {
            $rowData = [];
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false); // This ensures all cells are included
            foreach ($cellIterator as $cell) {
                $rowData[] = $cell->getValue();
            }
            $data[] = $rowData;
        }

        return $data;
    }
}
