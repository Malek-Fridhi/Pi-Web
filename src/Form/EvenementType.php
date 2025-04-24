<?php

namespace App\Form;

use App\Entity\Evenement;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class EvenementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre')
            ->add('description')
            ->add('date', null, [
                'widget' => 'single_text',
            ])
            ->add('duree')
            ->add('capacite')
          //  ->add('statut')
          ->add('statut', ChoiceType::class, [
            'choices' => [
                'Planned' => 'Planned',
                'Ongoing' => 'Ongoing',
                'Completed' => 'Completed',
                'Canceled' => 'Canceled',
            ],
            'expanded' => true,  // For radio buttons
            'multiple' => false, // Only one choice can be selected
        ])
            ->add('imageUrl')
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Evenement::class,
        ]);
    }
}