<?php

namespace App\Importer;

use App\Enum\ExcelFormat;
use App\Factory\ExcelReaderFactory;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Reader\SheetInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ExcelOpenSpoutImporter implements ImporterInterface
{
    public function __construct(
        private readonly ExcelReaderFactory $factory
    ) {
    }

    public function import(UploadedFile $file): array
    {
        $data = [];
        $extension = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
        $format = ExcelFormat::fromExtension($extension);
        $reader = $this->factory->fromFormat($format);

        $reader->open($file->getPathname());

        /** @var SheetInterface $sheet */
        foreach ($reader->getSheetIterator() as $sheet) {
            /** @var Row $row */
            foreach ($sheet->getRowIterator() as $row) {
                $data[] = $row->toArray();
            }
        }

        return $data;
    }
}