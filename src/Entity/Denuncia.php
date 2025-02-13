<?php

namespace App\Entity;

use App\Repository\DenunciaRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\Usuario;

#[ORM\Entity(repositoryClass: DenunciaRepository::class)]
class Denuncia
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank(message: 'La descripción no puede estar vacía.')]
    #[Assert\Length(min: 10, minMessage: 'La descripción debe tener al menos 10 caracteres.')]
    private string $descripcion;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $fechaHora;

    #[ORM\ManyToOne(targetEntity: EstadoDenuncia::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?EstadoDenuncia $estado = null;

    #[ORM\ManyToOne(targetEntity: CategoriaDenuncia::class, inversedBy: 'denuncias')]
    #[ORM\JoinColumn(nullable: false)]
    private ?CategoriaDenuncia $categoria = null;

    #[ORM\ManyToOne(targetEntity: Usuario::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Usuario $usuario = null;

    #[ORM\OneToMany(mappedBy: 'denuncia', targetEntity: Evidencia::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $evidencias;

    #[ORM\ManyToOne(targetEntity: Ubicacion::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Ubicacion $ubicacion = null;

    public function __construct()
    {
        $this->evidencias = new ArrayCollection();
        $this->fechaHora = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getFechaHora(): \DateTimeImmutable
    {
        return $this->fechaHora;
    }

    public function setFechaHora(\DateTimeImmutable $fechaHora): self
    {
        $this->fechaHora = $fechaHora;
        return $this;
    }

    // ESTADO
    public function getEstado(): ?EstadoDenuncia
    {
        return $this->estado;
    }

    public function setEstado(?EstadoDenuncia $estado): self
    {
        $this->estado = $estado;
        return $this;
    }

    // CATEGORIA
    public function getCategoria(): ?CategoriaDenuncia
    {
        return $this->categoria;
    }

    public function setCategoria(?CategoriaDenuncia $categoria): self
    {
        $this->categoria = $categoria;
        return $this;
    }

    // USUARIO
    public function getUsuario(): ?User
    {
        return $this->usuario;
    }

    public function setUsuario(?User $usuario): self
    {
        $this->usuario = $usuario;
        return $this;
    }

    // EVIDENCIAS
    public function getEvidencias(): Collection
    {
        return $this->evidencias;
    }
    public function addEvidencia(Evidencia $evidencia): self
    {
        if (!$this->evidencias->contains($evidencia)) {
            $this->evidencias->add($evidencia);
            $evidencia->setDenuncia($this);
        }
        return $this;
    }
    public function removeEvidencia(Evidencia $evidencia): self
    {
        if ($this->evidencias->removeElement($evidencia)) {
            if ($evidencia->getDenuncia() === $this) {
                $evidencia->setDenuncia(null);
            }
        }
        return $this;
    }

    // UBICACION
    public function getUbicacion(): ?Ubicacion
    {
        return $this->ubicacion;
    }

    public function setUbicacion(?Ubicacion $ubicacion): self
    {
        $this->ubicacion = $ubicacion;
        return $this;
    }
}
