<?php

namespace App\DataFixtures;

use App\Entity\Ingredient;
use App\Entity\Recipe;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator as FakerGenerator;

class AppFixtures extends Fixture
{

    /**
     * 
     * @var $faker Generator
     */
    private FakerGenerator $faker;
    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        //pour avoir 50 ingredients de 1 à 50
        //attention, ce for est changé apres l'ajout d'entité Recipe. on va stocker les données(ingredients) dans un tableau pour les avoir ensuite dans les recettes (ci-dessous)
        //tableau vide
        $ingredients = [];
        for ($i = 1; $i <= 20; $i++) {
            $ingredient = new Ingredient();
            $ingredient->setName($this->faker->word())
                ->setPrice(rand(1, 100))
                ->setQuantity(rand(1, 10));
            //a chaque tour de la boucle, on rajoute un element (un ingredient) au tableau
            $ingredients[] = $ingredient;
            //persiste les données
            $manager->persist($ingredient);
        }
        //envoi de données en une fois
        $manager->flush();

        //10 recipes
        for ($x = 1; $x <= 10; $x++) {
            $recipe = new Recipe();
            $recipe->setName($this->faker->word())
                //génère un nombre aléatoire qui peut être soit 0, soit 1. Cela crée une condition booléenne qui équivaut à "true" avec une probabilité de 50% et "false" avec une probabilité de 50%)
                //mt_rand utilise l'algorithme Mersenne Twister
                ->setTime(mt_rand(0, 1) == 1 ? mt_rand(1, 1440) : null)
                ->setPersonNb(mt_rand(0, 1) == 1 ? mt_rand(1, 50) : null)
                ->setDifficulty(mt_rand(0, 1) == 1 ? mt_rand(1, 5) : null)
                ->setDescription($this->faker->text(255))
                ->setPrice($this->faker->randomFloat(2, 1, 100))
                ->setIsFavorite(mt_rand(0, 1) == 1 ? true : false);
            //entre 1 et 11 ingredients possible par recette
            for ($y = 0; $y < mt_rand(1, 11); $y++) {
                //ajouter dans la recette deux minimum ou plusieurs ingredient du tab des ingredients (au-dessus)
                $recipe->addIngredient($ingredients[mt_rand(2, count($ingredients) - 1)]);
            }
            // ->setCreatedAt(DateTimeImmutable::createFromMutable($this->faker->dateTimeThisDecade()))
            // ->setUpdatedAt(DateTimeImmutable::createFromMutable($this->faker->dateTimeThisDecade()));
            $manager->persist($recipe);
        }

        //users
        for ($m = 0; $m <= 10; $m++) {
            $user = new User();
            $user->setUsername($this->faker->userName())
                ->setEmail($this->faker->email())
                ->setAgentNumber($this->faker->numberBetween(3000, 4000))
                ->setRoles([("basic-user")])
                //mdp de base
                ->setPlainPassword('password');
            //a ce moment il va faire appel a la doctrine qui elle appellera le listener dans App\EntityListener: resource: ".. EntityListener - in security.yaml. Il va voir qu'il ya encode password 
            $manager->persist($user);
        }
        $manager->flush();
    }
}
