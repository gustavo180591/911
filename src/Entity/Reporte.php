<?php

namespace App\Entity;

use App\Repository\ReporteRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReporteRepository::class)]
class Reporte
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'La descripción no puede estar vacía')]
    #[Assert\Length(
        min: 10,
        max: 1000,
        minMessage: 'La descripción debe tener al menos {{ limit }} caracteres',
        maxMessage: 'La descripción no puede tener más de {{ limit }} caracteres'
    )]
    private ?string $descripcion = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $fechaHora = null;

    #[ORM\ManyToOne(targetEntity: Denuncia::class, inversedBy: 'reportes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Denuncia $denuncia = null;

    #[ORM\ManyToOne(targetEntity: Usuario::class, inversedBy: 'reportes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Usuario $usuario = null;

    // Getters y Setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(string $descripcion): static
    {
        $this->descripcion = $descripcion;
        return $this;
    }

    public function getFechaHora(): ?\DateTimeInterface
    {
        return $this->fechaHora;
    }

    public function setFechaHora(\DateTimeInterface $fechaHora): static
    {
        $this->fechaHora = $fechaHora;
        return $this;
    }

    public function getDenuncia(): ?Denuncia
    {
        return $this->denuncia;
    }

    public function setDenuncia(?Denuncia $denuncia): static
    {
        $this->denuncia = $denuncia;
        return $this;
    }

    public function getUsuario(): ?Usuario
    {
        return $this->usuario;
    }

    public function setUsuario(?Usuario $usuario): static
    {
        $this->usuario = $usuario;
        return $this;
    }
}

