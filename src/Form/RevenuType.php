<?php

namespace App\Form;

use App\Entity\Finance;
use App\Entity\Revenu;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class RevenuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('sourceRevenu')
            //->add('montantRevenu')
            ->add('montantRevenu', NumberType::class, [
                'scale' => 2, // Ensures decimal precision
                'html5' => true, // Enables HTML5 validation in browsers
            ])
            ->add('datereceptionRevenu', null, [
                'widget' => 'single_text',
            ])
            ->add('finances', EntityType::class, [
                'class' => Finance::class,
                'choice_label' => 'idfinance',
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Revenu::class,
        ]);
    }
}
