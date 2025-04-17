<?php

namespace App\Form;

use App\Entity\Planning;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;


class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
      /*  $builder
        ->add('email')*/
       /* ->add('roles', ChoiceType::class, [
            'choices' => [
                'Admin' => 'ROLE_ADMIN',
                'User' => 'ROLE_USER',
                'Adherant' => 'ROLE_ADHERANT',
                'Manager' => 'ROLE_MANAGER',
                'Nutrionniste' => 'ROLE_NUTRIONNISTE',
                'Comptable' => 'ROLE_COMPTABLE',
                'Coach' => 'ROLE_COACH',
                'Salaire' => 'ROLE_Salaire',
                 
            ],
            'multiple' => true,  // Permet la sélection multiple des rôles
            'expanded' => true,  // Affiche sous forme de cases à cocher
        ])*/
       // ->add('password')
   /*    ->add('password', PasswordType::class, [
        'label' => 'Mot de passe',
        'required' => true,
    ])
        ->add('username')
        ->add('tel')
       // ->add('salaire')
        ->add('image', FileType::class, [
            'label' => 'Upload Profile Image', 
            'mapped' => false,  // Not mapped to the User entity directly
            'required' => false, // Image is optional
            'attr' => ['accept' => 'image/*'],  // Restrict file types to images
        ]);*/
           
           
           
           
           
           
            /*->add('plannings', EntityType::class, [
                'class' => Planning::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])*/

            $builder
    ->add('email')
   /* ->add('roles', ChoiceType::class, [
        'choices' => [
            'Admin' => 'ROLE_ADMIN',
            'User' => 'ROLE_USER',
            'Adherant' => 'ROLE_ADHERANT',
            'Manager' => 'ROLE_MANAGER',
            'Nutrionniste' => 'ROLE_NUTRIONNISTE',
            'Comptable' => 'ROLE_COMPTABLE',
            'Coach' => 'ROLE_COACH',
            'Salaire' => 'ROLE_Salaire',
             
        ],
        'multiple' => true,  // Permet la sélection multiple des rôles
        'expanded' => true,  // Affiche sous forme de cases à cocher
    ])*/
    ->add('password')
    ->add('username')
    ->add('tel')
    //->add('salaire')
    ->add('image', FileType::class, [
        'label' => 'Upload Profile Image', 
        'mapped' => false,  // Not mapped to the User entity directly
        'required' => false, // Image is optional
        'attr' => ['accept' => 'image/*'],  // Restrict file types to images
    ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
