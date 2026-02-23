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
    public const EZEKIEL_REFERENCE = 'npc.ezekiel';

    public function load(ObjectManager $manager): void
    {
        $now = new \DateTimeImmutable();

        // Moira Brown - Merchant in Megaton
        /** @var Role $merchant */
        $merchant = $this->getReference(RoleFixtures::MERCHANT_REFERENCE, Role::class);

        /** @var Location $megaton */
        $megaton = $this->getReference(LocationFixtures::MEGATON_REFERENCE, Location::class);

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

        // Ezekiel - Radio host in Diamond City
        /** @var Role $dj */
        $dj = $this->getReference(RoleFixtures::DJ_REFERENCE, Role::class);
        /** @var Location $diamondCity */
        $diamondCity = $this->getReference(LocationFixtures::DIAMOND_CITY_REFERENCE, Location::class);
        
        $ezekiel = new Npc();
        $ezekiel->setName('Ezekiel');
        $ezekiel->setNotes('Radio host of "The Frontline" in Diamond City. Former Brotherhood of Steel scribe.');
        $ezekiel->setRole($dj);
        $ezekiel->setLocation($diamondCity);
        $ezekiel->setStrength(4);
        $ezekiel->setPerception(7);
        $ezekiel->setEndurance(5);
        $ezekiel->setCharisma(6);
        $ezekiel->setIntelligence(7);
        $ezekiel->setAgility(4);
        $ezekiel->setLuck(5);
        $ezekiel->setCreatedAt($now);
        $ezekiel->setUpdatedAt($now);

        $manager->persist($ezekiel);
        $this->addReference(self::EZEKIEL_REFERENCE, $ezekiel);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [RoleFixtures::class, LocationFixtures::class];
    }
}
