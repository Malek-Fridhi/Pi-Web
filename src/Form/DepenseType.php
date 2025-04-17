<?php

namespace App\Form;

use App\Entity\Depense;
use App\Entity\Finance;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class DepenseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('typeDep', TextType::class, [
            'label' => 'Type de Dépense :', // Changer le label ici
            'attr' => ['class' => 'form-control'],
            
        ])
           // ->add('montantDep')
           ->add('montantDep', NumberType::class, [
            'label' => 'Montant de Dépense :', // Changer le label ici
            'attr' => [
                'placeholder' => 'en DT',
                'class' => 'form-control'
            ]
        ])
            ->add('datereceptionDep', null, [
                'label' => 'Date de Réception :',  // Ajout du label
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control',  // Ajout d'une classe Bootstrap
                    
                ]
            ])
            /*->add('finances', EntityType::class, [
                'class' => Finance::class,
                'choice_label' => 'idfinance',
                'multiple' => true,
            ])*/
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Depense::class,
        ]);
    }
}
