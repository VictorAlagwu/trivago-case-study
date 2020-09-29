<?php

namespace Tests\Unit\Service\Converter\Handler;

use App\Service\Converter\Handler\JsonHandler;
use Mockery;
use Mockery\Mock;
use PHPUnit\Framework\TestCase;
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
     * @var ParameterBagInterface|Mock
     */
    protected $parameterBag;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->parameterBag = Mockery::mock(ParameterBagInterface::class);
        $this->jsonHandler = new JsonHandler($this->parameterBag);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->jsonHandler);
        unset($this->parameterBag);
    }

    public function testParseFile(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }
}
