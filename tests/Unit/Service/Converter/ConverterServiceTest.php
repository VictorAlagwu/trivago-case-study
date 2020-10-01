<?php

namespace Tests\Unit\Service\Converter;

use App\Domain\Dto\Converter\ConverterRequestDto;
use App\Domain\Dto\Converter\ConverterResponseDto;
use App\Domain\Dto\Converter\ParseFileDto;
use App\Service\Converter\ConverterService;
use App\Service\Converter\Handler\JsonHandler;
use App\Service\Converter\Handler\XmlHandler;
use Mockery;
use Mockery\Mock;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use stdClass;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * Class ConverterServiceTest.
 *
 * @covers \App\Service\Converter\ConverterService
 */
class ConverterServiceTest extends TestCase
{
    /**
     * @var ConverterService
     */
    protected $converterService;

    /**
     * @var ParameterBagInterface|Mock
     */
    protected $parameterBag;

    /**
     * @var LoggerInterface|Mock
     */
    protected $logger;

    /**
     * @var JsonHandler|Mock
     */
    protected $jsonHandler;

    /**
     * @var XmlHandler|Mock
     */
    protected $xmlHandler;

    /**
     * @var hotelDetails|array
     */
    protected $hotelDetails;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->parameterBag = Mockery::mock(ParameterBagInterface::class);
        $this->logger = Mockery::mock(LoggerInterface::class);
        $this->jsonHandler = Mockery::mock(JsonHandler::class);
        $this->xmlHandler = Mockery::mock(XmlHandler::class);
        $this->converterService = new ConverterService(
            $this->parameterBag,
            $this->logger,
            $this->jsonHandler,
            $this->xmlHandler
        );
        $this->hotelDetails = [
            [
                'name' => "The Zimmer",
                'address' => "Spießgasse 314, 90061 Beilngries",
                'stars' => "2",
                'contact' => "Arlene Hornig",
                'phone' => "07638 017517",
                'uri' => "http://premier.de/about/"
            ],
            [
                'name' => "The 网络",
                'address' =>  "Spießgasse 314, 90061 Beilngries",
                'stars' => "5",
                'contact' =>  "Arlene Hornig",
                'phone' => "07638 017517",
                'uri' => "httdpd://premier.de/about/"
            ], [
                'name' => "The cLE",
                'address' => "Spießgasse 314, 90061 Beilngries",
                'stars' => "3",
                'contact' => "Arlene Hornig",
                'phone' => "07638 017517",
                'uri' => "http://premier.de/about/"

            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->converterService);
        unset($this->parameterBag);
        unset($this->logger);
        unset($this->jsonHandler);
        unset($this->xmlHandler);
    }


    public function testIndexWhenFileExtensionIsInvalid(): void
    {
        $convertedRequestDto = new ConverterRequestDto(
            'test.js',
            null,
            null,
            null,
            null
        );
        $this->parameterBag->shouldReceive('get')->once()->andReturn('address');
        pathinfo('test.js', PATHINFO_EXTENSION);
        $this->logger->shouldReceive('info')->once()->andReturn();
        $this->logger->shouldReceive('warning')->once()->andReturn();
        $dto = new ConverterResponseDto(false, 'File not supported', null);

        $result = $this->converterService->index($convertedRequestDto);

        $this->assertEquals($dto, $result);
    }

    public function testIndexWhenFileExtensionIsJsonAndFileIsNotConverted(): void
    {
        $handlerDto = new ParseFileDto(false, new stdClass(), 'not converted');
        $convertedRequestDto = new ConverterRequestDto(
            'test.json',
            null,
            null,
            null,
            null
        );
        $this->parameterBag->shouldReceive('get')->once()->andReturn('address');
        pathinfo('test.json', PATHINFO_EXTENSION);
        $this->logger->shouldReceive('info')->once()->andReturn();
        $this->parameterBag->shouldReceive('get')->once()->andReturn('address');

        $this->jsonHandler->shouldReceive('parseFile')->once()->andReturn($handlerDto);

        $dto = new ConverterResponseDto(false, 'test', null);

        $result = $this->converterService->index($convertedRequestDto);

        $this->assertSame($dto->status, $result->status);
    }


    public function testIndexWhenFileExtensionIsJsonAndFileIsConvertedAndSort(): void
    {
        $handlerDto = new ParseFileDto(true, (object) $this->hotelDetails, 'converted');

        $convertedRequestDto = new ConverterRequestDto(
            'test.json',
            'name',
            null,
            null,
            null
        );

        $this->parameterBag->shouldReceive('get')->once()->andReturn();
        pathinfo('test.json', PATHINFO_EXTENSION);
        $this->logger->shouldReceive('info')->once()->andReturn();
        $this->parameterBag->shouldReceive('get')->once()->andReturn(\dirname(__DIR__, 3));

        $this->jsonHandler->shouldReceive('parseFile')->once()->andReturn($handlerDto);
        
       
        $dto = new ConverterResponseDto(true, 'test', 'address');
        $this->logger->shouldReceive('warning')->once()->andReturn();

        $result = $this->converterService->index($convertedRequestDto);
      
        $this->assertSame($dto->status, $result->status);
    }

