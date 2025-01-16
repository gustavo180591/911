<?php

namespace App\Entity;

use App\Repository\RolRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RolRepository::class)]
class Rol
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 50, unique: true)]
    private string $nombre;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $descripcion = null;

    #[ORM\OneToMany(mappedBy: 'rol', targetEntity: Usuario::class)]
    private Collection $usuarios;

    #[ORM\ManyToMany(targetEntity: Permiso::class, inversedBy: 'roles')]
    private Collection $permisos;

    public function __construct()
    {
        $this->usuarios = new ArrayCollection();
        $this->permisos = new ArrayCollection();
    }

    // Getters y Setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(?string $descripcion): self
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * @return Collection|Usuario[]
     */
    public function getUsuarios(): Collection
    {
        return $this->usuarios;
    }

    public function addUsuario(Usuario $usuario): self
    {
        if (!$this->usuarios->contains($usuario)) {
            $this->usuarios[] = $usuario;
            $usuario->setRol($this);
        }

        return $this;
    }

    public function removeUsuario(Usuario $usuario): self
    {
        if ($this->usuarios->removeElement($usuario)) {
            if ($usuario->getRol() === $this) {
                $usuario->setRol(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Permiso[]
     */
    public function getPermisos(): Collection
    {
        return $this->permisos;
    }

    public function addPermiso(Permiso $permiso): self
    {
        if (!$this->permisos->contains($permiso)) {
            $this->permisos[] = $permiso;
            $permiso->addRol($this);
        }

        return $this;
    }

    public function removePermiso(Permiso $permiso): self
    {
        if ($this->permisos->removeElement($permiso)) {
            $permiso->removeRol($this);
        }

        return $this;
    }
}
