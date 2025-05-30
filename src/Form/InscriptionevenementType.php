<?php

namespace App\Form;

use App\Entity\Evenement;
use App\Entity\Inscriptionevenement;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class InscriptionevenementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date_inscription', null, [
                'widget' => 'single_text',
            ])
            //->add('statut')
            ->add('statut', ChoiceType::class, [
                'choices' => [
                    'Pending' => 'Pending',
                    'Approved ' => 'Approved ',
                    'Canceled' => 'Canceled',
                ],
                'expanded' => true,  // For radio buttons
                'multiple' => false, // Only one choice can be selected
            ])
            ->add('evenement', EntityType::class, [
                'class' => Evenement::class,
                'choice_label' => 'idevenement',
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Inscriptionevenement::class,
        ]);
    }
}
