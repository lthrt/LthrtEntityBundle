<?php

namespace Lthrt\EntityBundle\Tests\Entity;

use Doctrine\ORM\Mapping as ORM;
use Lthrt\EntityBundle\Annotation as Lthrt;
use Lthrt\EntityBundle\Entity\DoctrineEntityTrait;
use Lthrt\EntityBundle\Entity\NameTrait;
use Lthrt\EntityBundle\Entity\UpdatedTrait;

/**
 * @ORM\Table(name="test_entity1")
 * @ORM\Entity()
 */
class TestEntity1 implements \Lthrt\EntityBundle\Entity\EntityLog
{
    use NameTrait;
    use DoctrineEntityTrait;
    use UpdatedTrait;

    private $fluff;

    /**
     * @ORM\Column(type="string", length=60, nullable=true)
     * @Lthrt\NoLogThisField(active=true)
     */
    private $extraFluff;
    private $notFluff;
}
