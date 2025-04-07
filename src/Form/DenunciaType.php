<?php

namespace App\Form;

use App\Entity\Denuncia;
use App\Form\UbicacionType;
use App\Form\EvidenciaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
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
            // Descripción
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

            // Fecha/Hora
            ->add('fechaHora', DateTimeType::class, [
                'label' => 'Fecha y Hora del Incidente',
                'widget' => 'single_text',
                'required' => true,
                // Se inicializa con la fecha/hora actual (zona: Argentina/Buenos_Aires)
                'data' => new DateTime('now', new DateTimeZone('America/Argentina/Buenos_Aires')),
                'attr' => [
                    'class' => 'form-control',
                    'readonly' => true, // evitas que el usuario la modifique
                ],
            ])
            // Evidencias
            ->add('evidencias', CollectionType::class, [
                'entry_type' => EvidenciaType::class, // Usa el formulario EvidenciaType
                'entry_options' => ['label' => false], // Oculta las etiquetas de cada entrada
                'allow_add' => true, // Permite agregar nuevas evidencias
                'allow_delete' => true, // Permite eliminar evidencias
                'by_reference' => false, // Importante para que funcione correctamente
                'label' => false,
            ])
            // Ubicación
            ->add('ubicacion', UbicacionType::class, [
                'label' => false,
            ])
            // Botón de envío
            ->add('submit', SubmitType::class, [
                'label' => 'Registrar Incidente',
                'attr'  => [
                    'class' => 'btn btn-primary',
                    'style' => 'display:none;', // Ocultar el botón
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