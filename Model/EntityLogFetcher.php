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
use Lthrt\EntityBundle\Model\EntitySerializer;

class EntityLogFetcher
{
    private $em;
    private $serializer;
    private $user;
    private $class;
    private $method;

    public function __construct(
        EntityManager    $em,
        EntitySerializer $serializer
    ) {
        $this->em         = $em;
        $this->serializer = $serializer;
    }

    /**
     * Get Log or Ledger from database
     * asJSON converts result to json array (optional, default true)
     *
     * @param object $entity
     * @param boolean $asJSON
     *
     * @return mixed
     *
     */
    public function findLog(
        $entity,
        $asJSON = true
    ) {
        $logType = $this->em->getRepository('LthrtEntityBundle:LogType')
            ->findOneBy([
                'name' => str_replace("\\", "\\\\", get_class($entity)),
            ]);

        $isLedger = ($entity instanceof EntityLedger);

        if ($isLedger) {
            $qb = $this->em->getRepository('LthrtEntityBundle:Ledger')->createQueryBuilder('log');
        } elseif ($entity instanceof EntityLog) {
            $qb = $this->em->getRepository('LthrtEntityBundle:Log')->createQueryBuilder('log');
        } else {
            if ($asJSON) {
                return json_encode(null);
            } else {
                return null;
            }
        }

        $qb->andWhere($qb->expr()->eq('log.type', ':logType'));
        $qb->andWhere($qb->expr()->eq('log.eid', ':entity'));
        $qb->setParameter(':logType', $logType->getId());
        $qb->setParameter('entity', $entity->getId());
        $qb->addOrderBy('log.updated');
        $result = $qb->getQuery()->getResult() ?: null;
        if ($asJSON) {
            if ($isLedger) {
                return json_encode(
                    [
                        'current' => $this->normalize($entity),
                        'ledger'  => $this->normalize($result),
                    ]
                );
            } else {
                return $result ? $this->serialize($result) : json_encode(null);
            }
        } else {
            if ($isLedger) {
                return [
                    'current' => $entity,
                    'ledger'  => $result,
                ];
            } else {
                return $result ?: null;
            }
        }
    }

    public function serialize($item)
    {
        return $this->serializer->serialize($item);
    }
    public function normalize($item)
    {
        return $this->serializer->normalize($item);
    }
}
