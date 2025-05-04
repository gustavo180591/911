<?php

namespace App\Entity;

use App\Repository\LogSistemaRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;

#[ORM\Entity(repositoryClass: LogSistemaRepository::class)]
class LogSistema
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $accion = null;

    #[ORM\Column(length: 255)]
    private ?string $entidad = null;

    #[ORM\Column]
    private ?int $entidadId = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $fechaHora = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $usuario = null;

    public function __construct()
    {
        $this->fechaHora = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAccion(): ?string
    {
        return $this->accion;
    }

    public function setAccion(string $accion): self
    {
        $this->accion = $accion;
        return $this;
    }

    public function getEntidad(): ?string
    {
        return $this->entidad;
    }

    public function setEntidad(string $entidad): self
    {
        $this->entidad = $entidad;
        return $this;
    }

    public function getEntidadId(): ?int
    {
        return $this->entidadId;
    }

    public function setEntidadId(int $entidadId): self
    {
        $this->entidadId = $entidadId;
        return $this;
    }

    public function getFechaHora(): ?\DateTimeImmutable
    {
        return $this->fechaHora;
    }

    public function setFechaHora(\DateTimeImmutable $fechaHora): self
    {
        $this->fechaHora = $fechaHora;
        return $this;
    }

    public function getUsuario(): ?User
    {
        return $this->usuario;
    }

    public function setUsuario(?User $usuario): self
    {
        $this->usuario = $usuario;
        return $this;
    }
}
