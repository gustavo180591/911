<?php

namespace App\Entity;

use App\Repository\DenunciaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DenunciaRepository::class)]
class Denuncia
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'text')]
    private string $descripcion;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $fechaHora;

    #[ORM\ManyToOne(targetEntity: EstadoDenuncia::class)]
    #[ORM\JoinColumn(nullable: false)]
    private EstadoDenuncia $estado;

    #[ORM\ManyToOne(targetEntity: CategoriaDenuncia::class)]
    #[ORM\JoinColumn(nullable: false)]
    private CategoriaDenuncia $categoria;

    #[ORM\ManyToOne(targetEntity: Ubicacion::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Ubicacion $ubicacion;

    #[ORM\ManyToOne(targetEntity: Usuario::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Usuario $usuario;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $fechaCreacion;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $fechaActualizacion = null;

    // Getters y Setters

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

    public function getFechaHora(): \DateTimeInterface
    {
        return $this->fechaHora;
    }

    public function setFechaHora(\DateTimeInterface $fechaHora): self
    {
        $this->fechaHora = $fechaHora;

        return $this;
    }

    public function getEstado(): EstadoDenuncia
    {
        return $this->estado;
    }

    public function setEstado(EstadoDenuncia $estado): self
    {
        $this->estado = $estado;

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

    public function getUbicacion(): Ubicacion
    {
        return $this->ubicacion;
    }

    public function setUbicacion(Ubicacion $ubicacion): self
    {
        $this->ubicacion = $ubicacion;

        return $this;
    }

    public function getUsuario(): Usuario
    {
        return $this->usuario;
    }

    public function setUsuario(Usuario $usuario): self
    {
        $this->usuario = $usuario;

        return $this;
    }

    public function getFechaCreacion(): \DateTimeInterface
    {
        return $this->fechaCreacion;
    }

    public function setFechaCreacion(\DateTimeInterface $fechaCreacion): self
    {
        $this->fechaCreacion = $fechaCreacion;

        return $this;
    }

    public function getFechaActualizacion(): ?\DateTimeInterface
    {
        return $this->fechaActualizacion;
    }

    public function setFechaActualizacion(?\DateTimeInterface $fechaActualizacion): self
    {
        $this->fechaActualizacion = $fechaActualizacion;

        return $this;
    }
}
