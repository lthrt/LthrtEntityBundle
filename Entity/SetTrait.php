<?php

namespace Lthrt\EntityBundle\Entity;

/**
 * Set Trait.
 *
 * Not to be used directly
 *
 * Use GetSetTrait or EntityTrait
 *
 * For discussion see: http://www.epixa.com/2010/05/the-best-models-are-easy-models.html
 */
trait SetTrait
{
    /**
     * This is not meant to be used alone.  Use GetSetTrait.
     *
     * Map a call to set a property to its corresponding mutator if it exists.
     * Otherwise, set the property directly.
     *
     * Ignore any properties that begin with an underscore so not all of our
     * protected properties are exposed.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @throws \LogicException If no mutator/property exists by that name
     */
    public function __set(
        $name,
        $value
    ) {
        $reader    = new \Doctrine\Common\Annotations\AnnotationReader();
        $reflClass = new \ReflectionClass(__CLASS__);
        try {
            $reflProp = $reflClass->getProperty($name);

        } catch (\Exception $e) {
            throw new \BadMethodCallException(sprintf(
                'Bad __set(): No property named `%s` exists',
                $name
            ));

        }

        $annotations = $reader->getPropertyAnnotations($reflProp);

        // Should be more generic transformers here
        // specific call out for date is ugly
        // hmm

        if ('_' != $name[0]) {
            if (
                $annotations
                &&
                property_exists($annotations[0], 'type')
                &&
                (
                    $annotations[0]->type == 'datetime'
                    ||
                    $annotations[0]->type == 'date'
                )
            ) {
                if ($value instanceof \DateTime) {
                    $this->$name = $value;
                } else {
                    try {
                        $this->$name = new \DateTime($value);
                    } catch (\Exception $e) {
                        throw new \BadMethodCallException(sprintf(
                            'Bad __set(): invalid date string `%s` passed to property `%s`',
                            $value,
                            $name
                        ));
                    }
                }
                return $this;
            } else {
                $mutator = 'set' . ucfirst($name);
                if (method_exists($this, $mutator)) {
                    $this->$mutator($value);

                    return $this;
                }
                if (property_exists($this, $name)) {
                    $this->$name = $value;

                    return $this;
                }

            }

        }
        throw new \BadMethodCallException(sprintf(
            'Bad __set(): property protected by leading underscore:`%s`',
            $name
        ));

    }
}
