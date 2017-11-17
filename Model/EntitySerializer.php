<?php

/*
 * Handles logging and ledgeing of entitys with Doctrine Lifecycle callbacks
 *
 * @author lthrt <lighthart.coder@gmail.com>
 *
 */

namespace Lthrt\EntityBundle\Model;

use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\EntityManager;
use Lthrt\EntityBundle\Entity\Log;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Serializer\Serializer;

class EntitySerializer
{
    private $em;
    private $annotationReader;
    private $ignored;
    private $serializer;

    public function __construct(
        EntityManager $em,
        Reader        $annotationReader,
                      $ignored = []
    ) {
        $this->em = $em;

        $this->annotationReader = $annotationReader;

        $this->ignored = [];
        if ($ignored) {
            $this->ignored = array_merge($this->ignored, $ignored);
        }
    }

    public function getSerializer($callbacks = [])
    {
        $encoder    = new JsonEncoder();
        $normalizer = new PropertyNormalizer();
        $normalizer->setIgnoredAttributes($this->ignored);
        $normalizer->setCallbacks($callbacks);

        return new Serializer([$normalizer], [$encoder]);
    }
    /**
     * Configures Logger based on metadata
     * Creates serializer
     * only used if object does not have its own JSONSerialize
     *
     * @param object $entity
     *
     */
    public function configure($entity)
    {
        $em = $this->em;

        if (is_array($entity)) {
            $className = get_class(array_slice($entity, 0, 1)[0]);
        } else {
            $className = get_class($entity);
        }
        $metadata = $em->getClassMetadata($className);
        foreach ($metadata->fieldMappings as $field) {
            $annotation = $this->annotationReader->getPropertyAnnotation(
                new \ReflectionProperty($className, $field['fieldName']),
                'Lthrt\EntityBundle\Annotation\NoLogThisField'
            );
            if (isset($annotation) && $annotation->getActive()) {
                $this->ignored[] = $field['fieldName'];
            }
        }

        $dates = array_filter(
            $metadata->fieldMappings, function ($field) {
                return 'date' == $field['type'] || 'datetime' == $field['type'];
            }
        );

        $callbacks = [];
        array_map(
            function ($field) use ($entity, $em, $dates, &$callbacks) {
                $callback = function ($dateTime) {
                    return $dateTime instanceof \DateTime
                    ? $dateTime->format('Y-m-d H:i:s T')
                    : null;
                };
                $callbacks[$field] = $callback;
            },
            array_keys($dates)
        );

        $assocs = $metadata->getAssociationMappings();
        array_map(
            function ($assoc) use ($entity, $em, $assocs, &$callbacks) {
                if ($assocs[$assoc]['type'] <= 2) {
                    $callback = function ($object) {
                        return $object ? $object->getId() : null;
                    };
                } else {
                    $callback = function ($objects) {
                        return $objects ? $objects->map(function ($object) {return $object->getId();})->toArray() : [];
                    };
                }
                $callbacks[$assoc] = $callback;
            },
            array_keys($assocs)
        );

        $this->serializer = $this->getSerializer($callbacks);
    }

    public function serialize($entity)
    {
        if ($entity) {
            if (is_array($entity)) {
                $representation = $entity[0];
            } else {
                $representation = $entity;
            }

            if (method_exists($representation, 'jsonSerialize') && $representation instanceof \JsonSerializable) {
                return json_encode($entity);
            } else {
                $this->configure($representation);
                return $this->serializer->serialize($entity, 'json');
            }
        } else {
            return json_encode(null);
        }
    }

    public function normalize($entity)
    {
        if ($entity) {
            if (!is_array($entity)) {
                if (method_exists($entity, 'jsonSerialize') && $entity instanceof \JsonSerializable) {
                    return $entity->jsonSerialize();
                } else {
                    $this->configure($entity);
                    return $this->serializer->normalize($entity, 'json');
                }
            } else {
                if (method_exists($entity[0], 'jsonSerialize') && $entity[0] instanceof \JsonSerializable) {
                    return array_map(function ($ent) {return $ent->jsonSerialize();}, $entity);
                } else {
                    $this->configure($entity[0]);
                    return $this->serializer->normalize($entity, 'json');
                }
            }
        } else {
            return json_encode(null);
        }
    }

    public function deserialize(
        $json,
        $class
    ) {
        $this->serializer = $this->getSerializer();

        return $this->serializer->deserialize($json, $class, 'json');
    }
}
