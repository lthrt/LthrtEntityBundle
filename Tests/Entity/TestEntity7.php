<?php

namespace Lthrt\EntityBundle\Tests\Entity;

use Doctrine\ORM\Mapping as ORM;
use Lthrt\EntityBundle\Entity\DoctrineGetSetTrait;
use Lthrt\EntityBundle\Entity\IdTrait;

/**
 * @ORM\Table(name="test_entity7")
 * @ORM\Entity()
 */
class TestEntity7
{
    use DoctrineGetSetTrait;
    use IdTrait;

    /**
     * @var string
     *
     * @ORM\Column(name="field", type="string", length=255, nullable=true)
     */
    private $field;

    /**
     * @var string
     *
     * @ORM\Column(name="otherfield", type="string", length=255, nullable=true)
     */
    private $otherfield;

    /**
     * @var string
     *
     * @ORM\Column(name="_field", type="string", length=255, nullable=true)
     */
    private $_field;

    /**
     * @var string
     *
     * @ORM\Column(name="date_field", type="date", nullable=true)
     */
    private $date;

    /**
     * @ORM\OneToMany(targetEntity="TestEntity8", mappedBy="seven")
     */
    private $eight;

    /**
     * @ORM\OneToMany(targetEntity="TestEntity8", mappedBy="badseven")
     */
    private $badeight;

    public function getField()
    {
        return "Accessor";
    }

    public function setField($field)
    {
        $this->field = $field;
    }

    public function __construct()
    {
        $this->eight = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function inverseEight()
    {
        return 'seven';
    }
}
