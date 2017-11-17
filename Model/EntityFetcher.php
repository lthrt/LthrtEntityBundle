<?php

namespace Lthrt\EntityBundle\Model;

use Doctrine\ORM\EntityManager;
use Lthrt\EntityBundle\Model\ClassVerifier;

class EntityFetcher
{
    private $em;
    private $verifier;

    public function __construct(
        EntityManager $em,
        ClassVerifier $verifier
    ) {
        $this->em       = $em;
        $this->verifier = $verifier;
    }

    public function getEntity(
        $class,
        $id
    ) {
        $class = $this->verifier->verifyClass($class);
        if ($class) {
            $qb = $this->em->getRepository($class)->createQueryBuilder('entity');
            $qb->andWhere($qb->expr()->eq('entity.id', ':id'));
            $qb->setParameter('id', $id);
            $entity = $qb->getQuery()->getOneOrNullResult();
            if ($entity) {
                return $entity;
            } else {
                return;
            }
        } else {
            return;
        }
    }

    public function getEntities(
        $class,
        $ids
    ) {
        if (!is_array($ids)) {
            throw new \InvalidArgumentException('Parameter `ids` passed to EntityFetcher::getEntities must be an array.');
        }

        $class = $this->verifier->verifyClass($class);
        if ($class) {
            $qb = $this->em->getRepository($class)->createQueryBuilder('entity');
            $qb->andWhere($qb->expr()->in('entity.id', ':ids'));
            $qb->setParameter('ids', $ids);
            $entities = $qb->getQuery()->getResult();
            if ($entities) {
                return $entities;
            } else {
                return;
            }
        } else {
            return;
        }
    }

    public function getAll($class)
    {
        $class = $this->verifier->verifyClass($class);
        if ($class) {
            $qb       = $this->em->getRepository($class)->createQueryBuilder('entity');
            $entities = $qb->getQuery()->getResult();
            if ($entities) {
                return $entities;
            } else {
                return;
            }
        } else {
            return;
        }
    }
}
