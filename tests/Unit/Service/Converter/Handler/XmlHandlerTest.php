<?php

namespace Tests\Unit\Service\Converter\Handler;

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

    public function testParseFile(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }
}
