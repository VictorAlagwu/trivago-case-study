<?php

namespace App\Service\Converter;

use App\Domain\Dto\Converter\ConverterRequestDto;
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

    public function index(ConverterRequestDto $dto): ConverterResponseDto
    {
        try {
            $inputDirectory = $this->parameterBag->get('kernel.project_dir') . '/var/in/' . $dto->name;

            $fileExtension = $this->getFileExtension($inputDirectory);
            $checkExtension = $this->verifyExtension($fileExtension);
            $this->logger->info('File extension is ' . $fileExtension);
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

            $this->convertToCsv((array) $response->data, $outputDirectory, $dto);
            return new ConverterResponseDto(
                true,
                'File converted from ' . $fileExtension . ' to CSV',
                $outputDirectory
            );
        } catch (Exception $e) {
            return new ConverterResponseDto(false, $e->getMessage(), null);
        }
    }

    private function convertToCsv(
        array $hotelDetails,
        string $filePath,
        ConverterRequestDto $dto
    ): string {
        $fp = fopen($filePath, 'w+');

        $header = ["name", "address", "stars", "contact", "phone", "uri"];
        fputcsv($fp, $header);

        //Sort Data
        if ($dto->sortBy) {
            $hotelDetails = $this->sortHotelDetails($dto->sortBy, $hotelDetails);
        }
        //Filter
        if ($dto->filterBy) {
            $hotelDetails = $this->filterHotelDetails($dto->filterBy, $dto->filterValue, $hotelDetails);
        }
        //Group
        if ($dto->groupBy) {
            $hotelDetails = $this->groupHotelDetails($dto->groupBy, $hotelDetails);
        }

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


    private function sortHotelDetails(string $sortBy, array $hotelDetails)
    {
        $this->logger->info('Sorting hotel details by ' . $sortBy);

        $allowedParams = [
            "name", "stars", "address", "phone", "uri", "contact"
        ];
        usort($hotelDetails, function ($a, $b) use ($sortBy, $allowedParams) {
            if (in_array($sortBy, $allowedParams)) {
                return strcmp($a[$sortBy], $b[$sortBy]);
            }
        });

        return $hotelDetails;
    }

    private function filterHotelDetails(string $filterBy, string $filterValue, array $hotelDetails)
    {
        $this->logger->info('Filteing hotel details by ' . $filterBy . ' with value: ' . $filterValue);

        if (!isset($filterValue)) {
            return $hotelDetails;
        }
        return array_filter(
            $hotelDetails,
            function ($key) use ($filterBy, $filterValue) {

                return $key[$filterBy] === $filterValue;
            }
        );
    }

    private function groupHotelDetails(string $groupBy, array $hotelDetails)
    {
        $this->logger->info('Grouping hotel details by ' . $groupBy);

        $return = array();
        foreach ($hotelDetails as $hotel) {
            $return[$hotel[$groupBy]] = $hotel;
        }
        return $return;
    }
    protected function verifyExtension(string $extension): bool
    {
        $allowedExtensions = [
            "csv", "json", "xml"
        ];

        if (!in_array($extension, $allowedExtensions)) {
            $this->logger->warning('Unsupported extension', [
                'current_extension' => $extension, 'allowed_extensin' => $allowedExtensions
            ]);
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
            $this->logger->warning('No name,stars or uri index in this array', $hotel);
            return false;
        }
        if (!mb_check_encoding($hotel['name'], 'ASCII')) {
            $this->logger->warning('ASCII characters not allowed', $hotel);
            return false;
        }
        if (filter_var($hotel['uri'], FILTER_VALIDATE_URL) === false) {
            $this->logger->warning('Invalid URL', $hotel);
            return false;
        }
        if ($hotel['stars'] < 0 || $hotel['stars'] > 5) {
            $this->logger->warning('Only values between 0 and 5 are allowed', $hotel);
            return false;
        }
        $this->logger->info('Array passed all validations', $hotel);
        return true;
    }
}
