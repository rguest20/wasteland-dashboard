<?php

namespace App\Form;

use App\Entity\Knowledge;
use App\Entity\WorldSecret;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class KnowledgeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Title',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
            ])
            ->add('category', TextType::class, [
                'label' => 'Category',
                'required' => false,
            ])
            ->add('world_secret', EntityType::class, [
                'class' => WorldSecret::class,
                'choice_label' => 'title',
                'placeholder' => 'No linked world secret',
                'required' => false,
                'label' => 'Linked World Secret',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Knowledge::class,
        ]);
    }
}
