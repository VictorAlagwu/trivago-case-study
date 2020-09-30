<?php

namespace App\Service\Converter\Handler;

use App\Domain\Dto\Converter\ParseFileDto;
use Exception;
use SimpleXMLReader;

class XmlHandler implements HandlerInterface
{
    protected $parsedFile;

    public function parseFile(string $path): ParseFileDto
    {
        try {
            $reader = new SimpleXMLReader();
            $reader->open($path);
            $reader->registerCallback("hotels", function ($reader) {
                $element = $reader->expandSimpleXml();
                $this->parsedFile = $element->children();
                return true;
            });
            $reader->parse();
            $reader->close();
            $result = json_decode(json_encode($this->parsedFile), true);
 
            return new ParseFileDto(true, (object) $result['hotel'], 'File parsed');
        } catch (Exception $e) {
            return new ParseFileDto(false, null, $e->getMessage());
        }
    }
}
