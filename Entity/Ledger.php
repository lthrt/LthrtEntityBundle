<?php

namespace Lthrt\EntityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Ledger.
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Lthrt\EntityBundle\Repository\LogRepository")
 */
class Ledger
{
    use EIDTrait;
    use GetSetTrait;
    use IdTrait;
    use LogTypeTrait;
    use UpdatedTrait;
    use UserTrait;
}
