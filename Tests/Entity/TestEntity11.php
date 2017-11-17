<?php

namespace Lthrt\EntityBundle\Tests\Entity;

use Doctrine\ORM\Mapping as ORM;
use Lthrt\EntityBundle\Entity\DoctrineGetSetTrait;
use Lthrt\EntityBundle\Entity\IdTrait;
use Lthrt\EntityBundle\Entity\JsonTrait;

/**
 * @ORM\Table(name="test_entity11")
 * @ORM\Entity()
 */
class TestEntity11 implements \JsonSerializable
{
    use IdTrait;
    use JsonTrait;
    use DoctrineGetSetTrait;

    /**
     * @ORM\OneToMany(targetEntity="TestEntity8", mappedBy="eleven")
     */
    private $eight;

    private $list;

    private $goodfield;

    private $_badfield;

    public function __construct()
    {
        $this->eight     = new \Doctrine\Common\Collections\ArrayCollection();
        $this->list      = ["test1", "test2"];
        $this->goodfield = "TEST";
        $this->_badfield = "NonTest";
    }

    public function inverseEleven()
    {
        return 'eight';
    }
}
