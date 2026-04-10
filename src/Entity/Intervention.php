<?php

namespace App\Entity;

use App\Repository\InterventionRepository;
use App\State\InterventionProcessor;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Metadata\ApiFilter;

#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(),
        new Post(processor: InterventionProcessor::class),
        new Put(processor: InterventionProcessor::class),
        new Delete(),
    ],
    paginationItemsPerPage: 10,
    paginationEnabled: true
)]
#[ApiFilter(DateFilter::class, properties: ['debutAt', 'finAt'])]
#[ApiFilter(OrderFilter::class, properties: ['id', 'debutAt', 'montantTotal'])]
#[ORM\Entity(repositoryClass: InterventionRepository::class)]
class Intervention
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $debutAt = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $finAt = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $rapport = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $corps = null;

    #[ORM\Column(type: 'float')]
    private ?float $montantTotal = 0;

    #[ORM\ManyToOne(targetEntity: Demande::class, inversedBy: 'interventions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Demande $demande = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $depanneur = null;

    #[ORM\ManyToMany(targetEntity: Vehicule::class)]
    #[ORM\JoinTable(name: 'intervention_vehicule')]
    private Collection $vehicules;

    #[ORM\OneToMany(mappedBy: 'intervention', targetEntity: Facture::class, cascade: ['persist', 'remove'])]
    private Collection $factures;

    #[ORM\OneToMany(mappedBy: 'intervention', targetEntity: Notification::class)]
    private Collection $notifications;

    #[ORM\OneToOne(mappedBy: 'intervention', targetEntity: Chat::class, cascade: ['persist', 'remove'])]
    private ?Chat $chat = null;

    public function __construct()
    {
        $this->vehicules     = new ArrayCollection();
        $this->factures      = new ArrayCollection();
        $this->notifications = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getDebutAt(): ?\DateTimeInterface { return $this->debutAt; }
    public function setDebutAt(\DateTimeInterface $debutAt): static { $this->debutAt = $debutAt; return $this; }

    public function getFinAt(): ?\DateTimeInterface { return $this->finAt; }
    public function setFinAt(?\DateTimeInterface $finAt): static { $this->finAt = $finAt; return $this; }

    public function getRapport(): ?string { return $this->rapport; }
    public function setRapport(?string $rapport): static { $this->rapport = $rapport; return $this; }

    public function getCorps(): ?string { return $this->corps; }
    public function setCorps(?string $corps): static { $this->corps = $corps; return $this; }

    public function getMontantTotal(): ?float { return $this->montantTotal; }
    public function setMontantTotal(float $montantTotal): static { $this->montantTotal = $montantTotal; return $this; }

    public function getDemande(): ?Demande { return $this->demande; }
    public function setDemande(?Demande $demande): static { $this->demande = $demande; return $this; }

    public function getDepanneur(): ?User { return $this->depanneur; }
    public function setDepanneur(?User $depanneur): static { $this->depanneur = $depanneur; return $this; }

    public function getVehicules(): Collection { return $this->vehicules; }

    public function addVehicule(Vehicule $vehicule): static
    {
        if (!$this->vehicules->contains($vehicule)) {
            $this->vehicules->add($vehicule);
        }
        return $this;
    }

    public function removeVehicule(Vehicule $vehicule): static
    {
        $this->vehicules->removeElement($vehicule);
        return $this;
    }

    public function getFactures(): Collection { return $this->factures; }

    public function addFacture(Facture $facture): static
    {
        if (!$this->factures->contains($facture)) {
            $this->factures->add($facture);
            $facture->setIntervention($this);
        }
        return $this;
    }

    public function removeFacture(Facture $facture): static
    {
        if ($this->factures->removeElement($facture)) {
            if ($facture->getIntervention() === $this) {
                $facture->setIntervention(null);
            }
        }
        return $this;
    }

    public function getNotifications(): Collection { return $this->notifications; }

    public function getChat(): ?Chat { return $this->chat; }

    public function getClient(): ?User
    {
        return $this->demande ? $this->demande->getClient() : null;
    }
}