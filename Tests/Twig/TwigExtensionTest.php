<?php

namespace Lthrt\EntityBundle\Tests\Model;

use Lthrt\EntityBundle\Tests\LoadFixturesFromMetadata;
use Lthrt\EntityBundle\Tests\TestWithoutReboot;
use Lthrt\EntityBundle\Twig\JsonExtension;

class TwigExtensionTest extends TestWithoutReboot
{
    use LoadFixturesFromMetadata;

    public function testIsThisThingOn()
    {
        $this->assertTrue(true);
    }

    public function testTwigJson()
    {
        $twig = new JsonExtension();
        $this->assertEquals('lthrt.entity.twig_extension', $twig->getName());
        $this->assertEquals(['key' => 'value'], $twig->jsonDecode('{"key":"value"}'));
        $this->assertContains('<pre>{', $twig->jsonPretty('{"key":"value"}'));
        $this->assertContains('}</pre>', $twig->jsonPretty('{"key":"value"}'));
        $this->assertContains('"key":"value"', $twig->jsonPretty('{"key":"value"}'));
        $this->assertContains('    "key":"value"', $twig->jsonPretty('{"key":"value"}'));
        $this->assertContains('<pre>{', $twig->jsonPretty('{"key":["value1", "value2", "value3"]}'));
        $this->assertContains('}</pre>', $twig->jsonPretty('{"key":["value1", "value2", "value3"]}'));
        $this->assertContains('"key":[', $twig->jsonPretty('{"key":["value1", "value2", "value3"]}'));
        $this->assertContains('<pre>{', $twig->jsonPretty('{"key":["value1", "value2", "value3"]}'));
        $this->assertContains('"value1",', $twig->jsonPretty('{"key":["value1", "value2", "value3"]}'));
        $this->assertContains('        "value1",', $twig->jsonPretty('{"key":["value1", "value2", "value3"]}'));
        $this->assertContains('"value2",', $twig->jsonPretty('{"key":["value1", "value2", "value3"]}'));
        $this->assertContains('        "value2",', $twig->jsonPretty('{"key":["value1", "value2", "value3"]}'));
        $this->assertContains('"value3"', $twig->jsonPretty('{"key":["value1", "value2", "value3"]}'));
        $this->assertContains('        "value3"', $twig->jsonPretty('{"key":["value1", "value2", "value3"]}'));
        $this->assertContains(']', $twig->jsonPretty('{"key":["value1", "value2", "value3"]}'));
        $this->assertContains('    ]', $twig->jsonPretty('{"key":["value1", "value2", "value3"]}'));
        $this->assertCount(2, $twig->getFilters());
    }
}
