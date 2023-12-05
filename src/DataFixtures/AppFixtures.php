<?php

namespace App\DataFixtures;

use App\Entity\Ingredient;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator as FakerGenerator;
//use Generator;

class AppFixtures extends Fixture
{

    /**
     * 
     * @var $faker Generator
     */
    private FakerGenerator $faker;
    public function __construct(){
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        //pour avoir 50 ingredients de 1 à 50
        for ($i=1; $i <= 50; $i++) { 
            $ingredient = new Ingredient();
            $ingredient -> setName($this->faker->word())
                -> setPrice(rand(1,100))
                -> setQuantity(rand(1,10));
            $manager->persist($ingredient);
        }
        $manager->flush();
    }
}
