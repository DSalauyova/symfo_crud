<?php

namespace App\Form;

use App\Entity\Ingredient;
use App\Entity\Recipe;
use App\Repository\IngredientRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class RecipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'name',
                TextType::class,
                [
                    'attr' => [
                        'class' => 'form-control',
                        'minlength' => 2,
                        'maxlength' => 50,
                    ],
                    'label' => 'Name',
                    'label_attr' => [
                        'class' => 'form-label mt-6'
                    ],
                    'constraints' => [
                        new Assert\Length(min: 2, max: 50),
                        new Assert\NotBlank()
                    ]
                ]
            )
            ->add(
                'time',
                IntegerType::class,
                [
                    'attr' => [
                        'class' => 'form-control',
                        'minlength' => 1,
                        'maxlength' => 1440,
                    ],
                    'label' => 'Time (in minutes)',
                    'label_attr' => [
                        'class' => 'form-label mt-6'
                    ],
                    'constraints' => [
                        new Assert\Positive(),
                        new Assert\LessThan(1441)
                    ]
                ]
            )
            ->add(
                'person_nb',
                IntegerType::class,
                [
                    'attr' => [
                        'class' => 'form-control',
                        'minlength' => 1,
                        'maxlength' => 50,
                    ],
                    'label' => 'Number of persons',
                    'label_attr' => [
                        'class' => 'form-label mt-6'
                    ],
                    'constraints' => [
                        new Assert\Positive(),
                        new Assert\LessThan(51)
                    ]
                ]
            )
            ->add(
                'difficulty',
                RangeType::class,
                [
                    'attr' => [
                        'class' => 'form-range',
                        'min' => 1,
                        'max' => 5,
                    ],
                    'label' => 'Difficulty',
                    'label_attr' => [
                        'class' => 'form-label mt-6'
                    ],
                    'constraints' => [
                        new Assert\Positive(),
                        new Assert\LessThan(6)
                    ]
                ]
            )
            ->add(
                'description',
                TextareaType::class,
                [
                    'attr' => [
                        'class' => 'form-control',
                        'minlength' => 15,
                        'maxlength' => 1500,
                    ],
                    'label' => 'Description',
                    'label_attr' => [
                        'class' => 'form-label mt-6'
                    ],
                    'constraints' => [
                        new Assert\NotBlank(),
                    ]
                ]
            )
            ->add(
                'price',
                MoneyType::class,
                [
                    'attr' => [
                        'class' => 'form-control',
                    ],
                    'label' => 'Price',
                    'label_attr' => [
                        'class' => 'form-label mt-6'
                    ],
                    'constraints' => [
                        new Assert\LessThan(1001)
                    ]
                ]
            )
            ->add(
                'isFavorite',
                CheckboxType::class,
                [
                    'attr' => [
                        'class' => 'form-check-input'
                    ],
                    'required' => false,
                    'label' => 'Favorits',
                    'label_attr' => [
                        'class' => 'form-check-label'
                    ],
                    'constraints' => [
                        new Assert\NotNull()
                    ]
                ]
            )
            ->add(
                'ingredients',
                EntityType::class,
                [
                    'attr' => [
                        'class' => 'form-control'
                    ],
                    'label' => 'Ingredients',
                    'class' => Ingredient::class,
                    'query_builder' => function (IngredientRepository $rep) {
                        return $rep->createQueryBuilder('i')
                            ->orderBy('i.name', 'ASC');
                    },
                    'choice_label' => 'name',
                    'multiple' => 'true',
                    'expanded' => 'true'
                ]
            )
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-info mt-4'
                ],
                'label' => 'Create my recipe'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
        ]);
    }
}
