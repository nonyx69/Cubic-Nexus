<?php

namespace App\Entity;

use App\Repository\BanRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BanRepository::class)]
class Ban
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $UserCible = null;

    #[ORM\Column(length: 255)]
    private ?string $Raison = null;

    #[ORM\Column]
    private ?\DateTime $DateFin = null;

    #[ORM\Column]
    private ?bool $IsActive = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserCible(): ?string
    {
        return $this->UserCible;
    }

    public function setUserCible(string $UserCible): static
    {
        $this->UserCible = $UserCible;

        return $this;
    }

    public function getRaison(): ?string
    {
        return $this->Raison;
    }

    public function setRaison(string $Raison): static
    {
        $this->Raison = $Raison;

        return $this;
    }

    public function getDateFin(): ?\DateTime
    {
        return $this->DateFin;
    }

    public function setDateFin(\DateTime $DateFin): static
    {
        $this->DateFin = $DateFin;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->IsActive;
    }

    public function setIsActive(bool $IsActive): static
    {
        $this->IsActive = $IsActive;

        return $this;
    }
}
