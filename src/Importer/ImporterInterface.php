<?php

namespace App\Importer;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface ImporterInterface
{
    public function import(UploadedFile $file): array;
}