<?php

namespace App\Controller\Auth;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Psr\Log\LoggerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class RegisterController extends AbstractController
{
    private $entityManager;
    private $logger;
    private $slugger;

    public function __construct(
        EntityManagerInterface $entityManager,
        LoggerInterface $logger = null,
        SluggerInterface $slugger
    ) {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        $this->slugger = $slugger;
    }

    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $this->log('Iniciando proceso de registro de usuario');
        
        // Asegurar que el directorio de uploads existe
        $this->ensureUploadDirectoryExists();
        
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        // Si el formulario se envió, manejarlo
        if ($form->isSubmitted()) {
            $this->log('Formulario enviado');
            
            if ($form->isValid()) {
                $this->log('Formulario válido, procesando datos');
                try {
                    // Verificar si el usuario ya existe
                    $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['username' => $user->getUsername()]);
                    if ($existingUser) {
                        $this->log('Error: Nombre de usuario ya existente: ' . $user->getUsername());
                        $this->addFlash('error', 'El nombre de usuario "' . $user->getUsername() . '" ya está en uso. Por favor, elija otro.');
                        return $this->redirectToRoute('app_register');
                    }

                    // Verificar si el email ya existe
                    $existingEmail = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $user->getEmail()]);
                    if ($existingEmail) {
                        $this->log('Error: Email ya existente: ' . $user->getEmail());
                        $this->addFlash('error', 'El correo electrónico "' . $user->getEmail() . '" ya está registrado. Por favor, utilice otro o recupere su contraseña.');
                        return $this->redirectToRoute('app_register');
                    }
                    
                    // Verificar si el DNI ya existe
                    $existingDni = $this->entityManager->getRepository(User::class)->findOneBy(['dni' => $user->getDni()]);
                    if ($existingDni) {
                        $this->log('Error: DNI ya existente: ' . $user->getDni());
                        $this->addFlash('error', 'El DNI "' . $user->getDni() . '" ya está registrado en el sistema. Si es suyo, contacte con soporte.');
                        return $this->redirectToRoute('app_register');
                    }

                    // Verificar si se marcó la declaración jurada
                    if (!$user->isDeclaracionJurada()) {
                        $this->log('Error: Declaración jurada no aceptada');
                        $this->addFlash('error', 'Debe aceptar la declaración jurada para completar el registro');
                        return $this->redirectToRoute('app_register');
                    }

                    // Guardar el código de país del teléfono
                    $countryCode = $request->request->get('countryCode', '+54'); // Default a +54 si no se encuentra
                    $telefono = $countryCode . $form->get('telefono')->getData();
                    $user->setTelefono($telefono);
                    $this->log('Teléfono formateado con código de país: ' . $telefono);
                    
                    // Verificar si el teléfono ya existe
                    $existingTelefono = $this->entityManager->getRepository(User::class)->findOneBy(['telefono' => $telefono]);
                    if ($existingTelefono) {
                        $this->log('Error: Teléfono ya existente: ' . $telefono);
                        $this->addFlash('error', 'El número de teléfono "' . $telefono . '" ya está registrado en el sistema.');
                        return $this->redirectToRoute('app_register');
                    }
                    
                    // Hashear la contraseña
                    $hashedPassword = $passwordHasher->hashPassword(
                        $user, 
                        $form->get('plainPassword')->getData()
                    );
                    $user->setPassword($hashedPassword);
                    $this->log('Contraseña hasheada exitosamente');
                    
                    // Asignar roles
                    $user->setRoles(['ROLE_USER']);

                    // Procesar las imágenes
                    $uploadedFiles = [
                        'fotoRostro' => $form->get('fotoRostro')->getData(),
                        'fotoDniFrente' => $form->get('fotoDniFrente')->getData(),
                        'fotoDniDorso' => $form->get('fotoDniDorso')->getData()
                    ];
                    
