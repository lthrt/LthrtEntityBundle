<?php

namespace Lthrt\EntityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UpdatedTrait.
 */
trait UpdatedTrait
{
    /**
     * @var string
     *
     * @ORM\Column(name="updated", type="datetime")
     */
    private $updated;
}
