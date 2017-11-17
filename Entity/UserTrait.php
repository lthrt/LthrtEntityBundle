<?php

namespace Lthrt\EntityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AbbrTrait.
 */
trait UserTrait
{
    /**
     * @var string
     *
     * @ORM\Column(name="app_user", type="text", nullable=true)
     */
    private $user;
}
