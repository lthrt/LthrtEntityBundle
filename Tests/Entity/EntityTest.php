<?php

namespace Lthrt\EntityBundle\Tests\Entity;

use Lthrt\EntityBundle\Entity\DoctrineEntityTrait;
use Lthrt\EntityBundle\Entity\DoctrineGetSetTrait;
use Lthrt\EntityBundle\Entity\GetSetTrait;
use Lthrt\EntityBundle\Entity\IdTrait;
use Lthrt\EntityBundle\Tests\LoadFixturesFromMetadata;
use Lthrt\EntityBundle\Tests\TestWithoutReboot;

class EntityTest extends TestWithoutReboot
{
    use LoadFixturesFromMetadata;

    public function testIsThisThingOn()
    {
        $this->assertTrue(true);
    }

    public function testGetSet()
    {
        $entity        = new TestEntity5();
        $entity->field = "Test";
        $this->assertContains(DoctrineEntityTrait::class, class_uses($entity));
        $this->assertEquals("Accessor", $entity->field);

        $entity->otherfield = "Test";
        $this->assertEquals("Test", $entity->otherfield);
        $this->assertEquals("Test", $entity->getOtherfield());
        $this->assertEquals($entity, $entity->setOtherfield("Modified"));
        $this->assertEquals("Modified", $entity->getOtherfield());

        try {
            $entity->_field;
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof \BadMethodCallException);
            $this->assertContains('Bad __get(): property protected by leading underscore', $e->getMessage());
        }

