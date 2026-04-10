<?php

namespace App\Entity;

use App\Repository\FactureRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Metadata\ApiFilter;
#[ApiResource(
    paginationItemsPerPage: 10,
    paginationEnabled: true
)]
#[ApiFilter(OrderFilter::class, properties: ['id', 'montantTTC', 'emiseAt'])]
#[ORM\Entity(repositoryClass: FactureRepository::class)]
class Facture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'float')]
    private ?float $montantHT = null;

    #[ORM\Column(type: 'float')]
    private ?float $tva = null;

    #[ORM\Column(type: 'float')]
    private ?float $montantTTC = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $emiseAt = null;

    #[ORM\ManyToOne(targetEntity: Intervention::class, inversedBy: 'factures')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Intervention $intervention = null;

    #[ORM\OneToMany(mappedBy: 'facture', targetEntity: Paiement::class, cascade: ['persist', 'remove'])]
    private Collection $paiements;

    public function __construct()
    {
        $this->paiements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMontantHT(): ?float
    {
        return $this->montantHT;
    }

    public function setMontantHT(float $montantHT): static
    {
        $this->montantHT = $montantHT;
        return $this;
    }

    public function getTva(): ?float
    {
        return $this->tva;
    }

    public function setTva(float $tva): static
    {
        $this->tva = $tva;
        return $this;
    }

    public function getMontantTTC(): ?float
    {
        return $this->montantTTC;
    }

    public function setMontantTTC(float $montantTTC): static
    {
        $this->montantTTC = $montantTTC;
        return $this;
    }

    public function getEmiseAt(): ?\DateTimeInterface
    {
        return $this->emiseAt;
    }

    public function setEmiseAt(\DateTimeInterface $emiseAt): static
    {
        $this->emiseAt = $emiseAt;
        return $this;
    }

    public function getIntervention(): ?Intervention
    {
        return $this->intervention;
    }

    public function setIntervention(?Intervention $intervention): static
    {
        $this->intervention = $intervention;
        return $this;
    }

    public function getPaiements(): Collection
    {
        return $this->paiements;
    }

    public function addPaiement(Paiement $paiement): static
    {
        if (!$this->paiements->contains($paiement)) {
            $this->paiements->add($paiement);
            $paiement->setFacture($this);
        }
        return $this;
    }

    public function removePaiement(Paiement $paiement): static
    {
        if ($this->paiements->removeElement($paiement)) {
            if ($paiement->getFacture() === $this) {
                $paiement->setFacture(null);
            }
        }
        return $this;
    }
}