<?php

namespace App\Form;

use App\Entity\Location;
use App\Entity\Npc;
use App\Entity\Role;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Range;

class NpcType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('strength', IntegerType::class, $this->specialFieldOptions())
            ->add('perception', IntegerType::class, $this->specialFieldOptions())
            ->add('endurance', IntegerType::class, $this->specialFieldOptions())
            ->add('charisma', IntegerType::class, $this->specialFieldOptions())
            ->add('intelligence', IntegerType::class, $this->specialFieldOptions())
            ->add('agility', IntegerType::class, $this->specialFieldOptions())
            ->add('luck', IntegerType::class, $this->specialFieldOptions())
            ->add('notes', TextareaType::class, [
                'required' => false,
            ])
            ->add('role', EntityType::class, [
                'class' => Role::class,
                'choice_label' => 'name',
                'placeholder' => 'Choose a role',
                'required' => false,
            ])
            ->add('location', EntityType::class, [
                'class' => Location::class,
                'choice_label' => 'name',
                'placeholder' => 'Choose a location',
                'required' => false,
            ])
            ->add('npcSkills', CollectionType::class, [
                'entry_type' => NpcSkillType::class,
                'entry_options' => [
                    'label' => false,
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'required' => false,
                'prototype' => true,
            ])
            ->add('knowledge', CollectionType::class, [
                'entry_type' => KnowledgeType::class,
                'entry_options' => [
                    'label' => false,
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'required' => false,
                'prototype' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Npc::class,
        ]);
    }

    private function specialFieldOptions(): array
    {
        return [
            'constraints' => [
                new Range(min: 4, max: 12),
            ],
            'attr' => [
                'min' => 4,
                'max' => 12,
            ],
        ];
    }
}
