<?php

/*
 * Handles logging and ledgeing of entitys with Doctrine Lifecycle callbacks
 *
 * @author lthrt <lighthart.coder@gmail.com>
 *
 */

namespace Lthrt\EntityBundle\Model;

use Doctrine\ORM\EntityManager;
use Lthrt\EntityBundle\Entity\EntityLedger;
use Lthrt\EntityBundle\Entity\EntityLog;
use Lthrt\EntityBundle\Entity\Ledger;
use Lthrt\EntityBundle\Entity\Log;
use Lthrt\EntityBundle\Entity\LogType;
use Lthrt\EntityBundle\Model\EntitySerializer;
use Lthrt\EntityBundle\Model\RectifyUserTrait;

class EntityLogger
{
    use RectifyUserTrait;

    private $em;
    private $serializer;
    private $class;
    private $method;

    public function __construct(
        EntityManager    $em,
        EntitySerializer $serializer,
                         $user = null,
                         $backtrace = null
    ) {
        $this->em = $em;

        $this->serializer = $serializer;

        $this->rectifyUser($user);

        if ($backtrace && isset($backtrace['class'])) {
            $this->class = $backtrace['class'];
        }

        if ($backtrace && isset($backtrace['function'])) {
            $this->method = $backtrace['function'];
        }
    }

    /**
     * Actually writes the log or ledger based on
     * which is implemented.  Log takes precedent over ledger
     *
     * Called in PostPersist/PostUpdate Callback.
     *
     * @param object $entity
     */

    public function writeLog($entity)
    {
        if ($entity instanceof EntityLog or $entity instanceof EntityLedger) {
            if (property_exists($entity, 'loggingDisabled') && $entity->loggingDisabled) {
            } else {
                $escapedName = str_replace("\\", "\\\\", get_class($entity));
                $logType     = $this->em->getRepository('LthrtEntityBundle:LogType')
                    ->findOneBy([
                        'name' => $escapedName,
                    ]
                    );
                if ($logType) {
                } else {
                    $logType       = new LogType();
                    $logType->name = $escapedName;
                    $this->em->persist($logType);
                    $this->em->flush($logType);
                }

                if ($entity instanceof EntityLog) {
                    $log = new Log();

                    $log->class  = str_replace("\\", "\\\\", $this->class);
                    $log->method = $this->method;
                    $log->json   = (
                        $entity instanceof \JsonSerializable
                        ? $entity
                        : $this->serializer->normalize($entity)
                    );
                } else {
                    $log = new Ledger();
                }

                $log->updated = new \DateTime();
                $log->type    = $logType;
                $log->user    = $this->user;
                $log->eid     = $entity->getId();
                $this->em->persist($log);
                $this->em->flush($log);
            }
        }
    }
}
