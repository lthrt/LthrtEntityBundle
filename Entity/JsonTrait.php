<?php

namespace Lthrt\EntityBundle\Entity;

use Doctrine\Common\Collections\Collection;

/**
 * JsonTrait.
 *
 * Used by entity logger
 *
 */
trait JsonTrait
{
    /**
     *  Using class must implement "\JsonSerializable" for
     *  json_encode global function to work i.e. json_encode($entity)
     *  all fields starting with underscore are excluded
     */
    public function jsonSerialize()
    {
        $fields = array_map(function ($field) {
            if ($field instanceof Collection) {
                return array_map(function ($object) {return $object->getId();}, $field->toArray());
            } else {
                return $field;
            }
        },
            array_filter(get_object_vars($this),
                function ($v) {
                    return "_" != substr($v, 0, 1);
                },
                ARRAY_FILTER_USE_KEY
            )
        );
        return $fields;
    }
}
