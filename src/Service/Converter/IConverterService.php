<?php

namespace App\Service\Converter;

use App\Domain\Dto\Value\Converter\ConverterResponseDto;

interface IConverterService
{
    public function index(string $fileLocation): ConverterResponseDto;

    public function validateData($data): array;

    public function convertToCsv($data);
}
