<?php

declare(strict_types=1);

namespace App\Form\User;

use App\Entity\User;
use App\Entity\UsersGroup;
use App\Repository\UsersGroupRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Unique;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class RegisterType extends AbstractType
{


    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('email', EmailType::class, [
                'constraints' => [new NotBlank(), new Email()],
            ])
            ->add('password', TextType::class,
//                [
//                'constraints' => new NotBlank(), new Length(['min' => 6])
//            ]
            )
            ->add('name', TextType::class, [
                'constraints' => [new NotBlank()],
            ])
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

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
