<?php

namespace App\Entity;

use App\Repository\FormAnswerRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
#[ApiResource]
#[ORM\Entity(repositoryClass: FormAnswerRepository::class)]
#[ORM\Table(name: 'form_answers')]
class FormAnswer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: FormSubmission::class, inversedBy: 'answers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?FormSubmission $submission = null;

    #[ORM\ManyToOne(targetEntity: FormField::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?FormField $field = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $valeur = null;

    public function getId(): ?int { return $this->id; }

    public function getSubmission(): ?FormSubmission { return $this->submission; }
    public function setSubmission(?FormSubmission $submission): static { $this->submission = $submission; return $this; }

    public function getField(): ?FormField { return $this->field; }
    public function setField(?FormField $field): static { $this->field = $field; return $this; }

    public function getValeur(): ?string { return $this->valeur; }
    public function setValeur(?string $valeur): static { $this->valeur = $valeur; return $this; }
}