<?php

namespace App\Form;

use App\Entity\Objectif;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ObjectifType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('description', TextType::class, [
                'label' => 'Description',
                'attr' => [
                    'class' => 'form-control',
                    'minlength' => 5
                ]
            ])
            ->add('poidsActuel', NumberType::class, [
                'label' => 'Poids actuel (kg)',
                'scale' => 2,
                'invalid_message' => 'Le poids actuel doit être un nombre',
                'attr' => [
                    'class' => 'form-control',
                    'step' => '0.01'
                ]
            ])
            ->add('poidsCible', NumberType::class, [
                'label' => 'Poids cible (kg)',
                'scale' => 2,
                'invalid_message' => 'Le poids cible doit être un nombre',
                'attr' => [
                    'class' => 'form-control',
                    'step' => '0.01'
                ]
            ])
            ->add('status', ChoiceType::class, [
                'label' => 'Statut',
                'choices' => [
                    'En Cours' => Objectif::STATUS_EN_COURS,
                    'Atteint' => Objectif::STATUS_ATTEINT,
                ],
                'attr' => [
                    'class' => 'form-control'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Objectif::class,
            'attr' => ['novalidate' => 'novalidate']
        ]);
    }
}