    public function testIndexWhenFileExtensionIsJsonAndFileIsConvertedAndGroup(): void
    {
        $handlerDto = new ParseFileDto(true, (object) $this->hotelDetails, 'converted');

        $convertedRequestDto = new ConverterRequestDto(
            'test.json',
            null,
            null,
            null,
            'name'
        );

        $this->parameterBag->shouldReceive('get')->once()->andReturn();
        pathinfo('test.json', PATHINFO_EXTENSION);
        $this->logger->shouldReceive('info')->once()->andReturn();
        $this->parameterBag->shouldReceive('get')->once()->andReturn(\dirname(__DIR__, 3));

        $this->jsonHandler->shouldReceive('parseFile')->once()->andReturn($handlerDto);
        
       
        $dto = new ConverterResponseDto(true, 'test', 'address');
        $this->logger->shouldReceive('warning')->once()->andReturn();

        $result = $this->converterService->index($convertedRequestDto);
      
        $this->assertSame($dto->status, $result->status);
    }
    public function testIndexWhenFileExtensionIsJsonAndFileIsConvertedAndFiltered(): void
    {
        $handlerDto = new ParseFileDto(true, (object) $this->hotelDetails, 'converted');

        $convertedRequestDto = new ConverterRequestDto(
            'test.json',
            null,
            'stars',
            '3',
            null
        );

        $this->parameterBag->shouldReceive('get')->once()->andReturn();
        pathinfo('test.json', PATHINFO_EXTENSION);
        $this->logger->shouldReceive('info')->once()->andReturn();
        $this->parameterBag->shouldReceive('get')->once()->andReturn(\dirname(__DIR__, 3));

        $this->jsonHandler->shouldReceive('parseFile')->once()->andReturn($handlerDto);
        
       
        $dto = new ConverterResponseDto(true, 'test', 'address');
        $this->logger->shouldReceive('warning')->once()->andReturn();

        $result = $this->converterService->index($convertedRequestDto);
      
        $this->assertSame($dto->status, $result->status);
    }


    public function testIndexWhenFileExtensionIsXmlAndFileIsNotConverted(): void
    {
        $handlerDto = new ParseFileDto(false, new stdClass(), 'not converted');
        $convertedRequestDto = new ConverterRequestDto(
            'test.xml',
            null,
            null,
            null,
            null
        );
        $this->parameterBag->shouldReceive('get')->once()->andReturn('address');
        pathinfo('test.xml', PATHINFO_EXTENSION);
        $this->logger->shouldReceive('info')->once()->andReturn();
        $this->parameterBag->shouldReceive('get')->once()->andReturn('address');

        $this->xmlHandler->shouldReceive('parseFile')->once()->andReturn($handlerDto);

        $dto = new ConverterResponseDto(false, 'test', null);


        $result = $this->converterService->index($convertedRequestDto);

        $this->assertSame($dto->status, $result->status);
    }

    public function testIndexWhenFileExtensionIsCsv(): void
    {
        $convertedRequestDto = new ConverterRequestDto(
            'test.csv',
            null,
            null,
            null,
            null
        );
        $this->parameterBag->shouldReceive('get')->once()->andReturn('address');
        pathinfo('test.csv', PATHINFO_EXTENSION);
        $this->logger->shouldReceive('info')->once()->andReturn();
        $this->parameterBag->shouldReceive('get')->once()->andReturn('address');

        $dto = new ConverterResponseDto(true, 'test', null);

        $result = $this->converterService->index($convertedRequestDto);

        $this->assertSame($dto->status, $result->status);
    }

    public function testValidateHotelDetailsWhenDataIsValid(): void
    {
        $hotel = array(
            "name" => "Name",
            "address" => "Address",
            "stars" => "3",
            "uri" => "http://www.paucek.com/search.html"
        );
        $this->logger->shouldReceive('info')->once()->andReturn();

        $result = $this->converterService->validateHotelDetails($hotel);
        $this->assertTrue($result);
    }

    public function testValidateHotelDetailsWhenNameHasAsciiCharacters(): void
    {
        $hotel = array(
            "name" => "网络",
            "address" => "Address",
            "stars" => "3",
            "uri" => "http://www.paucek.com/search.html"
        );
        $this->logger->shouldReceive('warning')->once()->andReturn();

        $result = $this->converterService->validateHotelDetails($hotel);
        $this->assertFalse($result);
    }
    public function testValidateHotelDetailsWhenUriIsInvalid(): void
    {
        $hotel = array(
            "name" => "Test",
            "address" => "63847 Lowe Knoll, East Maxine, WA 97030-4876",
            "stars" => "3",
            "uri" => "wew.paucek.com/search.ht"
        );
        $this->logger->shouldReceive('warning')->once()->andReturn();
        $result = $this->converterService->validateHotelDetails($hotel);
        $this->assertFalse($result);
    }
    public function testValidateHotelDetailsWhenStarIsInvalid(): void
    {
        $hotel = array(
            "name" => "Test",
            "address" => "63847 Lowe Knoll, East Maxine, WA 97030-4876",
            "stars" => "6",
            "uri" => "http://www.paucek.com/search.html"
        );
        $this->logger->shouldReceive('warning')->once()->andReturn();

        $result = $this->converterService->validateHotelDetails($hotel);
        $this->assertFalse($result);
    }
    public function testValidateHotelDetailsWhenEmptyName(): void
    {
        $hotel = array(
            "address" => "63847 Lowe Knoll, East Maxine, WA 97030-4876",
            "stars" => "6",
            "uri" => "http://www.paucek.com/search.html"
        );
        $this->logger->shouldReceive('warning')->once()->andReturn();
        $result = $this->converterService->validateHotelDetails($hotel);
        $this->assertFalse($result);
    }
}
