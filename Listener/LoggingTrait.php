<?php

namespace Lthrt\EntityBundle\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Lthrt\EntityBundle\Entity\EntityLedger;
use Lthrt\EntityBundle\Entity\EntityLog;
use Lthrt\EntityBundle\Model\EntityLogger;
use Lthrt\EntityBundle\Model\FlushFinder;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

trait LoggingTrait
{
    public function log(
        LifecycleEventArgs $args,
        TokenStorage       $tokens
    ) {
        if ($args->getEntity() instanceof EntityLog || $args->getEntity() instanceof EntityLedger) {
            $this->user = $this->tokens && $this->tokens->getToken()
            ? $this->tokens->getToken()->getUser()
            : null;

            $finder  = new FlushFinder();
            $flusher = $finder->getFlusher(debug_backtrace());

            $logger = new EntityLogger(
                $args->getEntityManager(),
                $this->serializer,
                $this->user,
                $flusher
            );

            $logger->writeLog($args->getEntity());
        }
    }
}
