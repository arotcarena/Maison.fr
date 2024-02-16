<?php

namespace App\Validator;

use App\Repository\UserRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ExistingEmailValidator extends ConstraintValidator
{
    public function __construct(
        private UserRepository $userRepository
    )
    {
    }

    public function validate($value, Constraint $constraint)
    {
        /* @var App\Validator\ExistingEmail $constraint */

        if (null === $value || '' === $value) {
            return;
        }

        
        if(!$this->userRepository->findOneBy(['email' => $value]))
        {
            $this->context->buildViolation($constraint->message)
                        ->setParameter('{{ value }}', $value)
                        ->addViolation();
        }

        
    }
}
