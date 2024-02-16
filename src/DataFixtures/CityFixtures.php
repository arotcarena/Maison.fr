<?php

namespace App\DataFixtures;

use App\Entity\City;
use App\Helper\Slugator;
use App\Config\CityConfig;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;

class CityFixtures extends Fixture
{
    private SluggerInterface $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager): void
    {
        $i = 0;
        foreach(CityConfig::LIST as $postalCode => $name)
        {
            $i++;
            $city = (new City)
                        ->setName($name)
                        ->setSlug(strtolower($this->slugger->slug($name)))
                        ->setPostalCode($postalCode)
                        ->setDepartmentCode(substr($postalCode, 0, 2))
                        ;
            $manager->persist($city);
            $this->addReference('city'.$i, $city);
        }
        $manager->flush();
    }
}
