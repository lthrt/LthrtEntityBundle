<?php

namespace Lthrt\EntityBundle\Tests\Controller;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Lthrt\EntityBundle\Tests\Entity\GenericJsonEntity;

class TestGenericJsonEntityFixture extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * Set loading order.
     *
     * @return int
     */
    public function getOrder()
    {
        return 1;
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $manager->getClassMetadata(GenericJsonEntity::class)->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);

        $generic1        = new GenericJsonEntity();
        $generic1->name  = 'test';
        $generic1->fluff = 'fluff';
        $generic1->id    = 1;
        $this->addReference('_reference_LthrtEntityBundle_genericjson1', $generic1);
        $manager->persist($generic1);

        $generic2        = new GenericJsonEntity();
        $generic2->name  = 'check';
        $generic2->fluff = 'stuff';
        $generic2->id    = 2;
        $this->addReference('_reference_LthrtEntityBundle_genericjson2', $generic2);
        $manager->persist($generic2);

        $manager->flush();
    }
}
