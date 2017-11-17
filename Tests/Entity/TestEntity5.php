<?php

namespace Lthrt\EntityBundle\Tests\Entity;

use Doctrine\ORM\Mapping as ORM;
use Lthrt\EntityBundle\Entity\DoctrineEntityTrait;

/**
 * @ORM\Table(name="test_entity5")
 * @ORM\Entity()
 */
class TestEntity5
{
    use DoctrineEntityTrait;

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

    public function getField()
    {
        return "Accessor";
    }

    public function setField($field)
    {
        $this->field = $field;
    }
}
