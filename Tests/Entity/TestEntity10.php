<?php

namespace Lthrt\EntityBundle\Tests\Entity;

use Doctrine\ORM\Mapping as ORM;
use Lthrt\EntityBundle\Entity\DoctrineGetSetTrait;
use Lthrt\EntityBundle\Entity\IdTrait;

/**
 * @ORM\Table(name="test_entity10")
 * @ORM\Entity()
 */
class TestEntity10
{
    use DoctrineGetSetTrait;
    use IdTrait;

    /**
     * @var Lthrt\EntityBundle\Tests\TestEntity9
     *
     * @ORM\ManyToMany(targetEntity="TestEntity9")
     * @ORM\JoinTable(name="ten__nine",
     *      joinColumns={@ORM\JoinColumn(name="ten_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="nine_id", referencedColumnName="id")}
     * )
     */
    private $testEntity9;

    /**
     * @ORM\OneToMany(targetEntity="TestEntity8", mappedBy="badseven")
     */
    private $badeight;

    public function __construct()
    {
        $this->testEntity9 = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function inverseEight()
    {
        return 'nine';
    }
}
