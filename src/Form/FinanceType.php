<?php

namespace App\Form;

use App\Entity\Depense;
use App\Entity\Finance;
use App\Entity\Revenu;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class FinanceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('moisFin', ChoiceType::class, [
            'label' => 'Mois',
            'choices' => [
                'Janvier' => 1,
                'Février' => 2,
                'Mars' => 3,
                'Avril' => 4,
                'Mai' => 5,
                'Juin' => 6,
                'Juillet' => 7,
                'Août' => 8,
                'Septembre' => 9,
                'Octobre' => 10,
                'Novembre' => 11,
                'Décembre' => 12
            ],
            'attr' => [
            'class' => 'form-control'
        ],
        'placeholder' => 'Sélectionnez un mois'
    ])
            ->add('anneeFin', NumberType::class, [
                'label' => 'Année',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
           // ->add('totalDepenses')
            //->add('totalRevenus')
            /*->add('totalDepenses', MoneyType::class, [
                'currency' => 'USD', // Specify the currency code or make it dynamic
                'scale' => 2,         // Precision, 2 decimal places
                'divisor' => 100,     // Optionally, use divisor if you're dealing with smaller units
            ])
            ->add('totalRevenus', MoneyType::class, [
                'currency' => 'USD', // Specify the currency code or make it dynamic
                'scale' => 2,         // Precision, 2 decimal places
                'divisor' => 100,     // Optionally, use divisor if you're dealing with smaller units
            ])
            //->add('profit')
            ->add('profit', NumberType::class, [
                'scale' => 2,            // Define the precision (number of decimal places)
                'html5' => true,         // Ensure it uses HTML5 number input
                'attr' => ['step' => '0.01'],  // Allow decimal values
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
            ->add('depenses', EntityType::class, [
                'class' => Depense::class,
                'choice_label' => 'iddepense',
                'multiple' => true,
            ])
            ->add('revenus', EntityType::class, [
                'class' => Revenu::class,
                'choice_label' => 'idrevenu',
                'multiple' => true,
            ])*/
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Finance::class,
        ]);
    }
}
