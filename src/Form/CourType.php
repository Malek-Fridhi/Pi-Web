<?php

namespace App\Form;

use App\Entity\Cour;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class CourType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom du Cours :', // Changer le label ici
                'attr' => [
                    'class' => 'form-control', // Ajouter la classe Bootstrap
                    'placeholder' => 'Entrez le nom du cours'
                ]
            ])
            ->add('description', TextType::class, [
                'label' => 'Description du Cours :', // Changer le label ici
                'attr' => [
                    'class' => 'form-control', // Ajouter la classe Bootstrap
                    'placeholder' => 'Entrez une description'
                ]
            ])
            ->add('capacite', NumberType::class, [
                'label' => 'Capacité du Cours :', // Changer le label ici
                'attr' => [
                    'class' => 'form-control', // Ajouter la classe Bootstrap
                    'placeholder' => 'Nombre de places disponibles'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Cour::class,
        ]);
    }
}
