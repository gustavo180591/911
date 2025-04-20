<?php

namespace App\Form;

use App\Entity\Ubicacion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UbicacionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('calle', TextType::class, [
                'label' => 'Calle',
                'constraints' => [
                    new NotBlank([
                        'message' => 'La calle no puede estar vacía.',
                    ]),
                    new Length([
                        'max' => 255,
                        'maxMessage' => 'La calle no puede superar los {{ limit }} caracteres.',
                    ]),
                ],
            ])
            ->add('numero', TextType::class, [
                'label' => 'Número',
                'required' => false,
                'constraints' => [
                    new Length([
                        'max' => 20,
                        'maxMessage' => 'El número no puede superar los {{ limit }} caracteres.',
                    ]),
                ],
            ])
            
            // Campo oculto para "coordenadas"
            ->add('coordenadas', HiddenType::class, [
                'required' => false,
                'constraints' => [
                    new Length([
                        'max' => 255,
                        'maxMessage' => 'Las coordenadas no pueden superar los {{ limit }} caracteres.',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ubicacion::class,
        ]);
    }
}
