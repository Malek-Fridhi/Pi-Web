<?php

namespace App\Form;

use App\Entity\Abonnement;
use App\Entity\Cour;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class AbonnementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'label' => 'Type d\'abonnement :',
                'choices' => [
                    'Silver' => 'Silver',
                    'Gold' => 'Gold',
                    'Diamond' => 'Diamond',
                    'Premium' => 'Premium',
                ],
                'attr' => ['class' => 'form-control']
            ])
            ->add('date_debut', DateType::class, [
                'label' => 'Date de début :',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control']
            ])
            /*->add('user', EntityType::class, [
                'label' => 'Utilisateur :',
                'class' => User::class,
                'choice_label' => function (User $user) {
                    return $user->getUsername();
                },
                'placeholder' => 'Choisir un utilisateur',
                'attr' => ['class' => 'form-control']
            ])*/
            /*->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])*/
            ->add('cours', EntityType::class, [
                'label' => 'Sélectionnez vos cours :',
                'class' => Cour::class,
                'choice_label' => 'nom',
                'multiple' => true,
                'expanded' => true, // affichage en checkbox
                'constraints' => [
                    new Callback([$this, 'validateCoursLimit'])
                ]
            ]);
    }

    public function validateCoursLimit($cours, ExecutionContextInterface $context): void
    {
        $form = $context->getRoot();
        $type = $form->get('type')->getData();
        $maxCours = match ($type) {
            'Gold' => 2,
            'Diamond' => 3,
            'Premium' => 4,
            default => 1,
        };

        if (count($cours) < $maxCours) {
            $context->buildViolation("Vous devez sélectionner au moins $maxCours cours pour l'abonnement $type.")
                ->atPath('cours')
                ->addViolation();
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Abonnement::class,
        ]);
    }
}
