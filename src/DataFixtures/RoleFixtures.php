<?php

namespace App\DataFixtures;

use App\Entity\Role;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class RoleFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $roles = [
            'Merchant',
            'Raider',
            'Settler',
            'Brotherhood',
            'Ghoul',
        ];

        foreach ($roles as $name) {
            $role = new Role();
            $role->setName($name);

            $manager->persist($role);
        }

        $manager->flush();
    }
}
