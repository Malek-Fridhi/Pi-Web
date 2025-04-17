<?php

namespace App\Form;

use App\Entity\Cour;
use App\Entity\Planning;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlanningType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('duree')
            ->add('date', null, [
                'widget' => 'single_text',
            ])
            ->add('cour', EntityType::class, [
                'class' => Cour::class, // Li ykhdem m3a Cours entity
                'choice_label' => 'nom', // Bch yikhdem bel 'name' ta3 Cours
                'expanded' => true, // T3ridhom kif checkboxes
                'placeholder' => 'Sélectionnez un cours', // Optional placeholder
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'username',
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Planning::class,
        ]);
    }
}
