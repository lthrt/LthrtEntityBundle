<?php
namespace Lthrt\EntityBundle\Tests\Entity;

use Doctrine\ORM\Mapping as ORM;
use Lthrt\EntityBundle\Entity\DoctrineEntityTrait;
use Lthrt\EntityBundle\Entity\NameTrait;

/**
 * @ORM\Table(name="generic_entity")
 * @ORM\Entity()
 */

class GenericEntity
{
    use NameTrait;
    use DoctrineEntityTrait;

    /**
     * @ORM\Column(type="string", length=60, nullable=true)
     */
    private $fluff;
}
