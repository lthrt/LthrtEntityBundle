<?php

namespace Lthrt\EntityBundle\Tests\Model;

use Lthrt\EntityBundle\Model\EntitySerializer;
use Lthrt\EntityBundle\Tests\Entity\GenericJsonEntity;
use Lthrt\EntityBundle\Tests\Entity\TestEntity1;
use Lthrt\EntityBundle\Tests\Entity\TestEntity3;
use Lthrt\EntityBundle\Tests\Entity\TestEntity9;
use Lthrt\EntityBundle\Tests\LoadFixturesFromMetadata;
use Lthrt\EntityBundle\Tests\TestWithoutReboot;

class EntitySerializerTest extends TestWithoutReboot
{
    use LoadFixturesFromMetadata;

    public function setUp()
    {
        parent::setup();
    }

    public function testIsThisThingOn()
    {
        $this->assertTrue(true);
    }

    public function testSerializerAndNoLog()
    {
        $reader     = static::$kernel->getContainer()->get('annotation_reader');
        $em         = static::$kernel->getContainer()->get('doctrine')->getManager();
        $serializer = new EntitySerializer($em, $reader, ['fluff']);
        $entity     = new TestEntity1();
        $serializer->configure($entity);
        $result = $serializer->serialize($entity);
        $this->assertNotContains('fluff', $result);
        $this->assertNotContains('extraFluff', $result);
        $this->assertContains('updated', $result);
        $this->assertContains('id', $result);
        $this->assertContains('name', $result);

        $entity          = new TestEntity1();
        $now             = new \DateTime();
        $entity->updated = $now;
        $serializer->configure($entity);
        $result = $serializer->serialize($entity);
        $this->assertNotContains('fluff', $result);
        $this->assertNotContains('extraFluff', $result);
        $this->assertContains('updated', $result);
        $this->assertContains('id', $result);
        $this->assertContains('name', $result);
        $this->assertContains($now->format('Y-m-d H:i:s T'), $result);

        $result = $serializer->normalize($entity);
        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('notFluff', $result);
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('updated', $result);

        $anotherEntity = new TestEntity1();
        $serializer->configure([$entity, $anotherEntity]);
        $result = $serializer->serialize($entity);
        $this->assertNotContains('fluff', $result);
        $this->assertNotContains('extraFluff', $result);
        $this->assertContains('updated', $result);
        $this->assertContains('id', $result);
        $this->assertContains('name', $result);

        $result = $serializer->serialize([$entity, $anotherEntity]);
        $this->assertNotContains('fluff', $result);
        $this->assertNotContains('extraFluff', $result);
        $this->assertContains('updated', $result);
        $this->assertContains('id', $result);
        $this->assertContains('name', $result);

        $result = $serializer->normalize([$entity, $anotherEntity]);
        $this->assertCount(2, $result);
        $this->assertArrayHasKey('name', $result[0]);
        $this->assertArrayHasKey('name', $result[1]);
        $this->assertArrayHasKey('id', $result[0]);
        $this->assertArrayHasKey('id', $result[1]);
        $this->assertArrayHasKey('notFluff', $result[0]);
        $this->assertArrayHasKey('notFluff', $result[1]);

        $entity       = new TestEntity3();
        $entity->name = 'testValue';
        $serializer->configure($entity);
        $result = $serializer->serialize($entity);
        $this->assertContains('name', $result);
        $this->assertContains('testValue', $result);

        $entity = new TestEntity9();
        $serializer->configure($entity);
        $result = $serializer->serialize($entity);
        $this->assertContains('badeight', $result);
        $this->assertContains('eight', $result);
        $this->assertContains('testEntity10', $result);

        $entity = new GenericJsonEntity();
        $serializer->configure($entity);
        $result = $serializer->serialize($entity);
        $this->assertContains('name', $result);
        $result = $serializer->normalize($entity);
        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('fluff', $result);
        $this->assertArrayHasKey('id', $result);

        $anotherEntity = new GenericJsonEntity();
        $serializer->configure($entity);
        $result = $serializer->serialize([$entity, $anotherEntity]);
        $this->assertContains('name', $result);
        $this->assertContains('fluff', $result);
        $this->assertContains('id', $result);

        $result = $serializer->serialize([]);
        $this->assertContains('null', $result);

        $result = $serializer->normalize([$entity, $anotherEntity]);
        $this->assertCount(2, $result);
        $this->assertArrayHasKey('name', $result[0]);
        $this->assertArrayHasKey('name', $result[1]);
        $this->assertArrayHasKey('id', $result[0]);
        $this->assertArrayHasKey('id', $result[1]);
        $this->assertArrayHasKey('fluff', $result[0]);
        $this->assertArrayHasKey('fluff', $result[1]);

        $result = $serializer->normalize([]);
        $this->assertContains('null', $result);

        $json   = '{"name":"testValue", "id":"9","updated":"' . $now->format('Y-m-d H:i:s T') . '"}';
        $result = $serializer->deserialize($json, TestEntity1::class);
        $this->assertObjectHasAttribute('name', $result);
        $this->assertObjectHasAttribute('fluff', $result);
        $this->assertObjectHasAttribute('id', $result);
        $this->assertObjectHasAttribute('extraFluff', $result);
        $this->assertObjectHasAttribute('notFluff', $result);
        $this->assertNull($result->extraFluff);
        $this->assertNull($result->notFluff);
        $this->assertNull($result->fluff);
        $this->assertNotNull($result->id);
        $this->assertNotNull($result->name);
        $this->assertNotNull($result->updated);
        $this->assertEquals($result->updated, $now->format('Y-m-d H:i:s T'));
        $this->assertEquals($result->id, 9);
        $this->assertEquals($result->name, 'testValue');
    }
}
