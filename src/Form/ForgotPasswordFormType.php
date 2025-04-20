<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;

class ForgotPasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('email', EmailType::class, [
            'label' => 'Correo Electrónico',
            'attr' => [
                'class' => 'form-control',
                'placeholder' => 'Introduce tu correo electrónico'
            ],
            'constraints' => [
                new NotBlank([
                    'message' => 'El correo es obligatorio.'
                ]),
                new Email([
                    'message' => 'Introduce un correo válido.'
                ])
            ]
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([]);
    }
}
