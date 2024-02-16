<?php
namespace App\Tests\Validator;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Validator\UniqueUserEmail;
use Doctrine\ORM\EntityManagerInterface;
use App\Validator\UniqueUserEmailValidator;
use App\Tests\Controller\AuthenticationTrait;
use App\DataFixtures\TestFixtures\UserTestFixtures;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class UniqueUserEmailValidatorTest extends KernelTestCase
{
    use AuthenticationTrait;

    private ContainerInterface $container;

    public function setUp():void
    {
        $this->container = static::getContainer();
        $this->userRepository = $this->container->get(UserRepository::class);
    }

    public function testOriginalUserEmailValidation()
    {
        $this->loadTestUserFixtures($this->container);

        $validator = new UniqueUserEmailValidator($this->userRepository);
        $context = $this->createMock(ExecutionContextInterface::class);
        $validator->initialize($context);
        $context->expects($this->never())
                ->method('buildViolation')
                ;
        $validator->validate('original@email.fr', new UniqueUserEmail);
    }
    public function testExistingUserEmailValidation()
    {
        $this->loadTestUserFixtures($this->container);
        $existingEmail = $this->getTestUser($this->container)->getEmail();

        $validator = new UniqueUserEmailValidator($this->userRepository);
        $context = $this->createMock(ExecutionContextInterface::class);
        $validator->initialize($context);
        $context->expects($this->once())
                ->method('buildViolation')
                ;
        $validator->validate($existingEmail, new UniqueUserEmail);
    }
}