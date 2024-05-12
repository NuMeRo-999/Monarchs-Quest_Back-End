<?php

namespace App\Form;

use App\Entity\Heroe;
use App\Entity\SaveSlot;
use App\Entity\Stage;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('stage')
            ->add('heroes', EntityType::class, [
                'class' => Heroe::class,
'choice_label' => 'id',
'multiple' => true,
            ])
            ->add('saveSlot', EntityType::class, [
                'class' => SaveSlot::class,
'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Stage::class,
        ]);
    }
}
