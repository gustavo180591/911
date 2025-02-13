<?php

namespace App\Form;

use App\Entity\Denuncia;
use App\Form\UbicacionType;
use App\Form\EvidenciaType;
use App\Form\BootstrapCollectionType;
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

            // Ubicación (sub-form)
            ->add('ubicacion', UbicacionType::class, [
                'label' => 'Ubicación Detallada',
                'required' => false,
            ])

            // Evidencias
            ->add('evidencias', BootstrapCollectionType::class, [
                'entry_type'   => EvidenciaType::class,
                'allow_add'    => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label'        => 'Evidencias',
            ])

            // Botón de envío
            ->add('submit', SubmitType::class, [
                'label' => 'Registrar Denuncia',
                'attr'  => [
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
