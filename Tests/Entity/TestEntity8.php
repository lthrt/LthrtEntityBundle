<?php

namespace Lthrt\EntityBundle\Tests\Entity;

use Doctrine\ORM\Mapping as ORM;
use Lthrt\EntityBundle\Entity\DoctrineGetSetTrait;
use Lthrt\EntityBundle\Entity\IdTrait;

/**
 * @ORM\Table(name="test_entity8")
 * @ORM\Entity()
 */
class TestEntity8
{
    use DoctrineGetSetTrait;
    use IdTrait;

    /**
     * @ORM\ManyToOne(targetEntity="TestEntity7", inversedBy="eight")
     */
    private $seven;

    /**
     * @ORM\ManyToOne(targetEntity="TestEntity7", inversedBy="badeight")
     */
    private $badseven;

    /**
     * @var \Lthrt\UserBundle\Entity\TestEntity9
     *
     * @ORM\ManyToMany(targetEntity="TestEntity9", mappedBy="eight")
     */
    private $nine;

    public function __construct()
    {
        $this->nine = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function inverseNine()
    {
        return 'eight';
    }
}
