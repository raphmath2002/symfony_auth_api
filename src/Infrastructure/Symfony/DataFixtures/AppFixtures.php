<?php

namespace Infrastructure\Symfony\DataFixtures;

use Domain\Entity\Movie;

use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        for ($i=0; $i < 20; $i++) { 
            $movie = new Movie;
            $movie->name = "Film " . $i;

            $randomRating = rand(1, 6);
            $randomRating = $randomRating == 6 ? null : $randomRating;

            $movie->rating = $randomRating;
            $lipsum = simplexml_load_file('http://www.lipsum.com/feed/xml?amount=1&what=paras&start=0')->lipsum;
            $movie->description = $lipsum;

            //Generate a timestamp using mt_rand.
            $timestamp = mt_rand(1, time());

            //Format that timestamp into a readable date string.
            $randomDate = date("Y-M-d", $timestamp);

            $date = new DateTimeImmutable($randomDate);

            $movie->parution_date = $date;

            $manager->persist($movie);
        }

        $manager->flush();
    }
}
