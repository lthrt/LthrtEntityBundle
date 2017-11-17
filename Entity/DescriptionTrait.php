<?php

namespace Lthrt\EntityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DescTrait.
 */
trait DescriptionTrait
{
    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;
}
