<?php

namespace Lthrt\EntityBundle\Tests\Controller;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Lthrt\EntityBundle\Tests\Entity\GenericLoggedEntity;

class TestGenericLoggedEntityFixture extends AbstractFixture implements OrderedFixtureInterface
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
        $manager->getClassMetadata(GenericLoggedEntity::class)->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);

        $generic1        = new GenericLoggedEntity();
        $generic1->name  = 'Test';
        $generic1->fluff = 'Fluff';
        $generic1->id    = 1;
        $this->addReference('_reference_LthrtEntityBundle_genericlogged1', $generic1);
        $manager->persist($generic1);
        $manager->flush();

        $generic1->fluff = 'More Fluff';
        $manager->flush();

        $generic1->fluff = 'MOAR Fluff';
        $manager->flush();

        $generic1->fluff = 'MOAR cuz MOAR Fluff';
        $manager->flush();

        $generic2        = new GenericLoggedEntity();
        $generic2->name  = 'Check';
        $generic2->fluff = 'Stuff';
        $generic2->id    = 2;
        $this->addReference('_reference_LthrtEntityBundle_genericlogged2', $generic2);
        $manager->persist($generic2);
        $manager->flush();

        $generic2->fluff = 'More Stuff';
        $manager->flush();

        $generic2->fluff = 'MOAR Stuff';
        $manager->flush();

        $generic2->fluff = 'MOAR cuz MOAR Stuff';
        $manager->flush();
    }
}
