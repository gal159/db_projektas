<?php

namespace App\Entity;

use App\Repository\NaudotojasRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: NaudotojasRepository::class)]
class Naudotojas implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $username = null;

    #[ORM\Column(length: 50)]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $role = null;

    /**
     * @var Collection<int, Paslauga>
     */
    #[ORM\OneToMany(targetEntity: Paslauga::class, mappedBy: 'vadybininkas')]
    private Collection $paslaugos;

    #[ORM\ManyToOne(inversedBy: 'naudotojas')]
    private ?Bendrija $bendrija = null;

    public function __construct()
    {
        $this->paslaugos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): static
    {
        $this->role = $role;

        return $this;
    }

    /**
     * @return Collection<int, Paslauga>
     */
    public function getPaslaugos(): Collection
    {
        return $this->paslaugos;
    }

    public function addPaslaugo(Paslauga $paslaugo): static
    {
        if (!$this->paslaugos->contains($paslaugo)) {
            $this->paslaugos->add($paslaugo);
            $paslaugo->setVadybininkas($this);
        }

        return $this;
    }

    public function removePaslaugo(Paslauga $paslaugo): static
    {
        if ($this->paslaugos->removeElement($paslaugo)) {
            // set the owning side to null (unless already changed)
            if ($paslaugo->getVadybininkas() === $this) {
                $paslaugo->setVadybininkas(null);
            }
        }

        return $this;
    }

    public function getBendrija(): ?Bendrija
    {
        return $this->bendrija;
    }

    public function setBendrija(?Bendrija $bendrija): static
    {
        $this->bendrija = $bendrija;

        return $this;
    }

    public function getRoles(): array
    {
        return [$this->role];
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUserIdentifier(): string
    {
        return 'username';
    }
}
