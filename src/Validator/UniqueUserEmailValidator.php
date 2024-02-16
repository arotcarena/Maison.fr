<?php

namespace App\Validator;

use App\Repository\UserRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueUserEmailValidator extends ConstraintValidator
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    public function validate($value, Constraint $constraint)
    {
        /* @var App\Validator\UniqueUserEmail $constraint */

        if (null === $value || '' === $value) {
            return;
        }

        if($this->userRepository->findOneBy(['email' => $value]))
        {
            $this->context->buildViolation($constraint->message)
                        ->setParameter('{{ value }}', $value)
                        ->addViolation();
        }
    }
}
