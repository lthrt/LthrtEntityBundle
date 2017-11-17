<?php

namespace Lthrt\EntityBundle\Tests\Entity;

use Lthrt\EntityBundle\Entity\GetSetTrait;

/**
 */
class TestEntity6
{
    use GetSetTrait;

    private $field;
    private $otherfield;

    private $_field;

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
