<?php

namespace Lthrt\EntityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Partial Log.
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Lthrt\EntityBundle\Repository\PartialRepository")
 */
class Partial
{
    use GetSetTrait;
    use IdTrait;
    use UserTrait;
    use UpdatedTrait;

    /**
     * @var string
     *
     * @ORM\Column(name="json", type="text")
     */
    private $json = "{}";

    /**
     * @var string
     *
     * @ORM\Column(name="class", type="string", length=255,nullable=true)
     */
    private $class = "{}";
}
