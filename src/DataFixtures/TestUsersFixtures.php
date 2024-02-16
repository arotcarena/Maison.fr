<?php
namespace App\DataFixtures;

use App\Entity\User;
use App\Helper\Code2FAGenerator;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

class TestUsersFixtures extends Fixture
{
    public const USER_EMAIL = 'user@mail.com';
    public const USER_CONFIRMED_EMAIL = 'user-confirmed@mail.com';
    public const USER_CONFIRMED_WITH_2FA_EMAIL = 'user-confirmed-with-2fa@mail.com';
    public const USER_CONFIRMED_WITH_2FA_AND_CODE_RECEIVED_EMAIL = 'user-confirmed-with-2fa-and-code-received@mail.com';

    public const PASSWORD = 'password';

    public const CODE_2FA = '123456';


    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager)
    {
        /** User not confirmed */
        $user = new User;
        $user->setEmail(self::USER_EMAIL)
                ->setPassword($this->hasher->hashPassword($user, self::PASSWORD))
                ;
        $manager->persist($user);

        /** User confirmed */
        $user = new User;
        $user->setEmail(self::USER_CONFIRMED_EMAIL)
                ->setPassword($this->hasher->hashPassword($user, self::PASSWORD))
                ->setConfirmed(true)
                ;
        $manager->persist($user);

        /** User confirmed with 2FA */
        $user = new User;
        $user->setEmail(self::USER_CONFIRMED_WITH_2FA_EMAIL)
                ->setPassword($this->hasher->hashPassword($user, self::PASSWORD))
                ->setConfirmed(true)
                ->setChoice2FA(true)
                ;
        $manager->persist($user);

        /** User confirmed with 2FA and code received */
        $user = new User;
        $user->setEmail(self::USER_CONFIRMED_WITH_2FA_AND_CODE_RECEIVED_EMAIL)
                ->setPassword($this->hasher->hashPassword($user, self::PASSWORD))
                ->setConfirmed(true)
                ->setChoice2FA(true)
                ->setCode2FA(self::CODE_2FA)
                ;
        $manager->persist($user);



        $manager->flush();
    }
}