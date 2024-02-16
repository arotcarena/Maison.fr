<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Helper\Slugator;
use App\Config\CategoryConfig;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategoryFixtures extends Fixture
{
    private SluggerInterface $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }
    public function load(ObjectManager $manager): void
    {
        $i = 0;
        foreach(CategoryConfig::LIST as $category_name)
        {
            $i++;
            $category = (new Category)
                        ->setName($category_name)
                        ->setSlug(strtolower($this->slugger->slug($category_name)))
                        ;
            $manager->persist($category);
            $this->addReference('category'.$i, $category);
        }


        $manager->flush();
    }
}
