<?php

namespace App\Form;

use App\Entity\Rating;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RatingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'rate',
                ChoiceType::class,
                [
                    'choices' => [
                        '1' => 1,
                        '2' => 2,
                        '3' => 3,
                        '4' => 4,
                        '5' => 5
                    ],
                    'attr' => [
                        'class' => 'form-select'
                    ],
                    'label' => 'Rate this recipe',
                    'label_attr' => [
                        'class' => 'form-label mt-4'
                    ]
                ]
            )
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary btn-sm mt-4'
                ],
                'label' => 'Push !'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Rating::class,
        ]);
    }
}
