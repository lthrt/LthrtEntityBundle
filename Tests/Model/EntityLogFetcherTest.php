<?php

namespace Lthrt\EntityBundle\Tests\Model;

use Lthrt\EntityBundle\Tests\Entity\TestEntity1;
use Lthrt\EntityBundle\Tests\Entity\TestEntity2;
use Lthrt\EntityBundle\Tests\Entity\TestEntity3;
use Lthrt\EntityBundle\Tests\LoadFixturesFromMetadata;
use Lthrt\EntityBundle\Tests\TestWithoutReboot;

class EntityLogFetcherTest extends TestWithoutReboot
{
    use LoadFixturesFromMetadata;

    public function testIsThisThingOn()
    {
        $this->assertTrue(true);
    }

    public function testLogAndLedgerFetcher()
    {
        $em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
        static::generateSchema($em);

        $entity1          = new TestEntity1();
        $entity1->updated = new \DateTime();
        $entity1->name    = "Entity1";

        $em->persist($entity1);
        $em->flush();

        sleep(1);
        $entity1->name    = "Entity_1";
        $entity1->updated = new \DateTime();
        $em->flush();

        sleep(1);
        $entity1->name    = "Entity_I";
        $entity1->updated = new \DateTime();
        $em->flush();

        sleep(1);
        $entity1->name    = "entity_i";
        $entity1->updated = new \DateTime();
        $em->flush();

        sleep(1);
        $entity2          = new TestEntity1();
        $entity2->name    = "entity_ii";
        $entity2->updated = new \DateTime();
        $em->persist($entity2);

        sleep(1);
        $entity3          = new TestEntity1();
        $entity3->updated = new \DateTime();
        $entity3->name    = "entity_iii";
        $em->persist($entity3);

        sleep(1);
        $entity4       = new TestEntity2();
        $entity4->name = "entity_ledger_1";
        $em->persist($entity4);
        $em->flush();

        sleep(1);
        $entity4->name = "entity_ledger_I";
        $em->persist($entity4);
        $em->flush();

        sleep(1);
        $entity4->name = "entity_ledger_i";
        $em->persist($entity4);
        $em->flush();

        $entity5       = new TestEntity3();
        $entity5->name = "entity_unlogged";
        $em->persist($entity5);
        $em->flush();

        $fetcher = static::$kernel->getContainer()->get('lthrt.entity.logfetcher');

        $log = $fetcher->findLog($entity1);
        $this->assertContains('Entity1', $log);
        $this->assertContains('Entity_1', $log);
        $this->assertContains('Entity_I', $log);
        $this->assertContains('entity_i', $log);
        $this->assertCount(4, json_decode($log));

        $log = $fetcher->findLog($entity1, false);
        $this->assertContains('Entity1', json_encode(array_map(function ($l) {return $l->json;}, $log)));
        $this->assertContains('Entity_1', json_encode(array_map(function ($l) {return $l->json;}, $log)));
        $this->assertContains('Entity_I', json_encode(array_map(function ($l) {return $l->json;}, $log)));
        $this->assertContains('entity_i', json_encode(array_map(function ($l) {return $l->json;}, $log)));

        $log = $fetcher->findLog($entity4);
        $this->assertContains('entity_ledger_i', $log);
        $this->assertNotContains('entity_ledger_I', $log);
        $this->assertNotContains('entity_ledger_1', $log);
        $this->assertCount(3, json_decode($log)->ledger);

        $log = $fetcher->findLog($entity4, false);
        $this->assertEquals('entity_ledger_i', $log['current']->name);
        $this->assertNotContains('entity_ledger_I', json_encode($log));
        $this->assertNotContains('entity_ledger_1', json_encode($log));
        $this->assertCount(3, $log['ledger']);

        $log = $fetcher->findLog($entity5);
        $this->assertEquals('null', $log);
        $log = $fetcher->findLog($entity5, false);
        $this->assertNull($log);
    }
}
