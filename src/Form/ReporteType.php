<?php

namespace App\Form;

use App\Entity\Reporte;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ReporteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titulo', TextType::class, [
                'label' => 'Título del Reporte',
                'constraints' => [
                    new NotBlank([
                        'message' => 'El título no puede estar vacío.',
                    ]),
                    new Length([
                        'max' => 255,
                        'maxMessage' => 'El título no puede superar los {{ limit }} caracteres.',
                    ]),
                ],
            ])
            ->add('descripcion', TextareaType::class, [
                'label' => 'Descripción',
                'required' => false,
                'attr' => [
                    'rows' => 5,
                    'placeholder' => 'Escribe los detalles del reporte...',
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
