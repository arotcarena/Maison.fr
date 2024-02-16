<?php
namespace App\DataFixtures;

use App\Config\CategoryConfig;
use App\Entity\Pro;
use App\Entity\City;
use App\Config\CityConfig;
use App\Config\DepartmentConfig;
use App\DataFixtures\CategoryFixtures;
use App\Entity\Category;
use App\Entity\Picture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ProFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies()
    {
        return [
            CityFixtures::class, 
            CategoryFixtures::class
        ];
    }
    public function load(ObjectManager $manager)
    {
        for ($a=0; $a < 10; $a++) 
        { 
            $pro = (new Pro)
                    ->setEmail('macon'.$a.'@gmail.com')
                    ->setBusinessName('Maçonnerie n° '.$a)
                    ->setContactName('maçon n° '.$a)
                    ->setCity($this->getReference('city'. random_int(1, CityConfig::COUNT)))
                    ->setPhone('0123456789')
                    ->addCategory($this->getReference('category1'))
                    ->addCategory($this->getReference('category5'))
                    ->setDepartments('64, 31, 32, 40')
                    ;

            for ($i=1; $i <= 7; $i++) { 
                $pro->addPicture((new Picture)->setFileName('maçonnerie/'.$i.'.jpg'));
            }
            $manager->persist($pro);
        }

        //maçon

        $pro = (new Pro)
                ->setEmail('pat-macon@gmail.com')
                ->setBusinessName('Patrick Maçonnerie')
                ->setContactName('Patrick Lacaze')
                ->setCity($this->getReference('city'. random_int(1, CityConfig::COUNT)))
                ->setPhone('0625554557')
                ->addCategory($this->getReference('category1'))
                ->addCategory($this->getReference('category5'))
                ->setDepartments('64, 31, 32, 40')
                ;

        for ($i=1; $i <= 7; $i++) { 
            $pro->addPicture((new Picture)->setFileName('maçonnerie/'.$i.'.jpg'));
        }
        $manager->persist($pro);


        //peintre

        $pro = (new Pro)
                ->setEmail('bernard-peinture@gmail.com')
                ->setBusinessName('Bernard Peinture')
                ->setContactName('Bernard Pinto')
                ->setCity($this->getReference('city'. random_int(1, CityConfig::COUNT)))
                ->setPhone('0685504575')
                ->addCategory($this->getReference('category2'))
                ->addCategory($this->getReference('category3'))
                ->addCategory($this->getReference('category4'))
                ->addCategory($this->getReference('category7'))
                ->addCategory($this->getReference('category1'))
                ->setDepartments('75, 95, 91, 78, 64')
                ;

        for ($i=1; $i <= 7; $i++) { 
            $pro->addPicture((new Picture)->setFileName('peinture_intérieure/'.$i.'.jpg'));
        }
        $manager->persist($pro);


        //charpentier-couvreur

        $pro = (new Pro)
                ->setEmail('jean-claude@gmail.com')
                ->setBusinessName('Renov Toiture')
                ->setContactName('Jean-Claude Rouget')
                ->setCity($this->getReference('city'. random_int(1, CityConfig::COUNT)))
                ->setPhone('0677544445')
                ->addCategory($this->getReference('category8'))
                ->addCategory($this->getReference('category9'))
                ->addCategory($this->getReference('category10'))
                ->addCategory($this->getReference('category11'))
                ->addCategory($this->getReference('category12'))
                ->setDepartments('13, 84, 83, 64')
                ;

        for ($i=1; $i <= 7; $i++) { 
            $pro->addPicture((new Picture)->setFileName('couverture_tuile/'.$i.'.jpg'));
        }
        $manager->persist($pro);



        $manager->flush();
    }

    
}