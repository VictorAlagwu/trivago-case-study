<?php

namespace App\Service\Converter\Handler;

use App\Domain\Dto\Converter\ParseFileDto;
use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class JsonHandler implements HandlerInterface
{
    private string $inputDirectory;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->inputDirectory = $parameterBag->get('kernel.project_dir') . 'var/in/';
    }

    public function parseFile(string $path): ParseFileDto
    {
        try {
            $file = file_get_contents($path, FILE_USE_INCLUDE_PATH);
            $jsonObj = json_decode($file);

            return new ParseFileDto(true, (object) $jsonObj, 'File parsed');
        } catch (Exception $e) {
            return new ParseFileDto(false, null, $e->getMessage());
        }
    }
}
