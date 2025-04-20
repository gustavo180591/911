<?php

namespace App\Form;

use App\Entity\Reporte;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ReporteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Adaptamos el formulario para comentarios, usando solo el campo descripción
        $builder
            ->add('descripcion', TextareaType::class, [
                'label'       => 'Comentario',
                'required'    => true,
                'attr'        => [
                    'rows'        => 5,
                    'placeholder' => 'Escribe tu comentario...',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'El comentario no puede estar vacío.',
                    ]),
                    new Length([
                        'max'        => 1000,
                        'maxMessage' => 'El comentario no puede superar los {{ limit }} caracteres.',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reporte::class,
        ]);
    }
}
