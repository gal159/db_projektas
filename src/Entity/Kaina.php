<?php

namespace App\Entity;

use App\Repository\KainaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: KainaRepository::class)]
class Kaina
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $kaina = null;

    #[ORM\ManyToOne(inversedBy: 'kainos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Bendrija $bedrija = null;

    #[ORM\ManyToOne(inversedBy: 'kaina')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Paslauga $paslauga = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getBedrija(): ?Bendrija
    {
        return $this->bedrija;
    }

    public function setBedrija(?Bendrija $bedrija): static
    {
        $this->bedrija = $bedrija;

        return $this;
    }

    public function getPaslauga(): ?Paslauga
    {
        return $this->paslauga;
    }

    public function setPaslauga(?Paslauga $paslauga): static
    {
        $this->paslauga = $paslauga;

        return $this;
    }
}
