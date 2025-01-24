<?php

namespace App\Entity;

use App\Repository\EvidenciaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EvidenciaRepository::class)]
class Evidencia
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 50)]
    private string $tipo;

    #[ORM\Column(type: 'string', length: 255)]
    private string $rutaArchivo;

    #[Vich\UploadableField(mapping: 'denuncia', fileNameProperty: 'images')]
    private ?File $imageFile = null;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $fechaSubida;

    #[ORM\ManyToOne(targetEntity: Denuncia::class, inversedBy: 'evidencias')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Denuncia $denuncia = null;

    // Getters y Setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTipo(): string
    {
        return $this->tipo;
    }

    public function setTipo(string $tipo): self
    {
        $this->tipo = $tipo;

        return $this;
    }

    public function getRutaArchivo(): string
    {
        return $this->rutaArchivo;
    }

    public function setRutaArchivo(string $rutaArchivo): self
    {
        $this->rutaArchivo = $rutaArchivo;

        return $this;
    }

    public function getFechaSubida(): \DateTimeInterface
    {
        return $this->fechaSubida;
    }

    public function setFechaSubida(\DateTimeInterface $fechaSubida): self
    {
        $this->fechaSubida = $fechaSubida;

        return $this;
    }

    public function getDenuncia(): ?Denuncia
    {
        return $this->denuncia;
    }

    public function setDenuncia(?Denuncia $denuncia): self
    {
        $this->denuncia = $denuncia;

        return $this;
    }
}
