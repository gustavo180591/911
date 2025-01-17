<?php

namespace App\Form;

use App\Entity\Estadistica;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;

class EstadisticaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('tipoDenuncia', TextType::class, [
                'label' => 'Tipo de Denuncia',
                'constraints' => [
                    new NotBlank([
                        'message' => 'El tipo de denuncia no puede estar vacío.',
                    ]),
                    new Length([
                        'max' => 255,
                        'maxMessage' => 'El tipo de denuncia no puede superar los {{ limit }} caracteres.',
                    ]),
                ],
            ])
            ->add('cantidad', IntegerType::class, [
                'label' => 'Cantidad',
                'constraints' => [
                    new NotBlank([
                        'message' => 'La cantidad no puede estar vacía.',
                    ]),
                    new Positive([
                        'message' => 'La cantidad debe ser un número positivo.',
                    ]),
                ],
            ])
            ->add('fechaGeneracion', DateTimeType::class, [
                'label' => 'Fecha de Generación',
                'widget' => 'single_text',
                'constraints' => [
                    new NotBlank([
                        'message' => 'La fecha de generación no puede estar vacía.',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Estadistica::class,
        ]);
    }
}
