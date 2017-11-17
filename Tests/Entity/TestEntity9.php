<?php

namespace Lthrt\EntityBundle\Tests\Entity;

use Doctrine\ORM\Mapping as ORM;
use Lthrt\EntityBundle\Entity\DoctrineGetSetTrait;
use Lthrt\EntityBundle\Entity\IdTrait;

/**
 * @ORM\Table(name="test_entity9")
 * @ORM\Entity()
 */
class TestEntity9
{
    use DoctrineGetSetTrait;
    use IdTrait;

    /**
     * @var Lthrt\EntityBundle\Tests\TestEntity8
     *
     * @ORM\ManyToMany(targetEntity="TestEntity8")
     * @ORM\JoinTable(name="nine__eight",
     *      joinColumns={@ORM\JoinColumn(name="nine_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="eight_id", referencedColumnName="id")}
     * )
     */
    private $eight;

    /**
     * @var \Lthrt\UserBundle\Entity\TestEntity10
     *
     * @ORM\ManyToMany(targetEntity="TestEntity10", mappedBy="TestEntity9")
     */
    private $testEntity10;

    /**
     * @ORM\OneToMany(targetEntity="TestEntity8", mappedBy="badseven")
     */
    private $badeight;

    public function __construct()
    {
        $this->eight        = new \Doctrine\Common\Collections\ArrayCollection();
        $this->testEntity10 = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function inverseEight()
    {
        return 'nine';
    }
}
