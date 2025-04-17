<?php

namespace App\Form;

use App\Entity\Equipement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints as Assert;

class EquipementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', null, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['min' => 2, 'max' => 50]),
                    new Assert\Regex([
                        'pattern' => '/^[a-zA-ZÀ-ÿ\s]+$/',
                        'message' => 'Le nom doit contenir uniquement des lettres'
                    ])
                ]
            ])
            ->add('etat')
            ->add('status')
            ->add('image', FileType::class, [
                'label' => 'Image de l\'équipement',
                'required' => $options['image_required'],
                'mapped' => false,
            ])
            ->add('prix', null, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\GreaterThan(50),
                    new Assert\Type(['type' => 'numeric'])
                ]
            ])
            ->add('quantite', null, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Range(['min' => 1, 'max' => 100])
                ]
            ])
            ->add('marque', null, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Regex([
                        'pattern' => '/^[a-zA-ZÀ-ÿ\s]+$/',
                        'message' => 'La marque doit contenir uniquement des lettres'
                    ])
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Equipement::class,
            'image_required' => false, // Ajoutez cette ligne pour définir l'option par défaut
        ]);
    }
}