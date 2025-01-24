<?php

namespace App\Form;

use App\Entity\Denuncia;
use App\Entity\Evidencia;
use App\Entity\CategoriaDenuncia;
use App\Entity\EstadoDenuncia;
use App\Entity\Ubicacion;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class DenunciaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
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
            ])
            ->add('fechaHora', DateTimeType::class, [
                'label' => 'Fecha y Hora del Incidente',
                'widget' => 'single_text',
                'required' => true,
            ])
            ->add('categoria', EntityType::class, [
                'class' => CategoriaDenuncia::class,
                'choice_label' => 'nombre',
                'label' => 'Categoría',
                'placeholder' => 'Selecciona una categoría',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Debes seleccionar una categoría.',
                    ]),
                ],
            ])
            ->add('direccion', TextType::class, [
                'label' => 'Ubicación (Dirección)',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Escribe la dirección...',
                    'class' => 'form-control',
                ],
            ])
            ->add('ubicacion', EntityType::class, [
                'class' => Ubicacion::class,
                'choice_label' => function (Ubicacion $ubicacion) {
                    return $ubicacion->getCalle() . ' ' . $ubicacion->getNumero();
                },
                'label' => 'Ubicación Seleccionada',
                'required' => false,
                'attr' => [
                    'class' => 'form-select',
                ],
                'placeholder' => 'Selecciona una ubicación existente',
            ])
            ->add('evidencias', FileType::class, [
                'label' => 'Evidencias (Fotos, Videos, Audios)',
                'multiple' => true, // Permitir múltiples archivos
                'mapped' => false,  // No está mapeado directamente a la entidad
                'required' => false,
                'attr' => [
                    'accept' => 'image/*,video/*,audio/*', // Aceptar solo imágenes, videos y audios
                    'class' => 'form-control',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Denuncia::class,
        ]);
    }
}