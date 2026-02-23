<?php

namespace App\DataFixtures;

use App\Entity\Location;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class LocationFixtures extends Fixture
{
    public const MEGATON_REFERENCE = 'location.megaton';
    public const DIAMOND_CITY_REFERENCE = 'location.diamond-city';

    public function load(ObjectManager $manager): void
    {
        $locations = [
            ['name' => 'Mechminster', 'defence' => 3, 'food' => 2, 'morale' => 2, 'standing' => 1],
            ['name' => 'Beatsville', 'defence' => 0, 'food' => 2, 'morale' => 4, 'standing' => 1],
            ['name' => 'Mirage', 'defence' => 3, 'food' => 2, 'morale' => 3, 'standing' => 1],
            ['name' => 'Big Top', 'defence' => 1, 'food' => 2, 'morale' => 3, 'standing' => 4],
            ['name' => 'Diamond City', 'defence' => 3, 'food' => 2, 'morale' => 2, 'standing' => 3],
            ['name' => 'Goodneighbour', 'defence' => 2, 'food' => 1, 'morale' => 1, 'standing' => 1],
            ['name' => 'Megaton', 'defence' => 3, 'food' => 1, 'morale' => 3, 'standing' => 2],
        ];

        foreach ($locations as $data) {
            $location = new Location();
            $location->setName($data['name']);
            $location->setDefence($data['defence']);
            $location->setFood($data['food']);
            $location->setMorale($data['morale']);
            $location->setStanding($data['standing']);

            $manager->persist($location);
            $this->addReference($this->refKey($data['name']), $location);

        }

        $manager->flush();
    }

    private function refKey(string $name): string
    {
        $slug = strtolower(trim($name));
        $slug = preg_replace('/\s+/', '-', $slug);
        $slug = preg_replace('/[^a-z0-9\-]/', '', $slug);
        return 'location.' . $slug;
    }
}
