<?php

namespace App\DataFixtures;

use Faker\Factory;
use Faker\Generator;
use App\Entity\Newsletter;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class NewsletterFixtures extends Fixture implements DependentFixtureInterface
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
        for ($newslettercount=1; $newslettercount <= 30; $newslettercount++) { 
            $category = $this->getReference('category_' . $this->faker->numberBetween(1,5));
            $newsletter = new Newsletter();
            $newsletter->setTitle($this->faker->sentence(4));
            $newsletter->setContent($this->faker->realText(400));
            $dateTime = $this->faker->dateTimeThisMonth();
            $dateTimeImmutable = \DateTimeImmutable::createFromMutable($dateTime);
            $newsletter->setCreatedAt($dateTimeImmutable);
            $newsletter->setIsSent(false);
            $newsletter->setCategory($category);

                $this->addReference('newsletter_' .$newslettercount, $newsletter);
            
             $manager->persist($newsletter);
             
            }
        $manager->flush();
        dump($newsletter->getId());
        
    }
    public function getDependencies()
    {
        return [
            CategoryFixtures::class,
            
        ];
    }
}
