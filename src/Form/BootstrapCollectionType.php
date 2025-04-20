<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Clase que extiende CollectionType para añadir estilos de Bootstrap 
 * y defaults para 'allow_add', 'allow_delete', etc.
 */
class BootstrapCollectionType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'entry_type' => null,
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'attr' => ['class' => 'bootstrap-collection'],
        ]);
    }

    public function getParent(): string
    {
        return CollectionType::class;
    }
}
