<?php

namespace App\Form;

use App\Entity\Heroe;
use App\Entity\Item;
use App\Entity\Skill;
use App\Entity\Stage;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

class HeroeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('healthPoints')
            ->add('attackPower')
            ->add('criticalStrikeChance')
            ->add('defense')
            ->add('experience')
            ->add('level')
            ->add('state')
            ->add('maxHealthPoints')
            ->add('name')
            ->add('stages', EntityType::class, [
                'class' => Stage::class,
                'choice_label' => 'id',
                'multiple' => true,
                'required' => false,
            ])
            ->add('weapon_1', EntityType::class, [
                'class' => Item::class,
                'choice_label' => 'name',
                'multiple' => true,
                'required' => false,
            ])
            ->add('abilities', EntityType::class, [
                'class' => Skill::class,
                'choice_label' => 'name',
                'multiple' => true,
            ])
            ->add('image', FileType::class, [
                'label' => 'Images',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'mimeTypesMessage' => 'Please upload a valid Image',
                    ])
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Heroe::class,
        ]);
    }
}
