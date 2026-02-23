<?php

namespace App\DataFixtures;

use App\Entity\Skill;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class SkillFixtures extends Fixture
{
    public const BARTER_REFERENCE = 'skill.barter';
    public const BIG_GUNS_REFERENCE = 'skill.big-guns';
    public const ENERGY_WEAPONS_REFERENCE = 'skill.energy-weapons';
    public const EXPLOSIVES_REFERENCE = 'skill.explosives';
    public const LOCKPICK_REFERENCE = 'skill.lockpick';
    public const MEDICINE_REFERENCE = 'skill.medicine';
    public const MELEE_WEAPONS_REFERENCE = 'skill.melee-weapons';
    public const REPAIR_REFERENCE = 'skill.repair';
    public const SCIENCE_REFERENCE = 'skill.science';
    public const SMALL_GUNS_REFERENCE = 'skill.small-guns';
    public const SNEAK_REFERENCE = 'skill.sneak';
    public const SPEECH_REFERENCE = 'skill.speech';
    public const SURVIVAL_REFERENCE = 'skill.survival';
    public const UNARMED_REFERENCE = 'skill.unarmed';
    
    public function load(ObjectManager $manager): void
    {
        $skills = [
            'Barter' => 'The ability to negotiate better prices when buying or selling items.',
            'Big Guns' => 'Proficiency with heavy weapons like miniguns, missile launchers, and gatling lasers.',
            'Energy Weapons' => 'Skill with energy-based weapons such as laser rifles, plasma pistols, and tesla cannons.',
            'Explosives' => 'Expertise in using grenades, mines, and other explosive devices effectively.',
            'Lockpick' => 'The ability to pick locks on doors, safes, and containers to gain access to restricted areas or valuable items.',
            'Medicine' => 'Knowledge of first aid, stimpaks, and other medical treatments to heal injuries and cure diseases.',
            'Melee Weapons' => 'Proficiency with close-combat weapons like knives, swords, and baseball bats.',
            'Repair' => 'The ability to fix and maintain weapons, armor, and other equipment.',
            'Science' => 'Knowledge of scientific principles and the ability to hack computers and terminals.',
            'Small Guns' => 'Skill with pistols, rifles, and other small firearms.',
            'Sneak' => 'The ability to move silently and avoid detection.',
            'Speech' => 'The ability to persuade, intimidate, or deceive others.',
            'Survival' => 'The ability to endure harsh conditions and find food, water, and shelter in the wasteland.',
            'Unarmed' => 'Proficiency in hand-to-hand combat without weapons.',
        ];

        foreach ($skills as $name => $description) {
            $skill = new Skill();
            $skill->setName($name);
            $skill->setDescription($description);

            $manager->persist($skill);
            $this->addReference($this->refKey($name), $skill);
        }

        $manager->flush();
    }

    private function refKey(string $name): string
    {
        $slug = strtolower(trim($name));
        $slug = preg_replace('/\s+/', '-', $slug);
        $slug = preg_replace('/[^a-z0-9\-]/', '', $slug);
        return 'skill.' . $slug;
    }
}