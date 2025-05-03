<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\Denuncia; // Asegúrate de importar la entidad Denuncia
use App\Entity\Reporte;

#[ORM\Entity]
class Usuario implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;
    
    /**
     * Importante: aquí es donde Symfony guardará los roles en la base
     * como un JSON (p. ej. ["ROLE_USER", "ROLE_ADMIN"]).
     */
    #[ORM\Column(type: 'json')]
    private array $roles = [];
    
    #[ORM\Column(type: 'string', length: 255)]
    private ?string $nombre = null;
    
    #[ORM\Column(type: 'string', length: 255)]
    private ?string $apellido = null;
    
    #[ORM\Column(type: 'string', length: 20, unique: true, nullable: true)]
    private ?string $dni = null;
    
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $direccion = null;
    
    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private ?string $email = null;
    
    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private ?string $telefono = null;
    
    #[ORM\Column(type: 'string')]
    private ?string $password = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $fechaRegistro = null;

    // Agrega esta propiedad para las denuncias (o emergencias)
    #[ORM\OneToMany(mappedBy: 'usuario', targetEntity: Denuncia::class)]
    private Collection $denuncias;

    #[ORM\OneToMany(mappedBy: 'usuario', targetEntity: Reporte::class)]
    private Collection $reportes;
    
    public function __construct()
    {
        $this->fechaRegistro = new \DateTime();
        $this->denuncias = new ArrayCollection();
        $this->reportes = new ArrayCollection();
    }

    // Métodos obligatorios de UserInterface / PasswordAuthenticatedUserInterface
    // ----------------------------------------------------------------------------

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserIdentifier(): string
    {
        // Retornamos el email como identificador único
        return $this->email ?: '';
    }

    public function eraseCredentials(): void
    {
        // Si tienes datos sensibles temporales, límpialos aquí
    }

    /**
     * Retorna los roles de este usuario como un array de strings.
     * Si el array está vacío, garantizamos al menos "ROLE_USER".
     */
    public function getRoles(): array
    {
        // Garantiza que tenga al menos ROLE_USER
        $roles = $this->roles;
        if (empty($roles)) {
            $roles[] = 'ROLE_USER';
        }
        // Eliminamos duplicados si se diera el caso
        return array_unique($roles);
    }

    /**
     * Permite establecer (o sobrescribir) los roles del usuario.
     * Ejemplo: $usuario->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    // ----------------------------------------------------------------------------
    // Métodos de PasswordAuthenticatedUserInterface
    // (getPassword/setPassword ya definidos, implementa lo que necesites).
    // ----------------------------------------------------------------------------

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    // ----------------------------------------------------------------------------
    // Resto de propiedades
    // ----------------------------------------------------------------------------

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;
        return $this;
    }

    public function getApellido(): ?string
    {
        return $this->apellido;
    }

    public function setApellido(string $apellido): self
    {
        $this->apellido = $apellido;
        return $this;
    }

    public function getDni(): ?string
    {
        return $this->dni;
    }

    public function setDni(?string $dni): self
    {
        $this->dni = $dni;
        return $this;
    }

    public function getTelefono(): ?string
    {
        return $this->telefono;
    }

    public function setTelefono(?string $telefono): self
    {
        $this->telefono = $telefono;
        return $this;
    }

    public function getDireccion(): ?string
    {
        return $this->direccion;
    }

    public function setDireccion(?string $direccion): self
    {
        $this->direccion = $direccion;
        return $this;
    }
    public function getFechaRegistro(): ?\DateTimeInterface
    {
        return $this->fechaRegistro;
    }

    public function setFechaRegistro(\DateTimeInterface $fechaRegistro): self
    {
        $this->fechaRegistro = $fechaRegistro;
        return $this;
    }
    // Getter para la colección de denuncias
    public function getDenuncias(): Collection
    {
        return $this->denuncias;
    }

    // Opcional: si prefieres llamarla "emergencies" en la plantilla,
    // puedes agregar un getter alias:
    public function getEmergencies(): Collection
    {
        return $this->denuncias;
    }

    public function getReportes(): Collection
    {
        return $this->reportes;
    }

    public function addReporte(Reporte $reporte): self
    {
        if (!$this->reportes->contains($reporte)) {
            $this->reportes->add($reporte);
            $reporte->setUsuario($this);
        }
        return $this;
    }

    public function removeReporte(Reporte $reporte): self
    {
        if ($this->reportes->removeElement($reporte)) {
            if ($reporte->getUsuario() === $this) {
                $reporte->setUsuario(null);
            }
        }
        return $this;
    }
}
