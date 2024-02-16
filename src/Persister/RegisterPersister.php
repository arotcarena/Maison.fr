<?php
namespace App\Persister;

use App\Entity\Pro;
use App\Entity\User;
use App\Form\DataModel\Register;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class RegisterPersister
{
    private EntityManagerInterface $em;
    private UserPasswordHasherInterface $hasher;
    private TokenGeneratorInterface $tokenGenerator;

    public function __construct(EntityManagerInterface $em, UserPasswordHasherInterface $hasher, TokenGeneratorInterface $tokenGenerator)
    {
        $this->em = $em;
        $this->hasher = $hasher;
        $this->tokenGenerator = $tokenGenerator;
    }
    public function persist(Register $register):User
    {
        $user = new User;
        $user
            ->setEmail($register->getEmail())
            ->setPassword($this->hasher->hashPassword($user, $register->getPassword()))
            ;
        $this->em->persist($user);


        $pro = (new Pro)
                ->setEmail($register->getEmail())
                ->setBusinessName($register->getBusinessName())
                ->setContactName($register->getContactName())
                ->setPhone($register->getPhone())
                ->setCity($register->getCity())
                ->setCategories($register->getCategories())
                ->setPictures(new ArrayCollection(($register->getPictures())))
                ->setDepartments(implode(', ', $register->getDepartments()))
                ->setUser($user)
                ;
        $this->em->persist($pro);


        $this->em->flush();

        return $user;
    }
}