<?php

namespace App\Entity;

use App\Repository\DemandeRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use App\State\DemandeProcessor;

#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(),
        new Post(processor: DemandeProcessor::class),
        new Put(processor: DemandeProcessor::class),
        new Delete(),
    ],
    paginationItemsPerPage: 10,
    paginationEnabled: true
)]
#[ApiFilter(SearchFilter::class, properties: [
    'statut'     => 'exact',
    'user.email' => 'partial',
])]
#[ApiFilter(OrderFilter::class, properties: ['id', 'statut'])]
#[ORM\Entity(repositoryClass: DemandeRepository::class)]
class Demande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'float')]
    private ?float $latitudeClient = null;

    #[ORM\Column(type: 'float')]
    private ?float $longitudeClient = null;

    #[ORM\Column(length: 255)]
    private ?string $adresseClient = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 50)]
    private ?string $statut = StatutDemande::EN_ATTENTE->value;

    // ---- Relations ----

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'demandes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

   

    #[ORM\OneToMany(mappedBy: 'demande', targetEntity: Intervention::class, cascade: ['persist', 'remove'])]
    private Collection $interventions;
#[ORM\ManyToOne(targetEntity: User::class)]
private ?User $client = null;

public function getClient(): ?User
{
    return $this->client;
}
    public function __construct()
    {
        $this->interventions = new ArrayCollection();
        $this->statut        = StatutDemande::EN_ATTENTE->value;
    }

    // ---- Getters / Setters ----

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLatitudeClient(): ?float
    {
        return $this->latitudeClient;
    }

    public function setLatitudeClient(float $latitudeClient): static
    {
        $this->latitudeClient = $latitudeClient;
        return $this;
    }

    public function getLongitudeClient(): ?float
    {
        return $this->longitudeClient;
    }

    public function setLongitudeClient(float $longitudeClient): static
    {
        $this->longitudeClient = $longitudeClient;
        return $this;
    }

    public function getAdresseClient(): ?string
    {
        return $this->adresseClient;
    }

    public function setAdresseClient(string $adresseClient): static
    {
        $this->adresseClient = $adresseClient;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(StatutDemande $statut): static
    {
        $this->statut = $statut->value;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

   

    public function getInterventions(): Collection
    {
        return $this->interventions;
    }

    public function addIntervention(Intervention $intervention): static
    {
        if (!$this->interventions->contains($intervention)) {
            $this->interventions->add($intervention);
            $intervention->setDemande($this);
        }
        return $this;
    }

    public function removeIntervention(Intervention $intervention): static
    {
        if ($this->interventions->removeElement($intervention)) {
            if ($intervention->getDemande() === $this) {
                $intervention->setDemande(null);
            }
        }
        return $this;
    }
    
}
