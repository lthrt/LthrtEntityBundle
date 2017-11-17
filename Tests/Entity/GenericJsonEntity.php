<?php
namespace Lthrt\EntityBundle\Tests\Entity;

use Doctrine\ORM\Mapping as ORM;
use Lthrt\EntityBundle\Entity\DoctrineEntityTrait;
use Lthrt\EntityBundle\Entity\JsonTrait;
use Lthrt\EntityBundle\Entity\NameTrait;

/**
 * @ORM\Table(name="generic_json_entity")
 * @ORM\Entity()
 */

class GenericJsonEntity implements \JsonSerializable
{
    use NameTrait;
    use DoctrineEntityTrait;
    use JsonTrait;

    /**
     * @ORM\Column(type="string", length=60, nullable=true)
     */
    private $fluff;
}
