<?php

namespace App\Form;

use App\Entity\Denuncia;
use App\Entity\CategoriaDenuncia;
use App\Entity\EstadoDenuncia;
use App\Entity\Ubicacion;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

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
            ->add('estado', EntityType::class, [
                'class' => EstadoDenuncia::class,
                'choice_label' => 'nombre',
                'label' => 'Estado',
                'placeholder' => 'Selecciona un estado',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Debes seleccionar un estado.',
                    ]),
                ],
            ])
            ->add('ubicacion', EntityType::class, [
                'class' => Ubicacion::class,
                'choice_label' => function (Ubicacion $ubicacion) {
                    return $ubicacion->getCalle() . ' ' . $ubicacion->getNumero();
                },
                'label' => 'Ubicación',
                'placeholder' => 'Selecciona una ubicación',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Denuncia::class,
        ]);
    }
}
