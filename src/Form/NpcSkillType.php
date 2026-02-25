<?php

namespace App\Form;

use App\Entity\NpcSkill;
use App\Entity\Skill;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Range;

class NpcSkillType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('skill_id', EntityType::class, [
                'class' => Skill::class,
                'choice_label' => 'name',
                'placeholder' => 'Choose a skill',
                'label' => 'Skill',
            ])
            ->add('level', IntegerType::class, [
                'label' => 'Level',
                'constraints' => [
                    new Range(min: 1, max: 6),
                ],
                'attr' => [
                    'min' => 1,
                    'max' => 6,
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => NpcSkill::class,
        ]);
    }
}
