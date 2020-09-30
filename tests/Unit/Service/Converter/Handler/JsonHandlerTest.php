<?php

namespace Tests\Unit\Service\Converter\Handler;

use App\Domain\Dto\Converter\ParseFileDto;
use App\Service\Converter\Handler\JsonHandler;
use Mockery;
use Mockery\Mock;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * Class JsonHandlerTest.
 *
 * @covers \App\Service\Converter\Handler\JsonHandler
 */
class JsonHandlerTest extends TestCase
{
    /**
     * @var JsonHandler
     */
    protected $jsonHandler;


    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->jsonHandler = new JsonHandler();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->jsonHandler);
    }

    public function testParseFileValid(): void
    {
        $hotel = new stdClass();
        $hotel->name = 'test';
        $hotel->address = '21, Address';
        $hotel->stars = 3;
        $hotel->contact = 'Tester';
        $hotel->phone = '232323';
        $hotel->uri = 'https://google.com';

        $dto = new ParseFileDto(true, (object) $hotel, 'File parsed');
        $path = \dirname(__DIR__, 4) . '/var/in/hotelTest.json';
        $result = $this->jsonHandler->parseFile($path);
     
        $this->assertSame($dto->status, $result->status);
    }
    public function testParseFileNotValid(): void
    {
        $dto = new ParseFileDto(false, null, 'File not parsed');
     
        $result = $this->jsonHandler->parseFile('dsd');
     
        $this->assertSame($dto->status, $result->status);
    }
}
