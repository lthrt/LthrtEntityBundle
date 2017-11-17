<?php
namespace Lthrt\EntityBundle\Tests\Entity;

use Doctrine\ORM\Mapping as ORM;
use Lthrt\EntityBundle\Entity\DoctrineEntityTrait;
use Lthrt\EntityBundle\Entity\EntityLog;
use Lthrt\EntityBundle\Entity\JsonTrait;
use Lthrt\EntityBundle\Entity\NameTrait;

/**
 * @ORM\Table(name="generic_logged_entity")
 * @ORM\Entity()
 */

class GenericLoggedEntity implements EntityLog, \JsonSerializable
{
    use NameTrait;
    use DoctrineEntityTrait;
    use JsonTrait;

    /**
     * @ORM\Column(type="string", length=60, nullable=true)
     */
    private $fluff;
}
