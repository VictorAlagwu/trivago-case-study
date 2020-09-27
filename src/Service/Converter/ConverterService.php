<?php

namespace App\Service\Converter;

use App\Domain\Dto\Value\Converter\ConverterResponseDto;
use App\Helper\RandomToken\RandomToken;
use App\Service\Converter\Handler\JsonHandler;
use App\Service\Converter\Handler\XmlHandler;
use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ConverterService implements IConverterService
{
    private string $inputDirectory;
    private string $outputDirectory;
    protected JsonHandler $jsonHandler;
    protected XmlHandler $xmlHandler;

    public function __construct(
        ParameterBagInterface $parameterBag,
        JsonHandler $jsonHandler,
        XmlHandler $xmlHandler
    ) {
        $this->inputDirectory = $parameterBag->get('kernel.project_dir') . '/var/in/';
        $this->outputDirectory = $parameterBag->get('kernel.project_dir') . '/var/out/';

        $this->jsonHandler = $jsonHandler;
        $this->xmlHandler = $xmlHandler;
    }

    public function index(string $fileLocation): ConverterResponseDto
    {
        try {
            $path = $this->inputDirectory . $fileLocation;

            $fileExtension = $this->getFileExtension($path);
            $checkExtension = $this->verifyExtension($fileExtension);
            if (!$checkExtension) {
                throw new Exception('File not supported');
            }

            if ($fileExtension === "json") {
                $response = $this->jsonHandler->parseFile($path);
            } elseif ($fileExtension === "xml") {
                $response = $this->xmlHandler->parseFile($path);
            } else {
                throw new Exception('Invalid file extension');
            }

            if (!$response->status) {
                return new ConverterResponseDto(false, $response->message);
            }
            $this->jsonToCsvConverter($response->data);
            return new ConverterResponseDto(true, 'File converted from ' . $fileExtension . ' to CSV');
        } catch (Exception $e) {
            return new ConverterResponseDto(false, $e->getMessage());
        }
    }

    protected function jsonToCsvConverter($jsonObj)
    {
        $fileName = 'hotel-' . RandomToken::getToken(5);
        $filePath = $this->outputDirectory . '' . $fileName . '.csv';
        
        $fp = fopen($filePath, 'w+');

        //Object Validation
        $validatedData = $this->validateData($jsonObj);

        foreach ($validatedData as $field) {
            fputcsv($fp, (array) $field);
        }
        fclose($fp);
        return;
    }

    protected function verifyExtension(string $extension): bool
    {
        $allowedExtensions = [
            "csv", "json", "xml"
        ];

        if (!in_array($extension, $allowedExtensions)) {
            return false;
        };
        return true;
    }

    protected function getFileExtension(string $path): string
    {
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        return $ext;
    }
   

    public function validateData($validatedData)
    {
        // $newData = [];
        // foreach($validatedData as $data) {
        //     $newData->name = 'Victor';
        // }
        return $validatedData;
    }

    public function convertToCsv($data)
    {
    }
}
