<?php

namespace App\Service;

interface IConverterService
{
    public function getFile($fileLocation);

    public function validateData($data);

    public function convertToCsv($data);
}
