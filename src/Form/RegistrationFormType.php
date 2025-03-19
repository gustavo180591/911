<?php

namespace App\Form;

use App\Entity\Usuario;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre', TextType::class, [
                'label' => 'Nombre',
                'constraints' => [
                    new NotBlank([
                        'message' => 'El nombre no puede estar vacío.',
                    ]),
                ],
            ])
            ->add('apellido', TextType::class, [
                'label' => 'Apellido',
                'constraints' => [
                    new NotBlank([
                        'message' => 'El apellido no puede estar vacío.',
                    ]),
                ],
                ])
                ->add('dni', TextType::class, [
                    'label' => 'DNI',
                    'constraints' => [
                        new NotBlank([
                            'message' => 'El DNI no puede estar vacío.',
                        ]),
                    ],
                ])
                ->add('direccion', TextType::class, [
                    'label' => 'Dirección',
                    'constraints' => [
                        new NotBlank([
                            'message' => 'La dirección no puede estar vacío.',
                        ]),
                    ],
                ])
                ->add('email', EmailType::class, [
                'label' => 'Correo Electrónico',
                'constraints' => [
                    new NotBlank([
                        'message' => 'El correo electrónico no puede estar vacío.',
                        ]),
                    ],
                ])            
                ->add('telefono', TextType::class, [
                'label' => 'Teléfono',
                'constraints' => [
                    new NotBlank([
                        'message' => 'El telefono no puede estar vacío.',
                        ]),
                    ],
                ])       
                ->add('Password', PasswordType::class, [
                    'label' => 'Contraseña',
                    'constraints' => [
                        new NotBlank([
                            'message' => 'La contraseña no puede estar vacía.',
                        ]),
                        new Length([
                            'min' => 6,
                            'minMessage' => 'La contraseña debe tener al menos {{ limit }} caracteres.',
                            'max' => 4096,
                        ]),
                    ],
                ]);
            
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Usuario::class,
        ]);
    }
}
