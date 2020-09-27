<?php

namespace App\Service\Converter\Handler;

use App\Domain\Dto\Request\Converter\ParseFileDto;
use Exception;
use SimpleXMLReader;

class XmlHandler
{
    protected $parsedFile;

    public function parseFile(string $path): ParseFileDto
    {
        try {
            $reader = new SimpleXMLReader();
            $reader->open($path);
            $reader->registerCallback("hotels", function ($reader) {
                $element = $reader->expandSimpleXml();
                $this->parsedFile = $element;
                return true;
            });
            $reader->parse();
            $reader->close();
            return new ParseFileDto(true, $this->parsedFile, 'File parsed');
        } catch (Exception $e) {
            return new ParseFileDto(false, [], $e->getMessage());
        }
    }
}
