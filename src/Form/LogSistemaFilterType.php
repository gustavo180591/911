<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LogSistemaFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('usuario', TextType::class, [
                'label' => 'Usuario',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Buscar por usuario',
                ],
            ])
            ->add('accion', TextType::class, [
                'label' => 'Acción',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Buscar por acción',
                ],
            ])
            ->add('fechaInicio', DateType::class, [
                'label' => 'Fecha de Inicio',
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('fechaFin', DateType::class, [
                'label' => 'Fecha de Fin',
                'widget' => 'single_text',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Este formulario no está vinculado directamente a una entidad
            'csrf_protection' => true,
            'method' => 'GET',
        ]);
    }

    public function getBlockPrefix(): string
    {
        return ''; // Elimina el prefijo en los campos para facilitar la búsqueda
    }
}
