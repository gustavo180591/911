<?php

namespace App\Entity;

use App\Repository\MensajeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MensajeRepository::class)]
class Mensaje
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'text')]
    private string $contenido;

    #[ORM\ManyToOne(targetEntity: Usuario::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Usuario $usuarioRemitente;

    #[ORM\ManyToOne(targetEntity: Usuario::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Usuario $usuarioDestinatario;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $fechaEnvio;

    #[ORM\ManyToOne(targetEntity: Denuncia::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Denuncia $denuncia = null;

    // Getters y Setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContenido(): string
    {
        return $this->contenido;
    }

    public function setContenido(string $contenido): self
    {
        $this->contenido = $contenido;

        return $this;
    }

    public function getUsuarioRemitente(): Usuario
    {
        return $this->usuarioRemitente;
    }

    public function setUsuarioRemitente(Usuario $usuarioRemitente): self
    {
        $this->usuarioRemitente = $usuarioRemitente;

        return $this;
    }

    public function getUsuarioDestinatario(): Usuario
    {
        return $this->usuarioDestinatario;
    }

    public function setUsuarioDestinatario(Usuario $usuarioDestinatario): self
    {
        $this->usuarioDestinatario = $usuarioDestinatario;

        return $this;
    }

    public function getFechaEnvio(): \DateTimeInterface
    {
        return $this->fechaEnvio;
    }

    public function setFechaEnvio(\DateTimeInterface $fechaEnvio): self
    {
        $this->fechaEnvio = $fechaEnvio;

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

