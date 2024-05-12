<?php

namespace App\Form;

use App\Entity\Effect;
use App\Entity\Heroe;
use App\Entity\Skill;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

class SkillType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('type')
            ->add('attackDamage')
            ->add('effect', EntityType::class, [
                'class' => Effect::class,
                'choice_label' => 'id',
                'multiple' => true,
                'required' => false,
            ])
            ->add('heroes', EntityType::class, [
                'class' => Heroe::class,
                'choice_label' => 'id',
                'multiple' => true,
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
            'data_class' => Skill::class,
        ]);
    }
}
