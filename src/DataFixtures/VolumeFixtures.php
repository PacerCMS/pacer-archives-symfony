<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use App\Entity\Volume;
use App\Entity\Issue;

class VolumeFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $volume = new Volume();
        $volume->setVolumeNumber('1');
        $volume->setVolumeStartDate(new \DateTime('1928-08-01'));
        $volume->setVolumeEndDate(new \DateTime('1929-07-31'));
        $volume->setNameplateKey('volette');
        $manager->persist($volume);

        $manager->flush();
    }
}
