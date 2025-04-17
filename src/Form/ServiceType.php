<?php

namespace App\Form;

use App\Entity\Service;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
class ServiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom_service')
            ->add('description')
            ->add('duree')
            //->add('prix')
            ->add('prix', NumberType::class, [
                'required' => true, // Or false, depending on your use case
                'scale' => 2, // Number of decimal places, if necessary
                'attr' => [
                    'min' => 0, // Optional: prevent negative numbers
                ],
            ])
            ->add('type_service')
            ->add('disponibilite')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Service::class,
        ]);
    }
}
