<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre', TextType::class, [
                'label' => 'Nombre',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor ingrese su nombre',
                    ]),
                ],
            ])
            ->add('apellido', TextType::class, [
                'label' => 'Apellido',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor ingrese su apellido',
                    ]),
                ],
            ])
            ->add('dni', TextType::class, [
                'label' => 'DNI',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor ingrese su DNI',
                    ]),
                ],
            ])
            ->add('direccion', TextType::class, [
                'label' => 'Dirección',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor ingrese su dirección',
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Correo electrónico',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor ingrese su correo electrónico',
                    ]),
                ],
            ])
            ->add('username', TextType::class, [
                'label' => 'Nombre de Usuario',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor ingrese un nombre de usuario',
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'El nombre de usuario debe tener al menos {{ limit }} caracteres',
                        'max' => 180,
                    ]),
                    new Regex([
                        'pattern' => '/^[a-zA-Z0-9_]+$/',
                        'message' => 'El nombre de usuario solo puede contener letras, números y guiones bajos',
                    ]),
                ],
            ])
            ->add('telefono', TextType::class, [
                'label' => 'Teléfono',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor ingrese su número de teléfono',
                    ]),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => 'Contraseña',
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor ingrese una contraseña',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'La contraseña debe tener al menos {{ limit }} caracteres',
                        'max' => 4096,
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
