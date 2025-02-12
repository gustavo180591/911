<?php

namespace App\Entity;

use App\Repository\DenunciaRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DenunciaRepository::class)]
class Denuncia
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank(message: 'La descripción no puede estar vacía.')]
    #[Assert\Length(min: 10, minMessage: 'La descripción debe tener al menos 10 caracteres.')]
    private string $descripcion;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $fechaHora;

    // Direccion en texto (campo opcional si la usas como resumen de ubicación)
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $direccion = null;

    // Relación con la entidad CategoriaDenuncia (como antes)
    #[ORM\ManyToOne(targetEntity: CategoriaDenuncia::class, inversedBy: 'denuncias')]
    #[ORM\JoinColumn(nullable: false)]
    private CategoriaDenuncia $categoria;

    // Colección de evidencias (OneToMany)
    #[ORM\OneToMany(mappedBy: 'denuncia', targetEntity: Evidencia::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $evidencias;

    // NUEVO: relación con Ubicacion
    #[ORM\ManyToOne(targetEntity: Ubicacion::class)]
    #[ORM\JoinColumn(nullable: true)] // o false, según tu lógica
    private ?Ubicacion $ubicacion = null;

    public function __construct()
    {
        $this->evidencias = new ArrayCollection();
        $this->fechaHora = new \DateTimeImmutable();
    }

    // Métodos get/set nativos
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescripcion(): string
    {
        return $this->descripcion;
    }

    public function setDescripcion(string $descripcion): self
    {
        $this->descripcion = $descripcion;
        return $this;
    }

    public function getFechaHora(): \DateTimeImmutable
    {
        return $this->fechaHora;
    }

    public function setFechaHora(\DateTimeImmutable $fechaHora): self
    {
        $this->fechaHora = $fechaHora;
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

    public function getCategoria(): CategoriaDenuncia
    {
        return $this->categoria;
    }

    public function setCategoria(CategoriaDenuncia $categoria): self
    {
        $this->categoria = $categoria;
        return $this;
    }

    public function getEvidencias(): Collection
    {
        return $this->evidencias;
    }

    // NUEVO: Ubicacion
    public function getUbicacion(): ?Ubicacion
    {
        return $this->ubicacion;
    }

    public function setUbicacion(?Ubicacion $ubicacion): self
    {
        $this->ubicacion = $ubicacion;
        return $this;
    }
}
