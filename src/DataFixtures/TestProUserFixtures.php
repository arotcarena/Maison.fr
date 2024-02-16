<?php
namespace App\DataFixtures;

use App\Entity\Pro;
use App\Entity\User;
use App\Config\CityConfig;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class TestProUserFixtures extends Fixture
{
    public const PRO_USER_1_EMAIL = 'pat-macon@gmail.com';

    public const PRO_USER_2_EMAIL = 'jean-travailleur@gmail.com';


    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager)
    {
        $pro1 = (new Pro)
                ->setEmail(self::PRO_USER_1_EMAIL)
                ->setBusinessName('Patrick Maçonnerie')
                ->setContactName('Patrick Lacaze')
                ->setCity($this->getReference('city'. random_int(1, CityConfig::COUNT)))
                ->setPhone('0625554557')
                ->addCategory($this->getReference('category1'))
                ->addCategory($this->getReference('category5'))
                ->setDepartments('64, 31, 32, 40')
                ;

        $user1 = new User;
        $user1->setEmail(self::PRO_USER_1_EMAIL)
                ->setPassword($this->hasher->hashPassword($user1, 'password'))
                ->setConfirmed(true)
                ;

        $pro1->setUser($user1);
        
        $manager->persist($user1, $pro1);


        $pro2 = (new Pro)
                ->setEmail(self::PRO_USER_2_EMAIL)
                ->setBusinessName('Jean Travailleur')
                ->setContactName('Jean Gazon')
                ->setCity($this->getReference('city'. random_int(1, CityConfig::COUNT)))
                ->setPhone('0622521211')
                ->addCategory($this->getReference('category4'))
                ->addCategory($this->getReference('category2'))
                ->setDepartments('75, 64')
                ;

        $user2 = new User;
        $user2->setEmail(self::PRO_USER_2_EMAIL)
                ->setPassword($this->hasher->hashPassword($user2, 'password'))
                ->setConfirmed(true)
                ;

        $pro2->setUser($user2);

        
        $manager->persist($user2, $pro2);


        $manager->flush();
    }
}