<?php

namespace App\Form;

use App\Entity\Reclamation;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class ReclamationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
      
      
      $builder
            ->add('description', TextareaType::class, [
                'label' => 'Description',
              //  'attr' => ['placeholder' => 'Décrivez votre réclamation...']
                'attr' => [
                    'class' => 'form-control',
                    'minlength' => 5
                ]
            ])
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date',
            ])
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'En attente' => 'En attente',
                    'En cours' => 'En cours',
                    'Résolue' => 'Résolue',
                ],
                'label' => 'Statut',
            ])
            ->add('reponse', TextareaType::class, [
                'label' => 'Réponse (optionnel)',
                'required' => false,
                'attr' => ['placeholder' => 'Réponse éventuelle...']
            ]);
          /*  ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ]);*/

      
      
      
      
      
      
      
      
      
      
      
      
      
      
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reclamation::class,
        ]);
    }
}
