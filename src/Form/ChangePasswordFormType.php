<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ChangePasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('currentPassword', PasswordType::class, [
                'label' => 'Contraseña actual',
                'constraints' => [
                    new NotBlank([
                        'message' => 'La contraseña actual no puede estar vacía.',
                    ]),
                ],
                'mapped' => false, // No se vincula directamente con la entidad
            ])
            ->add('newPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
                    'label' => 'Nueva contraseña',
                ],
                'second_options' => [
                    'label' => 'Repite la nueva contraseña',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'La nueva contraseña no puede estar vacía.',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'La nueva contraseña debe tener al menos {{ limit }} caracteres.',
                        'max' => 4096,
                    ]),
                ],
                'invalid_message' => 'Las contraseñas no coinciden.',
                'mapped' => false, // No se vincula directamente con la entidad
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
