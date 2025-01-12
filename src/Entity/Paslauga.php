<?php

namespace App\Entity;

use App\Repository\PaslaugaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaslaugaRepository::class)]
class Paslauga
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $vardas = null;

    #[ORM\Column]
    private ?int $kaina = null;

    #[ORM\ManyToOne(inversedBy: 'paslaugos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Naudotojas $vadybininkas = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVardas(): ?string
    {
        return $this->vardas;
    }

    public function setVardas(string $vardas): static
    {
        $this->vardas = $vardas;

        return $this;
    }

    public function getKaina(): ?int
    {
        return $this->kaina;
    }

    public function setKaina(int $kaina): static
    {
        $this->kaina = $kaina;

        return $this;
    }

    public function getVadybininkas(): ?Naudotojas
    {
        return $this->vadybininkas;
    }

    public function setVadybininkas(?Naudotojas $vadybininkas): static
    {
        $this->vadybininkas = $vadybininkas;

        return $this;
    }
}
