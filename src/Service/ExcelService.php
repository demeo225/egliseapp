<?php
// src/Service/ExcelService.php
namespace App\Service;

use App\Entity\Fidele;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Doctrine\ORM\EntityManagerInterface;


class ExcelService
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function import($filePath)
    {
        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();

        foreach ($worksheet->getRowIterator() as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);
            
            $data = [];
            foreach ($cellIterator as $cell) {
                $data[] = $cell->getValue();
            }

            // Assuming the first cell contains a unique identifier
            $existingRecord = $this->em->getRepository(Fidele::class)->findOneBy(['code' => $data[0]]);

            if (!$existingRecord) {
                $entity = new Fidele();
                $entity->setCode($data[0]);
                $entity->setNomfidele($data[1]);
                // Set other fields...
                $this->em->persist($entity);
            }
        }

        $this->em->flush();
    }
}