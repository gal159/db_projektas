<?php

namespace App\Entity;

use App\Repository\PaslaugaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    #[ORM\Column(length: 255)]
    private ?string $matas = null;

    /**
     * @var Collection<int, Kaina>
     */
    #[ORM\OneToMany(targetEntity: Kaina::class, mappedBy: 'paslauga')]
    private Collection $kaina;

    public function __construct()
    {
        $this->kaina = new ArrayCollection();
    }

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

    public function getMatas(): ?string
    {
        return $this->matas;
    }

    public function setMatas(string $matas): static
    {
        $this->matas = $matas;

        return $this;
    }

    /**
     * @return Collection<int, Kaina>
     */
    public function getKaina(): Collection
    {
        return $this->kaina;
    }

    public function addKaina(Kaina $kaina): static
    {
        if (!$this->kaina->contains($kaina)) {
            $this->kaina->add($kaina);
            $kaina->setPaslauga($this);
        }

        return $this;
    }

    public function removeKaina(Kaina $kaina): static
    {
        if ($this->kaina->removeElement($kaina)) {
            // set the owning side to null (unless already changed)
            if ($kaina->getPaslauga() === $this) {
                $kaina->setPaslauga(null);
            }
        }

        return $this;
    }
}
