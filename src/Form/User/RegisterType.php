<?php

declare(strict_types=1);

namespace App\Form\User;

use MsgPhp\User\Infrastructure\Form\Type\HashedPasswordType;
use MsgPhp\User\Infrastructure\Validator\UniqueUsername;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

final class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'constraints' => [new NotBlank(), new Email(), new UniqueUsername()],
            ])
            ->add('password', HashedPasswordType::class, [
                'password_confirm' => false,
                'password_options' => ['constraints' => new NotBlank()],
            ])
            ->add('name', TextType::class, [
                'constraints' => [new NotBlank()],
            ])
//            ->add('lastName', TextType::class, [
//                'constraints' => [new NotBlank()],
//            ])
            ->add('travelAgency', TextType::class, [
                'constraints' => [new NotBlank()],
            ])
            ->add('enterprise', TextType::class, [
                'constraints' => [new NotBlank()],
            ])
            ->add('country', TextType::class, [
                'constraints' => [new NotBlank()],
            ])
            ->add('web', TextType::class, ['required' => false])
        ;
    }
}
