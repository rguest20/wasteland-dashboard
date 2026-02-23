<?php

namespace App\DataFixtures;

use App\Entity\Npc;
use App\Entity\NpcSkill;
use App\Entity\Skill;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class NpcSkillFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // Moira Brown - Merchant in Megaton

        /** @var Npc $moira */
        $moira = $this->getReference(NpcFixtures::MOIRA_REFERENCE, Npc::class);
        /** @var Skill $barter */
        $barter = $this->getReference(SkillFixtures::BARTER_REFERENCE, Skill::class);
        /** @var Skill $speech */
        $speech = $this->getReference(SkillFixtures::SPEECH_REFERENCE, Skill::class);
        /** @var Skill $medicine */
        $medicine = $this->getReference(SkillFixtures::MEDICINE_REFERENCE, Skill::class);

        $moiraBarter = new NpcSkill();
        $moiraBarter->setNpcId($moira);
        $moiraBarter->setSkillId($barter);
        $moiraBarter->setLevel(5);

        $moiraSpeech = new NpcSkill();
        $moiraSpeech->setNpcId($moira);
        $moiraSpeech->setSkillId($speech);
        $moiraSpeech->setLevel(4);

        $moiraMedicine = new NpcSkill();
        $moiraMedicine->setNpcId($moira);
        $moiraMedicine->setSkillId($medicine);
        $moiraMedicine->setLevel(2);

        $manager->persist($moiraBarter);
        $manager->persist($moiraSpeech);
        $manager->persist($moiraMedicine);

        // Ezekiel - Radio host in Diamond City

        /** @var Npc $ezekiel */
        $ezekiel = $this->getReference(NpcFixtures::EZEKIEL_REFERENCE, Npc::class);
        /** @var Skill $speech */
        $speech = $this->getReference(SkillFixtures::SPEECH_REFERENCE, Skill::class);
        /** @var Skill $survival */
        $survival = $this->getReference(SkillFixtures::SURVIVAL_REFERENCE, Skill::class);
        /** @var Skill $science */
        $science = $this->getReference(SkillFixtures::SCIENCE_REFERENCE, Skill::class);

        $ezekielSpeech = new NpcSkill();
        $ezekielSpeech->setNpcId($ezekiel);
        $ezekielSpeech->setSkillId($speech);
        $ezekielSpeech->setLevel(6);

        $ezekielSurvival = new NpcSkill();
        $ezekielSurvival->setNpcId($ezekiel);
        $ezekielSurvival->setSkillId($survival);
        $ezekielSurvival->setLevel(5);

        $ezekielScience = new NpcSkill();
        $ezekielScience->setNpcId($ezekiel);
        $ezekielScience->setSkillId($science);
        $ezekielScience->setLevel(4);

        $manager->persist($ezekielSpeech);
        $manager->persist($ezekielSurvival);
        $manager->persist($ezekielScience);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            NpcFixtures::class,
            SkillFixtures::class,
        ];
    }
}
