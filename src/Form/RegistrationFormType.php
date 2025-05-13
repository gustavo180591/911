<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class RegistrationFormType extends AbstractType
{
    private $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $maxSize = $this->params->get('images.max_size');
        $allowedMimeTypes = $this->params->get('images.allowed_mimetypes');
        
        $builder
            ->add('nombre', TextType::class, [
                'label' => 'Nombre',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor ingrese su nombre',
                    ]),
                ],
            ])
            ->add('apellido', TextType::class, [
                'label' => 'Apellido',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor ingrese su apellido',
                    ]),
                ],
            ])
            ->add('dni', TextType::class, [
                'label' => 'DNI',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor ingrese su DNI',
                    ]),
                ],
            ])
            ->add('direccion', TextType::class, [
                'label' => 'Dirección',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor ingrese su dirección',
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Correo electrónico',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor ingrese su correo electrónico',
                    ]),
                ],
            ])
            ->add('username', TextType::class, [
                'label' => 'Nombre de Usuario',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor ingrese un nombre de usuario',
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'El nombre de usuario debe tener al menos {{ limit }} caracteres',
                        'max' => 180,
                    ]),
                    new Regex([
                        'pattern' => '/^[a-zA-Z0-9_]+$/',
                        'message' => 'El nombre de usuario solo puede contener letras, números y guiones bajos',
                    ]),
                ],
            ])
            ->add('telefono', TextType::class, [
                'label' => 'Teléfono',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor ingrese su número de teléfono',
                    ]),
                ],
            ])
            ->add('fotoRostro', FileType::class, [
                'label' => 'Foto de Rostro',
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor suba una foto de su rostro',
                    ]),
                    new File([
                        'maxSize' => $maxSize,
                        'maxSizeMessage' => 'El archivo es demasiado grande ({{ size }} {{ suffix }}). El tamaño máximo permitido es {{ limit }} {{ suffix }}.',
                        'mimeTypes' => $allowedMimeTypes,
                        'mimeTypesMessage' => 'Por favor suba una imagen válida (JPG o PNG)',
                    ]),
                ],
                'attr' => [
                    'accept' => 'image/jpeg, image/png',
                    'data-max-size' => $maxSize,
                ],
            ])
            ->add('fotoDniFrente', FileType::class, [
                'label' => 'Foto DNI (Frente)',
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor suba una foto del frente de su DNI',
                    ]),
                    new File([
                        'maxSize' => $maxSize,
                        'maxSizeMessage' => 'El archivo es demasiado grande ({{ size }} {{ suffix }}). El tamaño máximo permitido es {{ limit }} {{ suffix }}.',
                        'mimeTypes' => $allowedMimeTypes,
                        'mimeTypesMessage' => 'Por favor suba una imagen válida (JPG o PNG)',
                    ]),
                ],
                'attr' => [
                    'accept' => 'image/jpeg, image/png',
                    'data-max-size' => $maxSize,
                ],
            ])
            ->add('fotoDniDorso', FileType::class, [
                'label' => 'Foto DNI (Dorso)',
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor suba una foto del dorso de su DNI',
                    ]),
                    new File([
                        'maxSize' => $maxSize,
                        'maxSizeMessage' => 'El archivo es demasiado grande ({{ size }} {{ suffix }}). El tamaño máximo permitido es {{ limit }} {{ suffix }}.',
                        'mimeTypes' => $allowedMimeTypes,
                        'mimeTypesMessage' => 'Por favor suba una imagen válida (JPG o PNG)',
                    ]),
                ],
                'attr' => [
                    'accept' => 'image/jpeg, image/png',
                    'data-max-size' => $maxSize,
                ],
            ])
            ->add('declaracionJurada', CheckboxType::class, [
                'label' => 'Declaración Jurada',
                'label_attr' => [
                    'class' => 'declaration-label'
                ],
                'label_html' => true,
                'mapped' => true,
                'required' => true,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Debe aceptar la declaración jurada para continuar',
                    ]),
                ],
                'attr' => [
                    'class' => 'declaration-input',
                    'data-toggle' => 'tooltip',
                    'data-placement' => 'top',
                    'title' => 'Al marcar esta casilla declaro que todos los datos proporcionados son verdaderos y que tomo conocimiento que en caso de falsa denuncia se aplicará lo establecido en el artículo 245 del código penal'
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => 'Contraseña',
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor ingrese una contraseña',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'La contraseña debe tener al menos {{ limit }} caracteres',
                        'max' => 4096,
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
