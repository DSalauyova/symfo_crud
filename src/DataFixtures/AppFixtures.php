<?php

namespace App\DataFixtures;

use App\Entity\Ingredient;
use App\Entity\Rating;
use App\Entity\Recipe;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator as FakerGenerator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    /**
     * 
     * @var $faker Generator
     */
    private FakerGenerator $faker;
    /**
     * $hasher imported
     * @var UserPasswordHasherInterface
     */
    private UserPasswordHasherInterface $hasher;
    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->faker = Factory::create('fr_FR');
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        //users
        $users = [];
        for ($m = 0; $m <= 20; $m++) {
            $user = new User();
            $hashedPassword = $this->hasher->hashPassword(
                $user,
                'welcome'
            );
            $user->setUserName($this->faker->name())
                ->setEmail($this->faker->email())
                ->setAgentNumber($this->faker->numberBetween(3000, 4000))
                ->setRoles([("basic-user")])
                ->setPassword($hashedPassword);
            //a ce moment il va faire appel a la doctrine qui elle appellera le listener dans App\EntityListener: resource: ".. EntityListener - in security.yaml. Il va voir qu'il ya encode password 
            $users[] = $user;
            $manager->persist($user);
        }

        //pour avoir 50 ingredients de 1 à 50
        //attention, ce for est changé apres l'ajout d'entité Recipe. on va stocker les données(ingredients) dans un tableau pour les avoir ensuite dans les recettes (ci-dessous)
        //tableau vide
        $ingredients = [];
        for ($i = 1; $i <= 80; $i++) {
            $ingredient = new Ingredient();
            $ingredient->setName($this->faker->word())
                ->setPrice(rand(1, 100))
                ->setQuantity(rand(1, 10))
                //prendre un user au hasard
                ->setUser($users[mt_rand(0, count($users) - 1)]);
            //a chaque tour de la boucle, on rajoute un element (un ingredient) au tableau
            $ingredients[] = $ingredient;
            //persiste les données
            $manager->persist($ingredient);
        }

        // recipes
        $recipes = [];
        for ($x = 1; $x <= 28; $x++) {
            $recipe = new Recipe();
            $recipe->setName($this->faker->word())
                //génère un nombre aléatoire qui peut être soit 0, soit 1. Cela crée une condition booléenne qui équivaut à "true" avec une probabilité de 50% et "false" avec une probabilité de 50%)
                //mt_rand utilise l'algorithme Mersenne Twister
                ->setTime(mt_rand(0, 1) == 1 ? mt_rand(1, 1440) : null)
                ->setPersonNb(mt_rand(0, 1) == 1 ? mt_rand(1, 12) : null)
                ->setDifficulty(mt_rand(0, 1) == 1 ? mt_rand(1, 5) : null)
                ->setDescription($this->faker->text(255))
                ->setPrice($this->faker->randomFloat(2, 1, 100))
                ->setIsFavorite(mt_rand(0, 1) == 1 ? true : false)
                ->setIsPublic(mt_rand(0, 1) == 1 ? true : false)
                ->setUser($users[mt_rand(0, count($users) - 1)]);
            //entre 1 et 11 ingredients possible par recette
            for ($y = 0; $y < mt_rand(1, 11); $y++) {
                //ajouter dans la recette deux minimum ou plusieurs ingredient du tab des ingredients (au-dessus)
                $recipe->addIngredient($ingredients[mt_rand(2, count($ingredients) - 1)]);
            }
            $recipes[] = $recipe;
            $manager->persist($recipe);
        }
        /**
         * Rating
         * A chaque boucle de recettes insertion dans la nouvelle table Rating : 
         * - un nouveau objet rating(note) est crée (de 1 a 5 random)
         * - un user est attribué (du premier au dernier)
         * - la recette courante du boucle (inserée dans la table)
         * 
         */
        foreach ($recipes as $recipe) {
            for ($k = 0; $k < mt_rand(0, 4); $k++) {
                $rating = new Rating;
                $rating
                    ->setRate(mt_rand(1, 5))
                    ->setUser($users[mt_rand(0, count($users) - 1)])
                    ->setRecipe($recipe);
                $manager->persist($rating);
            }
        }
        //envoi de données en une fois
        $manager->flush();
    }
}
