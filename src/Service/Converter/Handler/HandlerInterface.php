<?php

namespace App\Service\Converter\Handler;

use App\Domain\Dto\Converter\ParseFileDto;

/**
 * Interface HandlerInterface
 * @package App\Service\Converter\Handler
 */
interface HandlerInterface
{
    /**
     * @param string $path
     * @return ParseFileDto
     */
    public function parseFile(string $path): ParseFileDto;
}
