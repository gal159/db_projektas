<?php

namespace App\Entity;

use App\Repository\BendrijaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BendrijaRepository::class)]
class Bendrija
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $pavadinimas = null;

    /**
     * @var Collection<int, Naudotojas>
     */
    #[ORM\OneToMany(targetEntity: Naudotojas::class, mappedBy: 'bendrija')]
    private Collection $naudotojas;

    /**
     * @var Collection<int, Kaina>
     */
    #[ORM\OneToMany(targetEntity: Kaina::class, mappedBy: 'bedrija')]
    private Collection $kainos;

    #[ORM\ManyToOne(inversedBy: 'bendrijos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Naudotojas $vadybininkas = null;

    public function __construct()
    {
        $this->naudotojas = new ArrayCollection();
        $this->kainos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPavadinimas(): ?string
    {
        return $this->pavadinimas;
    }

    public function setPavadinimas(string $pavadinimas): static
    {
        $this->pavadinimas = $pavadinimas;

        return $this;
    }

    /**
     * @return Collection<int, Naudotojas>
     */
    public function getNaudotojas(): Collection
    {
        return $this->naudotojas;
    }

    public function addNaudotoja(Naudotojas $naudotoja): static
    {
        if (!$this->naudotojas->contains($naudotoja)) {
            $this->naudotojas->add($naudotoja);
            $naudotoja->setBendrija($this);
        }

        return $this;
    }

    public function removeNaudotoja(Naudotojas $naudotoja): static
    {
        if ($this->naudotojas->removeElement($naudotoja)) {
            // set the owning side to null (unless already changed)
            if ($naudotoja->getBendrija() === $this) {
                $naudotoja->setBendrija(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Kaina>
     */
    public function getKainos(): Collection
    {
        return $this->kainos;
    }

    public function addKaino(Kaina $kaino): static
    {
        if (!$this->kainos->contains($kaino)) {
            $this->kainos->add($kaino);
            $kaino->setBedrija($this);
        }

        return $this;
    }

    public function removeKaino(Kaina $kaino): static
    {
        if ($this->kainos->removeElement($kaino)) {
            // set the owning side to null (unless already changed)
            if ($kaino->getBedrija() === $this) {
                $kaino->setBedrija(null);
            }
        }

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
