<?php

namespace Tests\Unit\Service\Converter;

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
        $this->parameterBag->shouldReceive('get')->once()->andReturn('address');
        pathinfo('test.js', PATHINFO_EXTENSION);
        $this->logger->shouldReceive('info')->once()->andReturn();
        $dto = new ConverterResponseDto(false, 'File not supported', null);

        $result = $this->converterService->index('test.js');

        $this->assertEquals($dto, $result);
    }

    public function testIndexWhenFileExtensionIsJsonAndFileIsNotConverted(): void
    {
        $handlerDto = new ParseFileDto(false, new stdClass(), 'not converted');

        $this->parameterBag->shouldReceive('get')->once()->andReturn('address');
        pathinfo('test.json', PATHINFO_EXTENSION);
        $this->logger->shouldReceive('info')->once()->andReturn();
        $this->parameterBag->shouldReceive('get')->once()->andReturn('address');

        $this->jsonHandler->shouldReceive('parseFile')->once()->andReturn($handlerDto);

        $dto = new ConverterResponseDto(false, 'test', null);

        $result = $this->converterService->index('test.json');

        $this->assertSame($dto->status, $result->status);
    }
    public function testIndexWhenFileExtensionIsJsonAndFileIsConverted(): void
    {
        $hotel = new stdClass();
        $hotel->name = 'test';
        $hotel->address = '21, Address';
        $hotel->stars = 3;
        $hotel->contact = 'Tester';
        $hotel->phone = '232323';
        $hotel->uri = 'https://google.com';

        $handlerDto = new ParseFileDto(true, $hotel, 'converted');

        $this->parameterBag->shouldReceive('get')->once()->andReturn();
        pathinfo('test.json', PATHINFO_EXTENSION);
        $this->logger->shouldReceive('info')->once()->andReturn();
        $this->parameterBag->shouldReceive('get')->once()->andReturn(\dirname(__DIR__, 3));

        $this->jsonHandler->shouldReceive('parseFile')->once()->andReturn($handlerDto);

        $dto = new ConverterResponseDto(true, 'test', 'address');


        $result = $this->converterService->index('test.json');
  
        $this->assertSame($dto->status, $result->status);
    }

    public function testIndexWhenFileExtensionIsXmlAndFileIsNotConverted(): void
    {
        $handlerDto = new ParseFileDto(false, new stdClass(), 'not converted');

        $this->parameterBag->shouldReceive('get')->once()->andReturn('address');
        pathinfo('test.xml', PATHINFO_EXTENSION);
        $this->logger->shouldReceive('info')->once()->andReturn();
        $this->parameterBag->shouldReceive('get')->once()->andReturn('address');

        $this->xmlHandler->shouldReceive('parseFile')->once()->andReturn($handlerDto);

        $dto = new ConverterResponseDto(false, 'test', null);


        $result = $this->converterService->index('test.xml');

        $this->assertSame($dto->status, $result->status);
    }

    public function testIndexWhenFileExtensionIsCsv(): void
    {

        $this->parameterBag->shouldReceive('get')->once()->andReturn('address');
        pathinfo('test.csv', PATHINFO_EXTENSION);
        $this->logger->shouldReceive('info')->once()->andReturn();
        $this->parameterBag->shouldReceive('get')->once()->andReturn('address');

        $dto = new ConverterResponseDto(true, 'test', null);

        $result = $this->converterService->index('test.csv');

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

        $result = $this->converterService->validateHotelDetails($hotel);
        $this->assertFalse($result);
    }
}
