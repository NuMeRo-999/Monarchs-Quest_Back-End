<?php

namespace App\Form;

use App\Entity\Game;
use App\Entity\Item;
use App\Entity\SaveSlot;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SaveSlotType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('creationDate', null, [
                'widget' => 'single_text',
                'data' => new \DateTime(),
            ])
            ->add('money')
            ->add('game', EntityType::class, [
                'class' => Game::class,
'choice_label' => 'id',
            ])
            ->add('inventario', EntityType::class, [
                'class' => Item::class,
                'choice_label' => 'id',
                'multiple' => true,
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SaveSlot::class,
        ]);
    }
}
