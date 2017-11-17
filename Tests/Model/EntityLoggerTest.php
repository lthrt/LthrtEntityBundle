<?php

namespace Lthrt\EntityBundle\Tests\Model;

use Lthrt\EntityBundle\Model\PartialLogger;
use Lthrt\EntityBundle\Tests\Entity\TestEntity1;
use Lthrt\EntityBundle\Tests\Entity\TestEntity8;
use Lthrt\EntityBundle\Tests\Entity\TestEntity9;
use Lthrt\EntityBundle\Tests\Entity\TestUser1;
use Lthrt\EntityBundle\Tests\Entity\TestUser2;
use Lthrt\EntityBundle\Tests\Entity\TestUser3;
use Lthrt\EntityBundle\Tests\LoadFixturesFromMetadata;
use Lthrt\EntityBundle\Tests\TestWithoutReboot;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken as Token;

class EntityLoggerTest extends TestWithoutReboot
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

    public function testCustomUserObjects()
    {
        $em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
        static::generateSchema($em);

        $token = new Token('UnitTest', 'UnitTest', 'UnitTest');
        static::$kernel->getContainer()->get('security.token_storage')->setToken($token);
        $entity1          = new TestEntity1();
        $entity1->name    = "Entity1";
        $entity1->updated = new \DateTime();
        $em->persist($entity1);
        $em->flush();

        $fetcher = static::$kernel->getContainer()->get('lthrt.entity.logfetcher');

        $log = $fetcher->findLog($entity1);
        $this->assertContains('Entity1', $log);
        $this->assertContains('"user":"UnitTest"', $log);

        $token = new Token(new TestUser1(), 'ROLE_ADMIN', 'TestUser');
        static::$kernel->getContainer()->get('security.token_storage')->setToken($token);
        $entity1          = new TestEntity1();
        $entity1->name    = "Entity1";
        $entity1->updated = new \DateTime();
        $em->persist($entity1);
        $em->flush();

        $log = $fetcher->findLog($entity1);
        $this->assertContains('Entity1', $log);
        $this->assertContains('"user":"idfortestuser"', $log);

        $token = new Token(new TestUser2(), 'ROLE_ADMIN', 'TestUser');
        static::$kernel->getContainer()->get('security.token_storage')->setToken($token);
        $entity1          = new TestEntity1();
        $entity1->name    = "Entity1";
        $entity1->updated = new \DateTime();
        $em->persist($entity1);
        $em->flush();

        $log = $fetcher->findLog($entity1);
        $this->assertContains('Entity1', $log);
        $this->assertContains('"user":"testuser"', $log);

        $token = new Token(new TestUser2(), 'ROLE_ADMIN', 'TestUser');
        static::$kernel->getContainer()->get('security.token_storage')->setToken(null);
        $entity1          = new TestEntity1();
        $entity1->name    = "Entity1";
        $entity1->updated = new \DateTime();
        $em->persist($entity1);
        $em->flush();

        $log = $fetcher->findLog($entity1);
        $this->assertContains('Entity1', $log);
        $this->assertContains('"user":null', $log);

        $token = new Token(new TestUser3(), 'ROLE_ADMIN', 'TestUser');
        static::$kernel->getContainer()->get('security.token_storage')->setToken($token);
        $entity1          = new TestEntity1();
        $entity1->name    = "Entity1";
        $entity1->updated = new \DateTime();
        $em->persist($entity1);
        $em->flush();

        $log = $fetcher->findLog($entity1);
        $this->assertContains('Entity1', $log);
        $this->assertContains('"user":"tokenuser"', $log);
    }

    public function testPartialLogger()
    {
        $em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
        static::generateSchema($em);
        $logger           = new PartialLogger($em, static::$kernel->getContainer()->get('annotation_reader'));
        $entity1          = new TestEntity1();
        $entity1->updated = new \DateTime();
        $entity1->name    = "Entity1";
        $logger->log($entity1);

        $partials = $em->getRepository('LthrtEntityBundle:Partial')->findAll();
        $jsons    = array_map(function ($p) {return $p->json;}, $partials);
        $this->assertGreaterThan(0, array_filter($jsons, function ($j) {
            return strpos($j, '"name":"Entity1"') !== false;
        }));

        $classes = array_map(function ($p) {return $p->class;}, $partials);
        $this->assertGreaterThan(0, array_filter($classes, function ($c) use ($entity1) {
            return strpos($c, get_class($entity1));
        }));

        $nine      = new TestEntity9();
        $eight     = new TestEntity8();
        $alsoEight = new TestEntity8();
        $nine->addEight($eight);
        $nine->addEight($alsoEight);
        $logger->log($eight);
        $logger->log($nine);

        $partials = $em->getRepository('LthrtEntityBundle:Partial')->findAll();
        $jsons    = array_map(function ($p) {return $p->json;}, $partials);
        $this->assertGreaterThan(0, array_filter($jsons, function ($j) {
            return strpos($j, '"nine":"[null]"') !== false;
        }));
        $this->assertGreaterThan(0, array_filter($jsons, function ($j) {
            return strpos($j, '"eight":"[null,null]"') !== false;
        }));

        $em->persist($nine);
        $em->persist($eight);
        $em->persist($alsoEight);
        $em->flush();
        $logger->log($eight);
        $logger->log($nine);

        $partials = $em->getRepository('LthrtEntityBundle:Partial')->findAll();
        $jsons    = array_map(function ($p) {return $p->json;}, $partials);
        $this->assertGreaterThan(0, array_filter($jsons, function ($j) {
            return strpos($j, '"nine":"[1]"') !== false;
        }));
        $this->assertGreaterThan(0, array_filter($jsons, function ($j) {
            return strpos($j, '"eight":"[1,2]"') !== false;
        }));

    }
}
