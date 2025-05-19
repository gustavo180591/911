<?php

namespace App\Form;

use App\Entity\Evidencia;
use Symfony\Component\Form\AbstractType;
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
            ->add('imageFile', FileType::class, [
                'label' => 'Archivo de Evidencia',
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Debes subir un archivo.',
                    ]),
                    new File([
                        'maxSize' => '100M',
                        'mimeTypes' => [
                            'image/*',
                            'video/*',
                            'audio/*',
                            'application/pdf',
                        ],
                        'mimeTypesMessage' => 'Por favor sube un archivo válido (imagen, video, audio o PDF).',
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
