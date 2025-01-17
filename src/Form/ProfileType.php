<?php

namespace App\Form;

use App\Entity\Rol;
use App\Entity\Usuario;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre')
            ->add('apellido')
            ->add('email')
            ->add('password')
            ->add('dni')
            ->add('telefono')
            ->add('fechaNacimiento', null, [
                'widget' => 'single_text',
            ])
            ->add('genero')
            ->add('fechaRegistro', null, [
                'widget' => 'single_text',
            ])
            ->add('rol', EntityType::class, [
                'class' => Rol::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Usuario::class,
        ]);
    }
}