                    foreach ($uploadedFiles as $field => $uploadedFile) {
                        if ($uploadedFile instanceof UploadedFile) {
                            $this->log("Procesando $field: " . $uploadedFile->getClientOriginalName());

                            // Validar el archivo
                            $this->validateFile($uploadedFile);
                            
                            // Crear un nombre de archivo seguro
                            $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
                            $safeFilename = $this->slugger->slug($originalFilename);
                            $newFilename = $safeFilename . '-' . uniqid() . '.' . $uploadedFile->guessExtension();
                            
                            // Mover el archivo al directorio correcto
                            try {
                                $uploadDir = $this->getParameter('user_photos_directory');
                                
                                // Crear el directorio si no existe
                                if (!file_exists($uploadDir)) {
                                    mkdir($uploadDir, 0777, true);
                                    $this->log("Directorio creado: $uploadDir");
                                }
                                
                                $uploadedFile->move(
                                    $uploadDir,
                                    $newFilename
                                );
                                $this->log("Archivo movido exitosamente: $newFilename");
                                
                                // Guardar la ruta en la entidad
                                $setter = 'set' . ucfirst($field);
                                if (method_exists($user, $setter)) {
                                    $user->$setter($newFilename);
                                    $this->log("Campo $field actualizado con $newFilename");
                                    
                                    // Verificar inmediatamente que se haya establecido
                                    $getter = 'get' . ucfirst($field);
                                    if (method_exists($user, $getter)) {
                                        $storedValue = $user->$getter();
                                        $this->log("Verificación inmediata de $field: " . ($storedValue ?: 'NULL'));
                                    }
                                } else {
                                    $this->log("Error: Método $setter no existe");
                                }
                            } catch (FileException $e) {
                                $this->log('Error al mover archivo: ' . $e->getMessage());
                                throw new \Exception('Ha ocurrido un error al subir el archivo: ' . $e->getMessage());
                            }
                        } else {
                            $this->log("Error: $field no es un archivo válido");
                            $this->addFlash('error', "El campo $field es requerido");
                            return $this->redirectToRoute('app_register');
                        }
                    }
                    
                    // Verificar que los archivos se hayan guardado correctamente en la entidad
                    $this->log("Verificando campos de la entidad antes de persistir:");
                    $this->log("fotoRostro: " . ($user->getFotoRostro() ?: 'NULL'));
                    $this->log("fotoDniFrente: " . ($user->getFotoDniFrente() ?: 'NULL'));
                    $this->log("fotoDniDorso: " . ($user->getFotoDniDorso() ?: 'NULL'));
                    
                    if (!$user->getFotoRostro() || !$user->getFotoDniFrente() || !$user->getFotoDniDorso()) {
                        $this->log("Error: Faltan imágenes requeridas en la entidad");
                        $this->addFlash('error', 'Error al procesar las imágenes. Por favor, intente nuevamente.');
                        return $this->redirectToRoute('app_register');
                    }
                    
                    // Guardar el usuario en la base de datos
                    $this->entityManager->persist($user);
                    $this->log('Usuario persistido, ejecutando flush');
                    try {
                        $this->entityManager->flush();
                        $this->log('Usuario guardado exitosamente en la base de datos con ID: ' . $user->getId());
                    } catch (\Exception $e) {
                        $this->log('Error durante el flush: ' . $e->getMessage());
                        $this->log('Traza de error: ' . $e->getTraceAsString());
                        throw $e;
                    }

