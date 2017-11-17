<?php

namespace Lthrt\EntityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EIDTrait.
 *
 * Entity id trait, for logging
 */
trait EIDTrait
{
    /**
     * @var integer
     *
     * entityId
     *
     * @ORM\Column(name="eid", type="integer")
     */
    private $eid;
}
