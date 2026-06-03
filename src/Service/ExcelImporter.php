<?php
namespace App\Service;

use App\Entity\Fidele;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\YourEntity;

class ExcelImporter
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function import($file)
    {
        $spreadsheet = IOFactory::load($file);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        foreach ($rows as $row) {
            $entity = $this->findOrCreateEntity($row);
            $this->entityManager->persist($entity);
        }

        $this->entityManager->flush();
    }

    private function findOrCreateEntity(array $data)
    {
        // Assuming the first column is the unique identifier
        $identifier = $data[0]; 
        $entity = $this->entityManager->getRepository(Fidele::class)->findOneBy(['code' => $identifier]);

        if (!$entity) {
            $entity = new Fidele();
            $entity->setCode($identifier);
            $entity->setTypefidele($data[1]);
            $entity->setNomfidele($data[2]);
        } else {
            // If the entity exists, update its properties
            $entity->setTypefidele($data[1]);
            $entity->setNomfidele($data[2]);
        }

        return $entity;
    }
}
