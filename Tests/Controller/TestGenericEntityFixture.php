<?php

namespace Lthrt\EntityBundle\Tests\Controller;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Lthrt\EntityBundle\Tests\Entity\GenericEntity;

class TestGenericEntityFixture extends AbstractFixture implements OrderedFixtureInterface
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
        $manager->getClassMetadata(GenericEntity::class)->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);

        $generic1        = new GenericEntity();
        $generic1->name  = 'Test';
        $generic1->fluff = 'Fluff';
        $generic1->id    = 1;
        $this->addReference('_reference_LthrtEntityBundle_generic1', $generic1);
        $manager->persist($generic1);

        $generic2        = new GenericEntity();
        $generic2->name  = 'Check';
        $generic2->fluff = 'Stuff';
        $generic2->id    = 2;
        $this->addReference('_reference_LthrtEntityBundle_generic2', $generic2);
        $manager->persist($generic2);

        $manager->flush();
    }
}
