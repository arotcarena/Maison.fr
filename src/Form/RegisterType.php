<?php

namespace App\Form;

use App\Entity\City;
use App\Entity\Category;
use App\Config\DepartmentConfig;
use App\Form\DataModel\Register;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('password', PasswordType::class, [
                'always_empty' => false
            ])
            ->add('passwordConfirm', PasswordType::class, [
                'always_empty' => false
            ])
            ->add('businessName')
            ->add('contactName')
            ->add('phone')
            ->add('imageFiles', FileType::class, [
                'multiple' => true,
                'required' => false
            ])
            ->add('categories', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'multiple' => true
            ])
            ->add('city', EntityType::class, [
                'class' => City::class,
                'choice_label' => 'name'
            ])
            ->add('departments', ChoiceType::class, [
                'choices' => DepartmentConfig::LIST,
                'multiple' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Register::class,
        ]);
    }
}
