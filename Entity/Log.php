<?php

namespace Lthrt\EntityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Log.
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Lthrt\EntityBundle\Repository\LogRepository")
 */
class Log
{
    use EIDTrait;
    use GetSetTrait;
    use IdTrait;
    use LogTypeTrait;
    use UpdatedTrait;
    use UserTrait;

    /**
     * @var string
     *
     * @ORM\Column(name="class", type="string", length=255,nullable=true)
     */
    private $class = "{}";

    /**
     * @var string
     *
     * @ORM\Column(name="method", type="string", length=255, nullable=true)
     */
    private $method = "{}";
    /**

     * @var string
     *
     * @ORM\Column(name="json", type="json_array")
     */
    private $json = "{}";
}