                    $this->addFlash('success', 'Registro exitoso! Ahora puedes iniciar sesión.');
                    return $this->redirectToRoute('auth_success');
                    
                } catch (UniqueConstraintViolationException $e) {
                    // Verificar qué tipo de restricción se violó basado en el mensaje de error
                    $this->log('Error de restricción única en la base de datos: ' . $e->getMessage());
                    
                    if (strpos($e->getMessage(), 'UNIQ_8D93D6497F8F253B') !== false) {
                        $this->addFlash('error', 'El DNI ingresado ya está registrado en el sistema. Si este DNI es suyo, contacte con soporte.');
                    } else if (strpos($e->getMessage(), 'UNIQ_8D93D649F85E0677') !== false || strpos($e->getMessage(), 'username') !== false) {
                        $this->addFlash('error', 'El nombre de usuario "' . $user->getUsername() . '" ya está registrado. Por favor, elija otro.');
                    } else if (strpos($e->getMessage(), 'UNIQ_8D93D649E7927C74') !== false || strpos($e->getMessage(), 'email') !== false) {
                        $this->addFlash('error', 'El correo electrónico "' . $user->getEmail() . '" ya está registrado. Por favor, utilice otro o recupere su contraseña.');
                    } else if (strpos($e->getMessage(), 'UNIQ_8D93D649450FF010') !== false || strpos($e->getMessage(), 'telefono') !== false) {
                        $this->addFlash('error', 'El número de teléfono "' . $user->getTelefono() . '" ya está registrado en el sistema.');
                    } else {
                        $this->addFlash('error', 'Error de duplicación de datos. Por favor, verifique la información proporcionada y asegúrese de que no esté ya registrada.');
                    }
                    return $this->redirectToRoute('app_register');
                } catch (FileException $e) {
                    $this->log('Error al procesar archivos: ' . $e->getMessage());
                    $this->addFlash('error', 'Error al procesar las imágenes: ' . $e->getMessage());
                    return $this->redirectToRoute('app_register');
                } catch (\Exception $e) {
                    $this->log('Error general durante el registro: ' . $e->getMessage());
                    $this->log('Traza: ' . $e->getTraceAsString());
                    $this->addFlash('error', 'Error durante el registro: ' . $e->getMessage());
                    return $this->redirectToRoute('app_register');
                }
            } else {
                $this->log('Formulario inválido, procesando errores');
                // Procesar errores específicos por campo para mostrarlos junto al campo
                foreach ($form->getErrors(true) as $error) {
                    $errorMsg = $error->getMessage();
                    $this->log('Error de validación: ' . $errorMsg);
                    
                    // Obtener el origen del error (campo al que pertenece)
                    $origin = $error->getOrigin();
                    if ($origin) {
                        $fieldName = $origin->getName();
                        $this->log("Campo con error: $fieldName");
                    }
                    
                    // Agregar todos los errores como mensajes flash para simplicidad
                    $this->addFlash('error', $errorMsg);
                }
                
                // No redirigir para mantener el estado del formulario con los errores
                return $this->render('auth/register.html.twig', [
                    'registrationForm' => $form->createView(),
                    'phoneCodes' => $this->getPhoneCodes(),
                ]);
            }
        }

        return $this->render('auth/register.html.twig', [
            'registrationForm' => $form->createView(),
            'phoneCodes' => $this->getPhoneCodes(),
        ]);
    }

    #[Route('/register/success', name: 'auth_success')]
    public function success(): Response
    {
        return $this->render('auth/success.html.twig');
    }
    
    /**
     * Valida un archivo según los parámetros definidos
     * 
     * @param UploadedFile $file Archivo a validar
     * @throws \Exception Si el archivo no cumple las validaciones
     */
    private function validateFile(UploadedFile $file): void
    {
        $this->log("Validando archivo: " . $file->getClientOriginalName());
        
        // Validar tamaño máximo (5MB)
        $maxSize = $this->getParameter('images.max_size');
        if ($file->getSize() > $maxSize) {
            $errorMsg = sprintf(
                'El archivo es demasiado grande (%.2f MB). El tamaño máximo permitido es %.2f MB.',
                $file->getSize() / 1048576,
                $maxSize / 1048576
            );
            $this->log("Error: " . $errorMsg);
            throw new \Exception($errorMsg);
        }

        // Validar tipo MIME
        $allowedMimeTypes = $this->getParameter('images.allowed_mimetypes');
        $mimeType = $file->getMimeType();
        $this->log("Tipo MIME del archivo: {$mimeType}");
        
        if (!in_array($mimeType, $allowedMimeTypes)) {
            $errorMsg = sprintf(
                'El tipo de archivo "%s" no está permitido. Los tipos permitidos son: %s.',
                $mimeType,
                implode(', ', array_map(function ($mime) {
                    return str_replace('image/', '', $mime);
                }, $allowedMimeTypes))
            );
            $this->log("Error: " . $errorMsg);
            throw new \Exception($errorMsg);
        }

        // Validar extensión
        $allowedExtensions = $this->getParameter('images.allowed_extensions');
        $extension = strtolower($file->getClientOriginalExtension());
        $this->log("Extensión del archivo: {$extension}");
        
        if (!in_array($extension, $allowedExtensions)) {
            $errorMsg = sprintf(
                'La extensión "%s" no está permitida. Las extensiones permitidas son: %s.',
                $extension,
                implode(', ', $allowedExtensions)
            );
            $this->log("Error: " . $errorMsg);
            throw new \Exception($errorMsg);
        }

        // Verificar que el archivo sea realmente una imagen
        $this->log("Validación del archivo completada con éxito");
    }

    private function getPhoneCodes(): array
    {
        return [
            ['name' => 'Argentina', 'phoneCode' => '+54', 'flagEmoji' => '🇦🇷'],
            ['name' => 'Brasil', 'phoneCode' => '+55', 'flagEmoji' => '🇧🇷'],
            ['name' => 'Chile', 'phoneCode' => '+56', 'flagEmoji' => '🇨🇱'],
            ['name' => 'Colombia', 'phoneCode' => '+57', 'flagEmoji' => '🇨🇴'],
            ['name' => 'Ecuador', 'phoneCode' => '+593', 'flagEmoji' => '🇪🇨'],
            ['name' => 'México', 'phoneCode' => '+52', 'flagEmoji' => '🇲🇽'],
            ['name' => 'Perú', 'phoneCode' => '+51', 'flagEmoji' => '🇵🇪'],
            ['name' => 'Uruguay', 'phoneCode' => '+598', 'flagEmoji' => '🇺🇾'],
            ['name' => 'Venezuela', 'phoneCode' => '+58', 'flagEmoji' => '🇻🇪'],
        ];
    }
    
    /**
     * Registra un mensaje en el logger si está disponible
     */
    private function log(string $message): void
    {
        if ($this->logger) {
            $this->logger->info('[RegisterController] ' . $message);
        }
        
        // También escribimos en un archivo de log para depuración
        $logDir = $this->getParameter('kernel.project_dir') . '/var/log';
        if (!file_exists($logDir)) {
            mkdir($logDir, 0777, true);
        }
        
        $logFile = $logDir . '/register_controller.log';
        $logMessage = '[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL;
        
        try {
            file_put_contents($logFile, $logMessage, FILE_APPEND);
            // Asegurar que el archivo tenga permisos correctos
            chmod($logFile, 0777);
        } catch (\Exception $e) {
            // Si no podemos escribir en el log, al menos lo mostramos en la consola
            error_log("[RegisterController Error] No se pudo escribir en el log: " . $e->getMessage());
            error_log("[RegisterController] " . $message);
        }
    }

    /**
     * Asegura que el directorio de uploads exista y tenga los permisos correctos
     */
    private function ensureUploadDirectoryExists(): void
    {
        $uploadDir = $this->getParameter('user_photos_directory');
        
        if (!file_exists($uploadDir)) {
            $this->log("Creando directorio de uploads: $uploadDir");
            if (!mkdir($uploadDir, 0777, true)) {
                $this->log("Error: No se pudo crear el directorio $uploadDir");
            } else {
                $this->log("Directorio creado exitosamente: $uploadDir");
                chmod($uploadDir, 0777);
            }
        } else {
            $this->log("Directorio de uploads ya existe: $uploadDir");
            // Asegurar permisos correctos
            chmod($uploadDir, 0777);
        }
    }
} 