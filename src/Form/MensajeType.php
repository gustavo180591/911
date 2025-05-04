<?php

namespace App\Form;

use App\Entity\Mensaje;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MensajeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('contenido')
            ->add('fechaEnvio', null, [
                'widget' => 'single_text',
            ])
            ->add('usuarioRemitente', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
            ->add('usuarioDestinatario', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Mensaje::class,
        ]);
    }
}
