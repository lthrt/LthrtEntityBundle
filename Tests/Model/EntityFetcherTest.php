<?php

namespace Lthrt\EntityBundle\Tests\Model;

use Lthrt\EntityBundle\Tests\Entity\TestEntity1;
use Lthrt\EntityBundle\Tests\LoadFixturesFromMetadata;
use Lthrt\EntityBundle\Tests\TestWithoutReboot;

class EntityFetcherTest extends TestWithoutReboot
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

    public function testGetEntityandEntities()
    {
        $em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
        static::generateSchema($em);

        $now = new \DateTime();

        $entity1          = new TestEntity1();
        $entity1->name    = "Entity1";
        $entity1->updated = $now;
        $em->persist($entity1);
        $em->flush();

        $entity1->name = "Entity_1";
        $em->persist($entity1);
        $em->flush();

        $entity1->name = "Entity_I";
        $em->persist($entity1);
        $em->flush();

        $entity1->name = "entity_i";
        $em->persist($entity1);
        $em->flush();

        $entity2          = new TestEntity1();
        $entity2->name    = "entity_2";
        $entity2->updated = $now;
        $em->persist($entity2);

        $entity3          = new TestEntity1();
        $entity3->updated = $now;
        $entity3->name    = "entity_iii";
        $em->persist($entity3);

        $em->flush();

        $fetcher = static::$kernel->getContainer()->get('lthrt.entity.fetcher');
        $fetched = $fetcher->getEntity('TestEntity1', 1);
        $this->assertNotNull($fetched);
        $this->assertEquals($fetched, $entity1);

        $fetched = $fetcher->getEntity('TestEntity1', 100);
        $this->assertNull($fetched);

        $fetched = $fetcher->getEntity('TestEntityOne', 100);
        $this->assertNull($fetched);

        try {
            $fetched = $fetcher->getEntities('TestEntityOne', 100);
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof \InvalidArgumentException);
            $this->assertContains('Parameter `ids` passed to EntityFetcher::getEntities must be an array.', $e->getMessage());
        }

        $fetched = $fetcher->getEntities('TestEntityOne', [100]);
        $this->assertNull($fetched);

        $fetched = $fetcher->getEntities('TestEntity1', [100]);
        $this->assertNull($fetched);

        $fetched = $fetcher->getEntities('TestEntity1', [1]);
        $this->assertNotNull($fetched);
        $this->assertCount(1, $fetched);

        $fetched = $fetcher->getEntities('TestEntity1', [1, 2]);
        $this->assertNotNull($fetched);
        $this->assertCount(2, $fetched);

        $fetched = $fetcher->getEntities('TestEntity1', [1, 2, 3]);
        $this->assertNotNull($fetched);
        $this->assertCount(3, $fetched);

        $fetched = $fetcher->getAll('TestEntityOne');
        $this->assertNull($fetched);

        $fetched = $fetcher->getAll('TestEntity2');
        $this->assertNull($fetched);

        $fetched = $fetcher->getAll('TestEntity1');
        $this->assertNotNull($fetched);
        $this->assertCount(3, $fetched);
    }
}
