<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;

#[ORM\Entity]
#[Vich\Uploadable]
class Evidencia
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $rutaArchivo = null;

    #[Vich\UploadableField(mapping: 'denuncia', fileNameProperty: 'rutaArchivo')]
    private ?File $imageFile = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $fechaSubida = null;

    #[ORM\ManyToOne(targetEntity: Denuncia::class, inversedBy: 'evidencias')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Denuncia $denuncia = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRutaArchivo(): ?string
    {
        return $this->rutaArchivo;
    }

    public function setRutaArchivo(?string $rutaArchivo): self
    {
        $this->rutaArchivo = $rutaArchivo;
        return $this;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        // Actualiza la fecha de subida solo si se sube un nuevo archivo
        if (null !== $imageFile) {
            $this->fechaSubida = new \DateTimeImmutable();
        }
    }

    public function getFechaSubida(): ?\DateTimeInterface
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
