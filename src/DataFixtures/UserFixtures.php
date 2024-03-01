<?php

namespace App\DataFixtures;

use Faker\Factory;
use Faker\Generator;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;


class UserFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 50; $i++) {
            $category = $this->getReference('category_' . $this->faker->numberBetween(1,5));
   
            $user = new User();
            $user->setEmail($this->faker->email());
            $dateTime = $this->faker->dateTimeThisMonth();
            $dateTimeImmutable = \DateTimeImmutable::createFromMutable($dateTime);
            $user->setCreatedAt($dateTimeImmutable);
            $user->setIsRgpd($this->faker->boolean);
            $user->setValidationToken($this->faker->uuid());
            $user->setIsValid($this->faker->boolean);
            $user->addCategory($category);

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

