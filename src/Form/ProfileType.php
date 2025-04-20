<?php

namespace App\Form;

use App\Entity\Usuario;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;

class ProfileType extends AbstractType
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
                    new Length([
                        'max' => 255,
                        'maxMessage' => 'El nombre no puede superar los {{ limit }} caracteres.',
                    ]),
                ],
            ])
            ->add('apellido', TextType::class, [
                'label' => 'Apellido',
                'constraints' => [
                    new NotBlank([
                        'message' => 'El apellido no puede estar vacío.',
                    ]),
                    new Length([
                        'max' => 255,
                        'maxMessage' => 'El apellido no puede superar los {{ limit }} caracteres.',
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Correo Electrónico',
                'constraints' => [
                    new NotBlank([
                        'message' => 'El correo electrónico no puede estar vacío.',
                    ]),
                    new Email([
                        'message' => 'Por favor, introduce un correo electrónico válido.',
                    ]),
                ],
            ])
            ->add('dni', TextType::class, [
                'label' => 'DNI',
                'constraints' => [
                    new NotBlank([
                        'message' => 'El DNI no puede estar vacío.',
                    ]),
                    new Length([
                        'max' => 9,
                        'maxMessage' => 'El DNI no puede superar los {{ limit }} caracteres.',
                    ]),
                ],
            ])                
            ->add('direccion', TextType::class, [
                'label' => 'Dirección',
                'required' => false,
                'constraints' => [
                    new Length([
                        'max' => 255,
                        'maxMessage' => 'La dirección no puede superar los {{ limit }} caracteres.',
                    ]),
                ],
            ])
            ->add('telefono', TextType::class, [
                'label' => 'Teléfono',
                'required' => false,
                'constraints' => [
                    new Length([
                        'max' => 20,
                        'maxMessage' => 'El número de teléfono no puede superar los {{ limit }} caracteres.',
                    ]),
                ],
            ])
            ->add('password', TextType::class, [
                'label' => 'Contraseña',
                'required' => false,
                'constraints' => [
                    new Length([
                        'max' => 255,
                        'maxMessage' => 'La contraseña no puede superar los {{ limit }} caracteres.',
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