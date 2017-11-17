<?php

namespace Lthrt\EntityBundle\Tests\Entity;

use Doctrine\ORM\Mapping as ORM;
use Lthrt\EntityBundle\Entity\DoctrineEntityTrait;
use Lthrt\EntityBundle\Entity\NameTrait;

/**
 * @ORM\Table(name="test_entity2")
 * @ORM\Entity()
 */
class TestEntity2 implements \Lthrt\EntityBundle\Entity\EntityLedger
{
    use NameTrait;
    use DoctrineEntityTrait;
}