        try {
            $entity->_field = "Fail";
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof \BadMethodCallException);
            $this->assertContains('Bad __set(): property protected by leading underscore', $e->getMessage());
        }

        try {
            $entity->nonfield;
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof \BadMethodCallException);
            $this->assertContains('Bad __get(): No property named', $e->getMessage());
        }

        try {
            $entity->nonfield = "Fail";
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof \BadMethodCallException);
            $this->assertContains('Bad __set(): No property named', $e->getMessage());
        }

        try {
            $entity->date = 'date';
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof \BadMethodCallException);
            $this->assertContains('Bad __set(): invalid date string', $e->getMessage());
            $this->assertContains('passed to property', $e->getMessage());
        }

        $now          = new \DateTime();
        $entity->date = $now;
        $this->assertEquals($entity->date, $now);

        $this->assertEquals($entity->setId(), $entity);
        $this->assertEquals("", $entity->__toString());

        $em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        static::generateSchema($em);
        $em->persist($entity);

        $clone = clone $entity;
        $this->isNull($clone->id);

        $em->flush();

        $clone = clone $entity;
        $this->isNull($clone->id);

        $entity        = new TestEntity6();
        $entity->field = "Test";
        $this->assertContains(GetSetTrait::class, class_uses($entity));
        $this->assertEquals("Accessor", $entity->field);

        $entity->otherfield = "Test";
        $this->assertEquals("Test", $entity->otherfield);
        $this->assertEquals("Test", $entity->getOtherfield());
        $this->assertEquals("Test", $entity->otherfield());
        $this->assertEquals($entity, $entity->setOtherfield("Modified"));
        $this->assertEquals("Modified", $entity->getOtherfield());
        $this->assertEquals("Modified", $entity->otherfield());

        try {
            $entity->nonfield();
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof \BadMethodCallException);
            $this->assertContains('No method or property named', $e->getMessage());
            $this->assertContains('exists', $e->getMessage());
        }

        $entity        = new TestEntity7();
        $entity->field = "Test";
        $this->assertContains(DoctrineGetSetTrait::class, class_uses($entity));
        $this->assertContains(IdTrait::class, class_uses($entity));
        $this->assertEquals("Accessor", $entity->field);

        $entity->otherfield = "Test";
        $this->assertEquals("Test", $entity->otherfield);
        $this->assertEquals("Test", $entity->getOtherfield());
        $this->assertEquals($entity, $entity->setOtherfield("Modified"));
        $this->assertEquals("Modified", $entity->getOtherfield());

        try {
            $entity->nonfield();
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof \BadMethodCallException);
            $this->assertContains('No method or property named', $e->getMessage());
            $this->assertContains('exists', $e->getMessage());
        }

        $seven = new TestEntity7();
        $eight = new TestEntity8();

        try {
            $seven->addBadeight($eight);
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof \BadMethodCallException);
            $this->assertContains('Doctrine ArrayCollection:', $e->getMessage());
            $this->assertContains('not initialized in __construct for', $e->getMessage());
        }

        $seven->addEight($eight);
        $this->assertContains($eight, $seven->eight->toArray());
        $this->assertEquals($seven, $eight->seven);

        $seven->removeEight($eight);
        $this->assertNotContains($eight, $seven->eight->toArray());
        $this->assertNotEquals($seven, $eight->seven);
        $this->assertNull($eight->seven);

        $seven->addEight($eight);
        $this->assertContains($eight, $seven->eight->toArray());
        $this->assertEquals($seven, $eight->seven);

        $seven->clearEight();
        $this->assertNotContains($eight, $seven->eight->toArray());
        $this->assertNotEquals($seven, $eight->seven);
        $this->assertNull($eight->seven);

        $nine = new TestEntity9();
        $nine->addEight($eight);
        $this->assertContains($eight, $nine->eight->toArray());
        $this->assertContains($nine, $eight->nine->toArray());

        $nine->removeEight($eight);
        $this->assertNotContains($eight, $nine->eight->toArray());
        $this->assertNotContains($nine, $eight->nine->toArray());

        $nine->addEight($eight);
        $this->assertContains($eight, $nine->eight->toArray());
        $this->assertContains($nine, $eight->nine->toArray());

        $nine->clearEight();
        $this->assertNotContains($eight, $nine->eight->toArray());
        $this->assertNotContains($nine, $eight->nine->toArray());
        $this->assertEmpty($eight->nine->toArray());
        $this->assertEmpty($nine->eight->toArray());

        $eight->addNine($nine);
        $this->assertContains($nine, $eight->nine->toArray());
        $this->assertContains($eight, $nine->eight->toArray());

        $eight->removeNine($nine);
        $this->assertNotContains($nine, $eight->nine->toArray());
        $this->assertNotContains($eight, $nine->eight->toArray());

        $eight->addNine($nine);
        $this->assertContains($nine, $eight->nine->toArray());
        $this->assertContains($eight, $nine->eight->toArray());

        $eight->clearNine();
        $this->assertNotContains($nine, $eight->nine->toArray());
        $this->assertNotContains($eight, $nine->eight->toArray());
        $this->assertEmpty($nine->eight->toArray());
        $this->assertEmpty($eight->nine->toArray());

        $nine2 = new TestEntity9();
        $eight->addNine($nine);
        $eight->addNine($nine2);
        $this->assertContains($nine, $eight->nine->toArray());
        $this->assertContains($eight, $nine->eight->toArray());
        $this->assertContains($nine2, $eight->nine->toArray());
        $this->assertContains($eight, $nine2->eight->toArray());
        $this->assertNotEmpty($eight->nine->toArray());
        $this->assertNotEmpty($nine->eight->toArray());
        $this->assertNotEmpty($nine2->eight->toArray());

        $eight->removeNine($nine);
        $this->assertNotContains($nine, $eight->nine->toArray());
        $this->assertNotContains($eight, $nine->eight->toArray());
        $this->assertContains($nine2, $eight->nine->toArray());
        $this->assertNotEmpty($eight->nine->toArray());
        $this->assertEmpty($nine->eight->toArray());
        $this->assertNotEmpty($nine2->eight->toArray());

        $eight->addNine($nine);
        $this->assertContains($nine, $eight->nine->toArray());
        $this->assertContains($eight, $nine->eight->toArray());
        $this->assertContains($nine2, $eight->nine->toArray());
        $this->assertContains($eight, $nine2->eight->toArray());

        $eight->clearNine();
        $this->assertNotContains($eight, $nine->eight->toArray());
        $this->assertNotContains($eight, $nine2->eight->toArray());
        $this->assertEmpty($eight->nine->toArray());
        $this->assertEmpty($nine->eight->toArray());
        $this->assertEmpty($nine2->eight->toArray());

        $eight->addNine($nine);
        $eight->addNine($nine2);
        $nine->clearEight($eight);
        $this->assertNotContains($eight, $nine->eight->toArray());
        $this->assertContains($eight, $nine2->eight->toArray());
        $this->assertNotEmpty($eight->nine->toArray());
        $this->assertEmpty($nine->eight->toArray());
        $this->assertNotEmpty($nine2->eight->toArray());

        try {
            $nine->addBadeight($eight);
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof \BadMethodCallException);
            $this->assertContains('Doctrine ArrayCollection:', $e->getMessage());
            $this->assertContains('not initialized in __construct for', $e->getMessage());
        }

        try {
            $nine->removeBadeight($eight);
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof \BadMethodCallException);
            $this->assertContains('Doctrine ArrayCollection:', $e->getMessage());
            $this->assertContains('not initialized in __construct for', $e->getMessage());
        }

        $ten = new TestEntity10();
        $ten->addTestEntity9($nine);
        $this->assertContains($nine, $ten->testEntity9->toArray());
        $this->assertContains($ten, $nine->testEntity10->toArray());

        $ten->removeTestEntity9($nine);
        $this->assertNotContains($nine, $ten->testEntity9->toArray());
        $this->assertNotContains($ten, $nine->testEntity10->toArray());

        $ten->addTestEntity9($nine);
        $this->assertContains($nine, $ten->testEntity9->toArray());
        $this->assertContains($ten, $nine->testEntity10->toArray());

        $ten->clearTestEntity9($nine);
        $this->assertEmpty($ten->testEntity9->toArray());
        $this->assertEmpty($nine->testEntity10->toArray());
        $this->assertNotContains($nine, $ten->testEntity9->toArray());
        $this->assertNotContains($ten, $nine->testEntity10->toArray());

        $nine->addTestEntity10($ten);
        $this->assertContains($nine, $ten->testEntity9->toArray());
        $this->assertContains($ten, $nine->testEntity10->toArray());

        $ten->removeTestEntity9($nine);
        $this->assertNotContains($nine, $ten->testEntity9->toArray());
        $this->assertNotContains($ten, $nine->testEntity10->toArray());

        $ten->addTestEntity9($nine);
        $this->assertContains($nine, $ten->testEntity9->toArray());
        $this->assertContains($ten, $nine->testEntity10->toArray());

        $ten->clearTestEntity9($nine);
        $this->assertEmpty($ten->testEntity9->toArray());
        $this->assertEmpty($nine->testEntity10->toArray());
        $this->assertNotContains($nine, $ten->testEntity9->toArray());
        $this->assertNotContains($ten, $nine->testEntity10->toArray());
    }

    public function testJsonSerialize()
    {
        $entity11 = new TestEntity11();
        $this->assertEquals(json_encode($entity11->jsonSerialize()), json_encode($entity11));
        $this->assertContains('TEST', json_encode($entity11));
        $this->assertContains('goodfield', json_encode($entity11));
        $this->assertContains('test1', json_encode($entity11));
        $this->assertContains('test2', json_encode($entity11));
        $this->assertContains('list', json_encode($entity11));
        $this->assertContains('eight', json_encode($entity11));
        $this->assertContains('"eight":[]', json_encode($entity11));
        $this->assertContains('"eight":[]', json_encode($entity11));
        $this->assertNotContains('NonTest', json_encode($entity11));
        $this->assertNotContains('badfield', json_encode($entity11));

        $eight = new TestEntity8();
        $entity11->addEight($eight);
        $this->assertContains('"eight":[null]', json_encode($entity11));

        $em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
        static::generateSchema($em);
        $em->persist($entity11);
        $em->persist($eight);
        $em->flush();
        $this->assertContains('"eight":[1]', json_encode($entity11));

        $eight2 = new TestEntity8();
        $entity11->addEight($eight2);
        $this->assertContains('"eight":[1,null]', json_encode($entity11));

        $em->persist($eight2);
        $em->flush();
        $this->assertContains('"eight":[1,2]', json_encode($entity11));
    }
}
