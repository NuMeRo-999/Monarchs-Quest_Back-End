<?php

namespace App\Form;

use App\Entity\Enemy;
use App\Entity\Stage;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

class EnemyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('healthPoints')
            ->add('attackPower')
            ->add('defense')
            ->add('criticalStrikeChance')
            ->add('level')
            ->add('state')
            ->add('name')
            ->add('stage', EntityType::class, [
                'class' => Stage::class,
                'choice_label' => 'stage',
                'required' => false,
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
            'data_class' => Enemy::class,
        ]);
    }
}
