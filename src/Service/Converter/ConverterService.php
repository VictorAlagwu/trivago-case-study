<?php

namespace App\Service\Converter;

use App\Domain\Dto\Converter\ConverterResponseDto;
use App\Service\Converter\Handler\JsonHandler;
use App\Service\Converter\Handler\XmlHandler;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ConverterService
{
    private ParameterBagInterface $parameterBag;
    protected JsonHandler $jsonHandler;
    protected XmlHandler $xmlHandler;
    private LoggerInterface $logger;

    public function __construct(
        ParameterBagInterface $parameterBag,
        LoggerInterface $logger,
        JsonHandler $jsonHandler,
        XmlHandler $xmlHandler
    ) {
        $this->parameterBag = $parameterBag;

        $this->jsonHandler = $jsonHandler;
        $this->xmlHandler = $xmlHandler;
        $this->logger = $logger;
    }

    public function index(string $fileLocation): ConverterResponseDto
    {
        try {
            $inputDirectory = $this->parameterBag->get('kernel.project_dir') . '/var/in/' . $fileLocation;

            $fileExtension = $this->getFileExtension($inputDirectory);
            $checkExtension = $this->verifyExtension($fileExtension);
            $this->logger->info('File extension is' . $fileExtension);
            if (!$checkExtension) {
                throw new Exception('File not supported');
            }

            $fileName = 'hotel_' . $fileExtension;
            $outputDirectory = $this->parameterBag->get('kernel.project_dir') . '/var/out/' . $fileName . '.csv';
         
            if ($fileExtension === "json") {
                $response = $this->jsonHandler->parseFile($inputDirectory);
            }

            if ($fileExtension === "xml") {
                $response = $this->xmlHandler->parseFile($inputDirectory);
            }
            if ($fileExtension === "csv") {
                return new ConverterResponseDto(
                    true,
                    'File converted from ' . $fileExtension . ' to CSV',
                    $outputDirectory
                );
            }

            if (!$response->status) {
                return new ConverterResponseDto(false, $response->message, null);
            }

            $this->convertToCsv($fileExtension, $response->data, $outputDirectory);
            return new ConverterResponseDto(
                true,
                'File converted from ' . $fileExtension . ' to CSV',
                $outputDirectory
            );
        } catch (Exception $e) {
            return new ConverterResponseDto(false, $e->getMessage(), null);
        }
    }

    private function convertToCsv(string $extension, object $hotelDetails, string $filePath): string
    {
        $fp = fopen($filePath, 'w+');

        $header = ["name", "address", "stars", "contact", "phone", "uri"];
        fputcsv($fp, $header);

        //Sort Data

        //Filter

        //Group

        foreach ($hotelDetails as $hotel) {
            $hotelDetail = (array) $hotel;
            if (!$this->validateHotelDetails($hotelDetail)) {
                continue;
            }
            fputcsv($fp, $hotelDetail);
        }
        fclose($fp);

        return $filePath;
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

    // https://stackoverflow.com/questions/4147646/determine-if-utf-8-text-is-all-ascii
    public function validateHotelDetails(array $hotel): bool
    {
        if (!isset($hotel['name']) || !isset($hotel['stars']) || !isset($hotel['uri'])) {
            return false;
        }
        if (!mb_check_encoding($hotel['name'], 'ASCII')) {
            return false;
        }
        if (filter_var($hotel['uri'], FILTER_VALIDATE_URL) === false) {
            return false;
        }
        if ($hotel['stars'] < 0 || $hotel['stars'] > 5) {
            return false;
        }

        return true;
    }
}
