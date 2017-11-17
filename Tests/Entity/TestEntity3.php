<?php

namespace Lthrt\EntityBundle\Tests\Entity;

use Doctrine\ORM\Mapping as ORM;
use Lthrt\EntityBundle\Entity\DoctrineEntityTrait;
use Lthrt\EntityBundle\Entity\NameTrait;

/**
 * @ORM\Table(name="test_entity3")
 * @ORM\Entity()
 */
class TestEntity3
{
    use NameTrait;
    use DoctrineEntityTrait;

    /**
     * @var Lthrt\EntityBundle\Tests\TestEntity2
     *
     * @ORM\ManyToOne(targetEntity="TestEntity2")
     */
    private $entity2;

    public function jsonSerialize()
    {
        return json_encode(['id' => $this->id, 'testValue' => 'testValue']);
    }
}
