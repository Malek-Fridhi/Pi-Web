<?php

namespace App\Form;

use App\Entity\MessengerMessage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MessengerMessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('body')
            ->add('headers')
            ->add('queue_name')
            ->add('created_at', null, [
                'widget' => 'single_text',
            ])
            ->add('available_at', null, [
                'widget' => 'single_text',
            ])
            ->add('delivered_at', null, [
                'widget' => 'single_text',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MessengerMessage::class,
        ]);
    }
}
