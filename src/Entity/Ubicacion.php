<?php

namespace App\Entity;

use App\Repository\UbicacionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UbicacionRepository::class)]
class Ubicacion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $calle;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private ?string $numero = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $detalles = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $coordenadas = null; // Almacena latitud y longitud.

    // Getters y Setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCalle(): string
    {
        return $this->calle;
    }

    public function setCalle(string $calle): self
    {
        $this->calle = $calle;

        return $this;
    }

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(?string $numero): self
    {
        $this->numero = $numero;

        return $this;
    }

    public function getDetalles(): ?string
    {
        return $this->detalles;
    }

    public function setDetalles(?string $detalles): self
    {
        $this->detalles = $detalles;

        return $this;
    }

    public function getCoordenadas(): ?string
    {
        return $this->coordenadas;
    }

    public function setCoordenadas(?string $coordenadas): self
    {
        $this->coordenadas = $coordenadas;

        return $this;
    }
}
