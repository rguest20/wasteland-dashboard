<?php

namespace App\DataFixtures;

use App\Entity\Location;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class LocationFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $locations = [
            ['name' => 'Shady Sands', 'defence' => 78, 'food' => 84, 'morale' => 72, 'standing' => 65],
            ['name' => 'Junktown', 'defence' => 54, 'food' => 49, 'morale' => 58, 'standing' => 44],
            ['name' => 'The Hub', 'defence' => 62, 'food' => 73, 'morale' => 61, 'standing' => 70],
            ['name' => 'Necropolis', 'defence' => 41, 'food' => 30, 'morale' => 38, 'standing' => 33],
            ['name' => 'Boneyard', 'defence' => 67, 'food' => 57, 'morale' => 52, 'standing' => 59],
        ];

        foreach ($locations as $row) {
            $location = new Location();
            $location->setName($row['name']);
            $location->setDefence($row['defence']);
            $location->setFood($row['food']);
            $location->setMorale($row['morale']);
            $location->setStanding($row['standing']);
            $manager->persist($location);
        }

        $manager->flush();
    }
}
