<?php

namespace App\Form;

use App\Entity\EstadoDenuncia;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class EstadoDenunciaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre', TextType::class, [
                'label' => 'Nombre del Estado',
                'constraints' => [
                    new NotBlank([
                        'message' => 'El nombre del estado no puede estar vacío.',
                    ]),
                    new Length([
                        'max' => 50,
                        'maxMessage' => 'El nombre no puede superar los {{ limit }} caracteres.',
                    ]),
                ],
            ])
            ->add('descripcion', TextareaType::class, [
                'label' => 'Descripción',
                'required' => false,
                'attr' => [
                    'rows' => 4,
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EstadoDenuncia::class,
        ]);
    }
}
