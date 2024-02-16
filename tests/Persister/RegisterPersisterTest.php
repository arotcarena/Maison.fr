<?php
namespace App\Tests\Persister;

use App\DataFixtures\CategoryFixtures;
use App\DataFixtures\CityFixtures;
use App\Entity\Pro;
use App\Entity\City;
use App\Entity\User;
use App\Entity\Category;
use App\Form\DataModel\Register;
use App\Persister\RegisterPersister;
use App\Repository\CategoryRepository;
use App\Repository\CityRepository;
use App\Repository\ProRepository;
use App\Repository\UserRepository;
use App\TestServices\PasswordHasherHelper;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use PHPUnit\Framework\MockObject\Builder\InvocationMocker;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

class RegisterPersisterTest extends KernelTestCase
{
    public function testFlushIsDone()
    {
        $em = $this->createMock(EntityManagerInterface::class);
        $registerPersister = new RegisterPersister($em, $this->createMock(UserPasswordHasherInterface::class));
        
        $em->expects($this->once())
            ->method('flush')
            ;
        $registerPersister->persist($this->createRegister());
    }

    public function testUserIsCorrectlyPersisted()
    {
        $unique_email = $this->getUniqueEmail();
        $this->getRegisterPersister()->persist(
            $this->createRegister()->setEmail($unique_email)
                                    ->setPassword('test_password')
        );

        $user = $this->findUserByEmail($unique_email);
        $this->assertTrue($user !== null, 'Le User n\'est pas persisté du tout !');
        $this->assertTrue(password_verify('test_password', $user->getPassword()) , 'Le mot de passe du User n\'est pas correctement haché');
    }

    public function testProIsCorrectlyPersistedAndRelationnedToUser()
    {
        $unique_email = $this->getUniqueEmail();
        $this->getRegisterPersister()->persist(
            $this->createRegister()->setEmail($unique_email)
                                    ->setBusinessName('test_businessName')
                                    ->setPhone('0690807060')
        );

        $user = $this->findUserByEmail($unique_email);
        $pro = $user->getPro();
        $this->assertTrue($pro !== null, 'Le Pro n\'est pas persisté du tout !');
        $this->assertSame('test_businessName', $pro->getBusinessName(), 'Le businessName du pro n\'est pas correctement enregistré');
        $this->assertSame('0690807060', $pro->getPhone(), 'Le numéro de téléphone du pro n\'est pas correctement enregistré');
    }

    private function getUniqueEmail(): string 
    {
        return 'test'. random_int(0, 1000000) .'@mail.com';
    }

    private function getRegisterPersister(): RegisterPersister
    {
        return self::getContainer()->get(RegisterPersister::class);
    }

    private function findUserByEmail(string $email): ?User
    {
        return self::getContainer()->get(UserRepository::class)->findOneBy(['email' => $email]);
    }


    private function createRegister():Register
    {
        self::getContainer()->get(DatabaseToolCollection::class)->get()->loadFixtures([
            CategoryFixtures::class, 
            CityFixtures::class
        ]);
        $city = self::getContainer()->get(CityRepository::class)->findAll()[0];
        $category = self::getContainer()->get(CategoryRepository::class)->findAll()[0];
        return (new Register)
                    ->setEmail('email@email.com')
                    ->setPassword('password')
                    ->setPasswordConfirm('password')
                    ->setBusinessName('Entreprise du batiment')
                    ->setContactName('Jean Contact')
                    ->setCategories(new ArrayCollection([$category]))
                    ->setCity($city)
                    ->setPhone('0612131415')
                    ->setDepartments(['13', '64', '40'])
                    ;
    }

}