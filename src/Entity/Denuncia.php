<?php

namespace App\Entity;

use App\Repository\DenunciaRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

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

    #[ORM\ManyToOne(targetEntity: CategoriaDenuncia::class, inversedBy: 'denuncias')]
    #[ORM\JoinColumn(nullable: false)]
    private CategoriaDenuncia $categoria;

    #[ORM\OneToMany(mappedBy: 'denuncia', targetEntity: Evidencia::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $evidencias;

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

    public function getCategoria(): CategoriaDenuncia
    {
        return $this->categoria;
    }

    public function setCategoria(CategoriaDenuncia $categoria): self
    {
        $this->categoria = $categoria;
        return $this;
    }

    public function getEvidencias(): Collection
    {
        return $this->evidencias;
    }
}