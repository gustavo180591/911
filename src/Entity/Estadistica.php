<?php

namespace App\Entity;

use App\Repository\EstadisticaRepository;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EstadisticaRepository::class)]
class Estadistica
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $tipoDenuncia;

    #[ORM\Column(type: 'integer')]
    private int $cantidad;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $fechaGeneracion;

    // Getters y Setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTipoDenuncia(): string
    {
        return $this->tipoDenuncia;
    }

    public function setTipoDenuncia(string $tipoDenuncia): self
    {
        $this->tipoDenuncia = $tipoDenuncia;

        return $this;
    }

    public function getCantidad(): int
    {
        return $this->cantidad;
    }

    public function setCantidad(int $cantidad): self
    {
        $this->cantidad = $cantidad;

        return $this;
    }

    public function getFechaGeneracion(): \DateTimeInterface
    {
        return $this->fechaGeneracion;
    }

    public function setFechaGeneracion(\DateTimeInterface $fechaGeneracion): self
    {
        $this->fechaGeneracion = $fechaGeneracion;

        return $this;
    }
}
