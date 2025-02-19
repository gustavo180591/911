<?php

namespace App\Entity;

use App\Repository\ReporteRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Denuncia; // Asegúrate de que esta entidad exista y esté correctamente definida

#[ORM\Entity(repositoryClass: ReporteRepository::class)]
class Reporte
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    // Se ha hecho nullable el título para permitir su omisión en comentarios, si fuera necesario
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $titulo = null;

    #[ORM\Column(type: 'text')]
    private string $descripcion;

    #[ORM\ManyToOne(targetEntity: Usuario::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Usuario $autor;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $fechaGeneracion;

    // Nueva relación con la entidad Emergency (o Denuncia)
    #[ORM\ManyToOne(targetEntity: Denuncia::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Denuncia $denuncia;

    // Getters y Setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitulo(): ?string
    {
        return $this->titulo;
    }

    public function setTitulo(?string $titulo): self
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

    public function getDenuncia(): Denuncia
    {
        return $this->denuncia;
    }

    public function setDenuncia(Denuncia $denuncia): self
    {
        $this->denuncia = $denuncia;
        return $this;
    }
}
