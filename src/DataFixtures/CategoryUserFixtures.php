<?php

namespace App\DataFixtures;

use Faker\Factory;
use Faker\Generator;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;


class CategoryUserFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 50; $i++) {
            $category = $this->getReference('category_' . $this->$faker->numberBetween(1,5));

            $user = new User();
            $user->setEmail($this->faker->email);
            $user->setCreatedAt($this->faker->dateTimeBetween('-6 months'));
            $user->setIsRgpd($this->faker->boolean);
            $user->setValidationToken($this->faker->uuid);
            $user->setIsValid($this->faker->boolean);
            $user->setCategory($category);
                $this->addReference('subscriber_' .$i, $user);

             $manager->persist($user);
            }

        $manager->flush();
        }
        public function getDependencies()
    {
        return [
            CategoryFixtures::class,
       
        ];
    }
    }

