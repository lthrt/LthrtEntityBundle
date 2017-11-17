<?php

namespace Lthrt\EntityBundle\Tests\Model;

use Lthrt\EntityBundle\Tests\LoadFixturesFromMetadata;
use Lthrt\EntityBundle\Tests\TestWithoutReboot;

class ClassVerifierTest extends TestWithoutReboot
{
    use LoadFixturesFromMetadata;

    public function testIsThisThingOn()
    {
        $this->assertTrue(true);
    }

    public function testVerifyClass()
    {
        $verifier = static::$kernel->getContainer()->get('lthrt.entity.class_verifier');
        $this->assertFalse($verifier->verifyClass('Null'));
        $this->assertNull($verifier->classAssociations($verifier->verifyClass('Null')));
        $this->assertEquals('Lthrt\EntityBundle\Tests\Entity\TestEntity1', $verifier->verifyClass('TestEntity1'));
        $this->assertEquals([], $verifier->classAssociations($verifier->verifyClass('TestEntity1')));
        $this->assertEquals([], $verifier->classAssociations($verifier->verifyClass('TestEntity2')));
        $this->assertTrue(isset($verifier->classAssociations($verifier->verifyClass('TestEntity3'))['entity2']));
        $this->assertEquals('entity2', $verifier->classAssociations($verifier->verifyClass('TestEntity3'))['entity2']['fieldName']);
        $this->assertEquals('Lthrt\EntityBundle\Tests\Entity\TestEntity2', $verifier->classAssociations($verifier->verifyClass('TestEntity3'))['entity2']['targetEntity']);
        $this->assertEquals('Lthrt\EntityBundle\Tests\Entity\TestEntity3', $verifier->classAssociations($verifier->verifyClass('TestEntity3'))['entity2']['sourceEntity']);
    }
}
