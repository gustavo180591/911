<?php

namespace App\Form;

use App\Entity\Notificacion;
use App\Entity\User;
use App\Entity\Denuncia;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class NotificacionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('mensaje', TextareaType::class, [
                'label' => 'Mensaje',
                'constraints' => [
                    new NotBlank([
                        'message' => 'El mensaje no puede estar vacío.',
                    ]),
                    new Length([
                        'max' => 1000,
                        'maxMessage' => 'El mensaje no puede superar los {{ limit }} caracteres.',
                    ]),
                ],
                'attr' => [
                    'rows' => 4,
                    'placeholder' => 'Escribe el mensaje de la notificación...'
                ],
            ])
            ->add('usuario', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'email',
                'label' => 'Usuario',
                'placeholder' => 'Selecciona un usuario',
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Debes seleccionar un usuario.',
                    ]),
                ],
            ])
            ->add('denuncia', EntityType::class, [
                'class' => Denuncia::class,
                'choice_label' => 'descripcion',
                'label' => 'Denuncia (Opcional)',
                'placeholder' => 'Asociar a una denuncia (opcional)',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Notificacion::class,
        ]);
    }
}
