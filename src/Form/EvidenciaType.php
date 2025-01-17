<?php

namespace App\Form;

use App\Entity\Evidencia;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class EvidenciaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('tipo', ChoiceType::class, [
                'label' => 'Tipo de Evidencia',
                'choices' => [
                    'Imagen' => 'imagen',
                    'Video' => 'video',
                    'Audio' => 'audio',
                    'Documento' => 'documento',
                ],
                'placeholder' => 'Selecciona un tipo',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Debes seleccionar un tipo de evidencia.',
                    ]),
                ],
            ])
            ->add('rutaArchivo', FileType::class, [
                'label' => 'Archivo de Evidencia',
                'mapped' => false, // No se vincula directamente con la entidad
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Debes subir un archivo.',
                    ]),
                    new File([
                        'maxSize' => '10M',
                        'mimeTypes' => [
                            'image/*',
                            'video/*',
                            'audio/*',
                            'application/pdf',
                            'application/msword',
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        ],
                        'mimeTypesMessage' => 'Por favor sube un archivo válido (imagen, video, audio o documento).',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Evidencia::class,
        ]);
    }
}
