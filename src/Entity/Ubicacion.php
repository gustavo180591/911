<?php

namespace App\Entity;

use App\Repository\UbicacionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UbicacionRepository::class)]
class Ubicacion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: 'La calle no puede estar vacía.')]
    private string $calle;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private ?string $numero = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $detalles = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\Regex(
        pattern: '/^-?\d{1,3}\.\d+,-?\d{1,3}\.\d+$/',
        message: 'Las coordenadas deben estar en el formato "latitud,longitud".'
    )]
    private ?string $coordenadas = null;

    // Getters y Setters
    public function getId(): ?int { return $this->id; }

    public function getCalle(): string { return $this->calle; }

    public function setCalle(string $calle): self
    {
        $this->calle = $calle;
        return $this;
    }

    public function getNumero(): ?string { return $this->numero; }

    public function setNumero(?string $numero): self
    {
        $this->numero = $numero;
        return $this;
    }

    public function getDetalles(): ?string { return $this->detalles; }

    public function setDetalles(?string $detalles): self
    {
        $this->detalles = $detalles;
        return $this;
    }

    public function getCoordenadas(): ?string { return $this->coordenadas; }

    public function setCoordenadas(?string $coordenadas): self
    {
        $this->coordenadas = $coordenadas;
        return $this;
    }

    public function getLatitud(): ?float
    {
        if ($this->coordenadas) {
            [$latitud, ] = explode(',', $this->coordenadas);
            return (float) $latitud;
        }
        return null;
    }

    public function getLongitud(): ?float
    {
        if ($this->coordenadas) {
            [, $longitud] = explode(',', $this->coordenadas);
            return (float) $longitud;
        }
        return null;
    }
}
