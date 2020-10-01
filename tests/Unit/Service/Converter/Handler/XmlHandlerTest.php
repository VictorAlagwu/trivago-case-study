<?php

namespace Tests\Unit\Service\Converter\Handler;

use App\Domain\Dto\Converter\ParseFileDto;
use App\Service\Converter\Handler\XmlHandler;
use PHPUnit\Framework\TestCase;

/**
 * Class XmlHandlerTest.
 *
 * @covers \App\Service\Converter\Handler\XmlHandler
 */
class XmlHandlerTest extends TestCase
{
    /**
     * @var XmlHandler
     */
    protected $xmlHandler;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->xmlHandler = new XmlHandler();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->xmlHandler);
    }

    public function testParseFileValid(): void
    {
        $hotel =  [
            'name' => "The cLE",
            'address' => "Spießgasse 314, 90061 Beilngries",
            'stars' => "3",
            'contact' => "Arlene Hornig",
            'phone' => "07638 017517",
            'uri' => "http://premier.de/about/"

        ];
        $dto = new ParseFileDto(true, (object) $hotel, 'File parsed');
        $path = \dirname(__DIR__, 4) . '/var/in/hotelTest.xml';
        $result = $this->xmlHandler->parseFile($path);

        $this->assertSame($dto->status, $result->status);
    }
    public function testParseFileNotValid(): void
    {
        $dto = new ParseFileDto(false, null, 'File not parsed');

        $result = $this->xmlHandler->parseFile('dsd');

        $this->assertSame($dto->status, $result->status);
    }
}
