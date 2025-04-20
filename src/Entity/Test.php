<?php

namespace App\Entity;

use App\Repository\TestRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TestRepository::class)]
class Test
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $NameTest = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNameTest(): ?string
    {
        return $this->NameTest;
    }

    public function setNameTest(string $NameTest): static
    {
        $this->NameTest = $NameTest;

        return $this;
    }
}
