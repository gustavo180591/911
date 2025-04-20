<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class LoginFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'label' => 'Nombre de Usuario o Email',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor, introduce tu nombre de usuario o email.',
                    ]),
                ],
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Contraseña',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor, introduce tu contraseña.',
                    ]),
                ],
            ]);
    }
}
