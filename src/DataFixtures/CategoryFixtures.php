<?php

namespace App\DataFixtures;

use Faker\Factory;
use Faker\Generator;
use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture 
{
    /**
     * @var Generator
     */
    private Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        for ($categoryCount = 1; $categoryCount <= 5; $categoryCount++) {
            
            
            $category = new Category();
            $category->setName($this->faker->sentence(1));

            $this->addReference('category_' . $categoryCount, $category);
            

            $manager->persist($category);
        }

        $manager->flush();
    }

   
}
