<?php

namespace App\DataFixtures;

use App\Entity\Npc;
use App\Entity\Location;
use App\Entity\Role;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class NpcFixtures extends Fixture implements DependentFixtureInterface
{
    public const MOIRA_REFERENCE = 'npc.moira-brown';

    public function load(ObjectManager $manager): void
    {
        $now = new \DateTimeImmutable();

        /** @var Role $merchant */
        $merchant = $this->getReference(RoleFixtures::MERCHANT_REFERENCE);

        /** @var Location $megaton */
        $megaton = $this->getReference(LocationFixtures::MEGATON_REFERENCE);

        $moira = new Npc();
        $moira->setName('Moira Brown');
        $moira->setNotes('Owner of Craterside Supply in Megaton. Author of "Wasteland Survival Guide".');
        $moira->setRole($merchant);
        $moira->setLocation($megaton);
        $moira->setStrength(5);
        $moira->setPerception(6);
        $moira->setEndurance(4);
        $moira->setCharisma(7);
        $moira->setIntelligence(8);
        $moira->setAgility(5);
        $moira->setLuck(6);
        $moira->setCreatedAt($now);
        $moira->setUpdatedAt($now);

        $manager->persist($moira);
        $this->addReference(self::MOIRA_REFERENCE, $moira);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [RoleFixtures::class, LocationFixtures::class];
    }
}
