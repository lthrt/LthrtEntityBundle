<?php

namespace Lthrt\EntityBundle\Annotation;

use Doctrine\Common\Annotations\Annotation;

// Place aheaqd of attribute to no log

/**
 * @Annotation
 * @Target("PROPERTY")
 */

class NoLogThisField
{
    /**
     * @Required
     *
     * @var boolean
     */
    public $active;

    public function getActive()
    {
        return $this->active;
    }
}
