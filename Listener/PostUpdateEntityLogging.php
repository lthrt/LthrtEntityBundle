<?php

namespace Lthrt\EntityBundle\Listener;

use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Lthrt\EntityBundle\Model\EntitySerializer;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class PostUpdateEntityLogging
{
    use LoggingTrait;

    private $annotationReader;
    private $tokens;

    /**
     *  @param Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage
     *  @param Doctrine\Common\Annotations\Reader
     */
    public function __construct(
        TokenStorage $tokens,
        Reader       $annotationReader
    ) {
        $this->annotationReader = $annotationReader;
        $this->tokens           = $tokens;

    }

    /**
     * PostUpdate Callback.
     *
     * @param LifecycleEventArgs $args Doctrines lifecycle event args
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->serializer = new EntitySerializer($args->getEntityManager(), $this->annotationReader);
        $this->log($args, $this->tokens);
    }
}
