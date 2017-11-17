<?php

namespace Lthrt\EntityBundle\Model;

use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\EntityManager;
use Lthrt\EntityBundle\Entity\Partial;
use Lthrt\EntityBundle\Model\RectifyUserTrait;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class PartialLogger
{
    use RectifyUserTrait;

    private $em;
    private $serializer;
    private $annotationReader;

    public function __construct(
        EntityManager $em,
        Reader        $annotationReader,
                      $user = null
    ) {
        $this->em               = $em;
        $this->annotationReader = $annotationReader;
        $this->rectifyUser($user);
    }

    public function partial($entity)
    {
        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceHandler(function ($object) {return $object->getId();});
        $normalizers = [$normalizer];
        $encoder     = new JsonEncoder();
        $serializer  = new EntitySerializer($this->em, $this->annotationReader);
        $serializer->configure($entity);
        $json = $serializer->serialize($entity);

        $partial          = new Partial();
        $partial->json    = $json;
        $partial->user    = $this->user;
        $partial->updated = new \DateTime();
        $partial->class   = get_class($entity);

        return $partial;
    }

    public function log($entity)
    {
        $partial = $this->partial($entity);
        $this->em->persist($partial);
        $this->em->flush();
    }
}
