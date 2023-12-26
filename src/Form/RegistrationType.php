<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'username',
                TextType::class,
                [
                    'attr' => [
                        'class' => 'form-control',
                        'minlength' => 2,
                        'maxlength' => 80,
                    ],
                    'label' => 'Login',
                    'label_attr' => [
                        'class' => 'form-label mt-4'
                    ],
                    'constraints' => [
                        new Assert\Length(min: 2, max: 80),
                        new Assert\NotBlank()
                    ]
                ]
            )
            ->add(
                'agent_number',
                IntegerType::class,
                [
                    'attr' => [
                        'class' => 'form-control'
                    ],
                    'label' => 'Agent Number',
                    'label_attr' => [
                        'class' => 'form-label mt-4'
                    ],
                    'constraints' => [
                        new Assert\NotBlank(),
                        new Assert\Length(['min' => 4, 'max' => 4]),
                        new Assert\Positive()
                    ]
                ]

            )
            ->add(
                'email',
                EmailType::class,
                [
                    'attr' => [
                        'class' => 'form-control',
                        'minlength' => 5,
                        'maxlength' => 180,
                    ],
                    'label' => 'Email',
                    'label_attr' => [
                        'class' => 'form-label mt-4'
                    ],
                    'constraints' => [
                        new Assert\Length(min: 5, max: 180)
                    ]
                ]
            )
            ->add(
                'plainPassword',
                RepeatedType::class,
                [
                    'type' => PasswordType::class,
                    'first_options' => [
                        'label' => 'Password',
                        'attr' => ['class' => 'form-control '],
                        'label_attr' => [
                            'class' => 'form-label mt-4'
                        ],
                    ],

                    'second_options' => [
                        'label' => 'Confirm the password',
                        'attr' => ['class' => 'form-control'],
                        'label_attr' => [
                            'class' => 'form-label mt-4'
                        ],
                    ],
                    'invalid_message' => 'Passwords do not match'
                ]
            )
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-info mt-4'
                ],
                'label' => 'Submit'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class, [
                'attr' => [
                    'class' => 'btn btn-primary'
                ]
            ]
        ]);
    }
}
