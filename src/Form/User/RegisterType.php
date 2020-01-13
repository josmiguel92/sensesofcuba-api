<?php

declare(strict_types=1);

namespace App\Form\User;

use App\Entity\User;
use MsgPhp\User\Infrastructure\Form\Type\HashedPasswordType;
use MsgPhp\User\Infrastructure\Validator\UniqueUsername;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
            ->add('_token', null, ['required' => true])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([

            // enable/disable CSRF protection for this form
            'csrf_protection' => false,
            // the name of the hidden HTML field that stores the token
//            'csrf_field_name' => '_token',
            // an arbitrary string used to generate the value of the token
            // using a different string for each form improves its security
//            'csrf_token_id'   => 'register',
        ]);
    }
}
