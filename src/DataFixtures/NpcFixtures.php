<?php

namespace App\DataFixtures;

use App\Entity\Npc;
use App\Entity\Location;
use App\Entity\Role;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

final class NpcFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('en_GB');

        /** @var Role[] $roles */
        $roles = $manager->getRepository(Role::class)->findAll();
        /** @var Location[] $locations */
        $locations = $manager->getRepository(Location::class)->findAll();

        for ($i = 0; $i < 50; $i++) {
            $npc = new Npc();
            $npc->setName($faker->name());
            $npc->setNotes($faker->optional(0.7)->sentence(12));
            $npc->setRole($faker->randomElement($roles));
            $npc->setLocation($faker->optional(0.8)->randomElement($locations));
            $npc->setCreatedAt(new \DateTimeImmutable());
            $npc->setUpdatedAt(new \DateTimeImmutable());

            $manager->persist($npc);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [RoleFixtures::class, LocationFixtures::class];
    }
}
