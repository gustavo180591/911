<?php 
namespace App\Form;

use App\Entity\Denuncia;
use App\Entity\Ubicacion;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use DateTime;
use DateTimeZone;

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
                'data' => new \DateTime('now', new \DateTimeZone('America/Argentina/Buenos_Aires')), // Establecer valor por defecto
            ])                     

            
            ->add('evidencias', CollectionType::class, [
                'entry_type' => EvidenciaType::class, // Usa el formulario EvidenciaType
                'entry_options' => ['label' => false], // No mostrar etiquetas para cada evidencia
                'allow_add' => true, // Permitir agregar nuevas evidencias dinámicamente
                'allow_delete' => true, // Permitir eliminar evidencias
                'by_reference' => false, // Importante para que Symfony maneje correctamente la relación
                'label' => 'Evidencias',
                'attr' => [
                    'class' => 'evidencias-collection', // Clase CSS para estilos
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