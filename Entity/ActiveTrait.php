<?php

namespace Lthrt\EntityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ActiveTrait.
 */
trait ActiveTrait
{
    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $active = true;
}
