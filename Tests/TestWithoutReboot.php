<?php

namespace Lthrt\EntityBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\Container;

class TestWithoutReboot extends WebTestCase
{
    /**
     * @var Symfony\Bundle\FrameworkBundle\Client
     */
    protected $client;

    /**
     * @var Symfony\Component\DependencyInjection\Container
     */
    protected $container;

    public function testIsThisThingOn()
    {
        $this->assertTrue(true);
    }

    public function setUp()
    {
        static::ensureKernelShutdown();
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $this->client = static::$kernel->getContainer()->get('test.client');

        // without this, the client destroys in memory database after every request.
        $this->client->disableReboot();
    }

    public function tearDown()
    {
        static::$kernel->shutdown();
        parent::tearDown();
    }

    public static function setUpBeforeClass() {}
    public static function tearDownAfterClass() {}
}
