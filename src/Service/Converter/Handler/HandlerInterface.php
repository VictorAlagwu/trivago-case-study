<?php

namespace App\Service\Converter\Handler;

use App\Domain\Dto\Converter\ParseFileDto;

interface HandlerInterface
{
    public function parseFile(string $path): ParseFileDto;
}
