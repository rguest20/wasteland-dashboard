<?php

namespace App\DataFixtures;

use App\Entity\Role;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class RoleFixtures extends Fixture
{
    public const MERCHANT_REFERENCE = 'role.merchant';
    public const DJ_REFERENCE = 'role.dj';

    public function load(ObjectManager $manager): void
    {
        $roles = [
            'Merchant',
            'Raider',
            'Settler',
            'Brotherhood',
            'Ghoul',
            'Mercenary',
            'Scientist',
            'DJ',
        ];

        foreach ($roles as $name) {
            $role = new Role();
            $role->setName($name);

            $manager->persist($role);
            $this->addReference($this->refKey($name), $role);
        }

        $manager->flush();
    }

    private function refKey(string $name): string
    {
        $slug = strtolower(trim($name));
        $slug = preg_replace('/\s+/', '-', $slug);
        $slug = preg_replace('/[^a-z0-9\-]/', '', $slug);
        return 'role.' . $slug;
    }
}