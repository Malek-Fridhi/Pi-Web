<?php 
namespace App\Form;

use App\Entity\PlanNutritionnel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlanNutritionnelType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateCreation', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de création',
                'attr' => [
                    'class' => 'form-control',
                ],
                'required' => true,
                'empty_data' => null,
                'invalid_message' => 'Veuillez entrer une date de création valide.',
            ])
            ->add('dateModification', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de modification',
                'attr' => [
                    'class' => 'form-control',
                ],
                'required' => true,
                'empty_data' => null,
                'invalid_message' => 'Veuillez entrer une date de modification valide.',
            ])
            ->add('regime', TextareaType::class, [
                'label' => 'Régime',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez le régime ici...',
                ],
                'required' => true,
            ])
            ->add('nbrJours', IntegerType::class, [
                'label' => 'Nombre de jours',
                'attr' => [
                    'class' => 'form-control',
                    'min' => 1,
                ],
                'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PlanNutritionnel::class,
            'attr' => ['novalidate' => 'novalidate'], // Désactive la validation HTML5
        ]);
    }
}
