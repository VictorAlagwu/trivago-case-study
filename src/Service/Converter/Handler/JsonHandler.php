<?php

namespace App\Service\Converter\Handler;

use App\Domain\Dto\Converter\ParseFileDto;
use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * Class JsonHandler
 * @package App\Service\Converter\Handler
 */
class JsonHandler implements HandlerInterface
{
    /**
     * @param string $path
     * @return ParseFileDto
     */
    public function parseFile(string $path): ParseFileDto
    {
        try {
            $file = file_get_contents($path, FILE_USE_INCLUDE_PATH);
            $jsonObj = json_decode($file, true);
           
            return new ParseFileDto(true, (object) $jsonObj, 'File parsed');
        } catch (Exception $e) {
            return new ParseFileDto(false, null, $e->getMessage());
        }
    }
}
