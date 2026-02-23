<?php

namespace App\DataFixtures;

use App\Entity\Location;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class LocationFixtures extends Fixture
{
    public const MEGATON_REFERENCE = 'location.megaton';

    public function load(ObjectManager $manager): void
    {
        $locations = [
            ['name' => 'Shady Sands', 'defence' => 1, 'food' => 3, 'morale' => 1, 'standing' => 1],
            ['name' => 'Junktown', 'defence' => 3, 'food' => 3, 'morale' => 2, 'standing' => 1],
            ['name' => 'The Hub', 'defence' => 4, 'food' => 3, 'morale' => 2, 'standing' => 1],
            ['name' => 'Necropolis', 'defence' => 3, 'food' => 2, 'morale' => 3, 'standing' => 1],
            ['name' => 'Boneyard', 'defence' => 2, 'food' => 4, 'morale' => 5, 'standing' => 1],
            ['name' => 'Megaton', 'defence' => 3, 'food' => 1, 'morale' => 3, 'standing' => 2],
            ['name' => 'Vault 15', 'defence' => 2, 'food' => 3, 'morale' => 2, 'standing' => 1],
            ['name' => 'Adytum', 'defence' => 1, 'food' => 2, 'morale' => 2, 'standing' => 1],
            ['name' => 'The Den', 'defence' => 2, 'food' => 3, 'morale' => 1, 'standing' => 1],
            ['name' => 'Girdershade', 'defence' => 3, 'food' => 4, 'morale' => 3, 'standing' => 1],
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
