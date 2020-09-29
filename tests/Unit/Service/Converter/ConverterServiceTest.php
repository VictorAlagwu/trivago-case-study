<?php

namespace Tests\Unit\Service\Converter;

use App\Service\Converter\ConverterService;
use App\Service\Converter\Handler\JsonHandler;
use App\Service\Converter\Handler\XmlHandler;
use Mockery;
use Mockery\Mock;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
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
        $this->converterService = new ConverterService($this->parameterBag, $this->logger, $this->jsonHandler, $this->xmlHandler);
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

    public function testIndex(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }

    public function testValidateHotelDetails(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }
}
