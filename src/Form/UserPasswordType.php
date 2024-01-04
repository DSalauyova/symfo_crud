<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class UserPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'plainPassword',
                PasswordType::class,
                [
                    'attr' => ['class' => 'form-control '],
                    'label' => 'Old Password',
                    'label_attr' => ['class' => 'form-label mt-4']
                ]
            )
            ->add(
                'newPassword',
                RepeatedType::class,
                [
                    'type' => PasswordType::class,
                    'first_options' => [
                        'label' => 'New Password',
                        'attr' => ['class' => 'form-control '],
                        'label_attr' => [
                            'class' => 'form-label mt-4'
                        ],
                        'constraints' => [new Assert\Notblank()]
                    ],
                    'second_options' => [
                        'label' => 'Confirm the new password',
                        'attr' => ['class' => 'form-control'],
                        'label_attr' => [
                            'class' => 'form-label mt-4'
                        ],
                    ],
                    'invalid_message' => 'Passwords do not match'
                ]
            )
            ->add(
                'submit',
                SubmitType::class,
                [
                    'attr' => ['class' => 'btn btn-primary mt-4'],
                    'label' => 'Confirm modification'
                ]
            );
    }
}
