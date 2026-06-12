<?php
namespace App\Service;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;

class QrCodeGenerator
{
    public function generate(string $url, string $filename): void
    {
       $result = Builder::create()
    ->writer(new PngWriter())
    ->data($url)
    ->size(400)
    ->margin(10)
    ->build();

        $result->saveToFile($filename);
        }
}