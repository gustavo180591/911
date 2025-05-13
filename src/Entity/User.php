<?php

namespace App\Entity;

use App\Repository\UserRepository;
use App\Validator\Constraints as CustomAssert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank(message: 'El nombre de usuario no puede estar vacío')]
    #[Assert\Length(min: 3, max: 180, minMessage: 'El nombre de usuario debe tener al menos {{ limit }} caracteres', maxMessage: 'El nombre de usuario no puede tener más de {{ limit }} caracteres')]
    private ?string $username = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank(message: 'El correo electrónico no puede estar vacío')]
    #[Assert\Email(message: 'El correo electrónico {{ value }} no es válido')]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'El nombre no puede estar vacío')]
    private ?string $nombre = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'El apellido no puede estar vacío')]
    private ?string $apellido = null;

    #[ORM\Column(length: 20, unique: true, nullable: true)]
    #[Assert\NotBlank(message: 'El DNI no puede estar vacío')]
    private ?string $dni = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(message: 'La dirección no puede estar vacía')]
    private ?string $direccion = null;

    #[ORM\Column(length: 20, nullable: true)]
    #[Assert\NotBlank(message: 'El teléfono no puede estar vacío')]
    private ?string $telefono = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $fotoRostro = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $fotoDniFrente = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $fotoDniDorso = null;

    #[ORM\Column]
    #[Assert\IsTrue(message: 'Debe aceptar la declaración jurada')]
    #[CustomAssert\LegalDeclaration]
    private bool $declaracionJurada = false;

    #[ORM\Column(length: 20)]
    private string $estado = 'pendiente';

    #[ORM\Column]
    private ?\DateTimeImmutable $fechaRegistro = null;

    #[ORM\OneToMany(mappedBy: 'usuario', targetEntity: Denuncia::class)]
    private Collection $denuncias;

    #[ORM\OneToMany(mappedBy: 'usuario', targetEntity: Reporte::class)]
    private Collection $reportes;

    public function __construct()
    {
        $this->fechaRegistro = new \DateTimeImmutable();
        $this->denuncias = new ArrayCollection();
        $this->reportes = new ArrayCollection();
        $this->declaracionJurada = false;
        $this->estado = 'pendiente';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function eraseCredentials(): void
    {
        // Si tienes datos sensibles temporales, límpialos aquí
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): static
    {
        $this->nombre = $nombre;
        return $this;
    }

    public function getApellido(): ?string
    {
        return $this->apellido;
    }

    public function setApellido(string $apellido): static
    {
        $this->apellido = $apellido;
        return $this;
    }

    public function getDni(): ?string
    {
        return $this->dni;
    }

    public function setDni(?string $dni): static
    {
        $this->dni = $dni;
        return $this;
    }

    public function getDireccion(): ?string
    {
        return $this->direccion;
    }

    public function setDireccion(?string $direccion): static
    {
        $this->direccion = $direccion;
        return $this;
    }

    public function getTelefono(): ?string
    {
        return $this->telefono;
    }

    public function setTelefono(?string $telefono): static
    {
        $this->telefono = $telefono;
        return $this;
    }

    public function getFechaRegistro(): ?\DateTimeImmutable
    {
        return $this->fechaRegistro;
    }

    public function setFechaRegistro(\DateTimeImmutable $fechaRegistro): static
    {
        $this->fechaRegistro = $fechaRegistro;
        return $this;
    }

    public function getFotoRostro(): ?string
    {
        return $this->fotoRostro;
    }

    public function setFotoRostro(?string $fotoRostro): static
    {
        $this->fotoRostro = $fotoRostro;
        return $this;
    }

    public function getFotoDniFrente(): ?string
    {
        return $this->fotoDniFrente;
    }

    public function setFotoDniFrente(?string $fotoDniFrente): static
    {
        $this->fotoDniFrente = $fotoDniFrente;
        return $this;
    }

    public function getFotoDniDorso(): ?string
    {
        return $this->fotoDniDorso;
    }

    public function setFotoDniDorso(?string $fotoDniDorso): static
    {
        $this->fotoDniDorso = $fotoDniDorso;
        return $this;
    }

    public function isDeclaracionJurada(): bool
    {
        return $this->declaracionJurada;
    }

    public function setDeclaracionJurada(bool $declaracionJurada): static
    {
        $this->declaracionJurada = $declaracionJurada;
        return $this;
    }

    public function getEstado(): string
    {
        return $this->estado;
    }

    public function setEstado(string $estado): static
    {
        $this->estado = $estado;
        return $this;
    }

    public function isAprobado(): bool
    {
        return $this->estado === 'aprobado';
    }

    public function isPendiente(): bool
    {
        return $this->estado === 'pendiente';
    }

    public function isRechazado(): bool
    {
        return $this->estado === 'rechazado';
    }

    public function getDenuncias(): Collection
    {
        return $this->denuncias;
    }

    public function addDenuncia(Denuncia $denuncia): static
    {
        if (!$this->denuncias->contains($denuncia)) {
            $this->denuncias->add($denuncia);
            $denuncia->setUsuario($this);
        }
        return $this;
    }

    public function removeDenuncia(Denuncia $denuncia): static
    {
        if ($this->denuncias->removeElement($denuncia)) {
            if ($denuncia->getUsuario() === $this) {
                $denuncia->setUsuario(null);
            }
        }
        return $this;
    }

    public function getReportes(): Collection
    {
        return $this->reportes;
    }

    public function addReporte(Reporte $reporte): static
    {
        if (!$this->reportes->contains($reporte)) {
            $this->reportes->add($reporte);
            $reporte->setUsuario($this);
        }
        return $this;
    }

    public function removeReporte(Reporte $reporte): static
    {
        if ($this->reportes->removeElement($reporte)) {
            if ($reporte->getUsuario() === $this) {
                $reporte->setUsuario(null);
            }
        }
        return $this;
    }
}

 