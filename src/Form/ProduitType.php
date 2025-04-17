<?php

namespace App\Form;

use App\Entity\Produit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType; 
use Symfony\Component\Validator\Constraints\NotBlank;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('quantite')
            ->add('prix_vente')
            ->add('prix_achat')
            //->add('image')
            ->add('image', FileType::class, [
                'required' => true, // Rend le champ obligatoire
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez télécharger une image',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,

            'image_required' => true
        ]);
    }
}
