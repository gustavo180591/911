<?php

namespace App\Entity;

use App\Repository\NotificacionRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;

#[ORM\Entity(repositoryClass: NotificacionRepository::class)]
class Notificacion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'text')]
    private string $mensaje;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $fechaEnvio;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $usuario;

    #[ORM\ManyToOne(targetEntity: Denuncia::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Denuncia $denuncia = null;

    // Getters y Setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMensaje(): string
    {
        return $this->mensaje;
    }

    public function setMensaje(string $mensaje): self
    {
        $this->mensaje = $mensaje;

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

    public function getUsuario(): User
    {
        return $this->usuario;
    }

    public function setUsuario(User $usuario): self
    {
        $this->usuario = $usuario;

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
