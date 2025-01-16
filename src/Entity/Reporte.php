<?php

namespace App\Entity;

use App\Repository\ReporteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReporteRepository::class)]
class Reporte
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $titulo;

    #[ORM\Column(type: 'text')]
    private string $descripcion;

    #[ORM\ManyToOne(targetEntity: Usuario::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Usuario $autor;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $fechaGeneracion;

    // Getters y Setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitulo(): string
    {
        return $this->titulo;
    }

    public function setTitulo(string $titulo): self
    {
        $this->titulo = $titulo;

        return $this;
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

    public function getAutor(): Usuario
    {
        return $this->autor;
    }

    public function setAutor(Usuario $autor): self
    {
        $this->autor = $autor;

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
