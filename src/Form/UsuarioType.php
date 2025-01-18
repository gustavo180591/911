<?php

namespace App\Form;

use App\Entity\Usuario;
use App\Entity\Rol;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;

class UsuarioType extends AbstractType
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
            ->add('password', PasswordType::class, [
                'label' => 'Contraseña',
                'mapped' => false, // Este campo no se mapea directamente a la entidad
                'required' => $options['is_new'],
                'constraints' => $options['is_new'] ? [
                    new NotBlank([
                        'message' => 'La contraseña no puede estar vacía.',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'La contraseña debe tener al menos {{ limit }} caracteres.',
                        'max' => 4096,
                    ]),
                ] : [],
            ])
            ->add('dni', TextType::class, [
                'label' => 'DNI',
                'constraints' => [
                    new NotBlank([
                        'message' => 'El DNI no puede estar vacío.',
                    ]),
                    new Length([
                        'max' => 20,
                        'maxMessage' => 'El DNI no puede superar los {{ limit }} caracteres.',
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
            ->add('rol', EntityType::class, [
                'class' => Rol::class,
                'choice_label' => 'nombre',
                'label' => 'Rol',
                'placeholder' => 'Selecciona un rol',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Debes asignar un rol al usuario.',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Usuario::class,
            'is_new' => false, // Por defecto, asumimos que no es un nuevo usuario
        ]);
    }
}
