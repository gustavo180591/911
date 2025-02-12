<?php

namespace App\Form;

use App\Entity\Denuncia;
use App\Form\EvidenciaType;
use App\Form\BootstrapCollectionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

// IMPORTS necesarios (asegúrate de tenerlos):
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;

use DateTime;
use DateTimeZone;

class DenunciaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Campo de descripción
            ->add('descripcion', TextareaType::class, [
                'label' => 'Descripción de la Denuncia',
                'constraints' => [
                    new NotBlank([
                        'message' => 'La descripción no puede estar vacía.',
                    ]),
                    new Length([
                        'max' => 2000,
                        'maxMessage' => 'La descripción no puede superar los {{ limit }} caracteres.',
                    ]),
                ],
                'attr' => [
                    'placeholder' => 'Describe brevemente la denuncia...',
                    'rows' => 3,
                    'class' => 'form-control',
                ],
            ])

            // Fecha/Hora del incidente
            ->add('fechaHora', DateTimeType::class, [
                'label' => 'Fecha y Hora del Incidente',
                'widget' => 'single_text',
                'required' => true,
                'data' => new DateTime('now', new DateTimeZone('America/Argentina/Buenos_Aires')),
                'attr' => [
                    'class' => 'form-control',
                    'readonly' => true, // para que no sea editable
                ],
            ])

            // Dirección (texto) para sincronizar con Leaflet
            ->add('direccion', TextType::class, [
                'label' => 'Dirección',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Escribe la dirección...',
                ],
            ])

            // Latitud y Longitud (ocultas)
            ->add('latitud', HiddenType::class)
            ->add('longitud', HiddenType::class)

            // Evidencias (colección)
            ->add('evidencias', BootstrapCollectionType::class, [
                'entry_type' => EvidenciaType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => 'Evidencias',
            ])

            // Botón de envío
            ->add('submit', SubmitType::class, [
                'label' => 'Registrar Denuncia',
                'attr' => [
                    'class' => 'btn btn-primary',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Denuncia::class,
        ]);
    }
}
