<?php

namespace App\Entity;

use App\Repository\HistorialDenunciaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HistorialDenunciaRepository::class)]
class HistorialDenuncia
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Denuncia::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Denuncia $denuncia;

    #[ORM\ManyToOne(targetEntity: EstadoDenuncia::class)]
    #[ORM\JoinColumn(nullable: false)]
    private EstadoDenuncia $estadoAnterior;

    #[ORM\ManyToOne(targetEntity: EstadoDenuncia::class)]
    #[ORM\JoinColumn(nullable: false)]
    private EstadoDenuncia $estadoNuevo;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $fechaCambio;

    // Getters y Setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDenuncia(): Denuncia
    {
        return $this->denuncia;
    }

    public function setDenuncia(Denuncia $denuncia): self
    {
        $this->denuncia = $denuncia;

        return $this;
    }

    public function getEstadoAnterior(): EstadoDenuncia
    {
        return $this->estadoAnterior;
    }

    public function setEstadoAnterior(EstadoDenuncia $estadoAnterior): self
    {
        $this->estadoAnterior = $estadoAnterior;

        return $this;
    }

    public function getEstadoNuevo(): EstadoDenuncia
    {
        return $this->estadoNuevo;
    }

    public function setEstadoNuevo(EstadoDenuncia $estadoNuevo): self
    {
        $this->estadoNuevo = $estadoNuevo;

        return $this;
    }

    public function getFechaCambio(): \DateTimeInterface
    {
        return $this->fechaCambio;
    }

    public function setFechaCambio(\DateTimeInterface $fechaCambio): self
    {
        $this->fechaCambio = $fechaCambio;

        return $this;
    }
}
