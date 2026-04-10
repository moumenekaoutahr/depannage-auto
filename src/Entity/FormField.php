<?php

namespace App\Entity;

use App\Repository\FormFieldRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
#[ApiResource]
#[ORM\Entity(repositoryClass: FormFieldRepository::class)]
#[ORM\Table(name: 'form_fields')]
class FormField
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Form::class, inversedBy: 'fields')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Form $form = null;

    #[ORM\Column(length: 50)]
    private ?string $type = null; // text, number, date, select, checkbox, file, textarea

    #[ORM\Column(length: 255)]
    private ?string $label = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $placeholder = null;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $required = false;

    #[ORM\Column(type: 'integer')]
    private int $ordre = 0;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $options = null;

    public function getId(): ?int { return $this->id; }

    public function getForm(): ?Form { return $this->form; }
    public function setForm(?Form $form): static { $this->form = $form; return $this; }

    public function getType(): ?string { return $this->type; }
    public function setType(string $type): static { $this->type = $type; return $this; }

    public function getLabel(): ?string { return $this->label; }
    public function setLabel(string $label): static { $this->label = $label; return $this; }

    public function getPlaceholder(): ?string { return $this->placeholder; }
    public function setPlaceholder(?string $placeholder): static { $this->placeholder = $placeholder; return $this; }

    public function isRequired(): bool { return $this->required; }
    public function setRequired(bool $required): static { $this->required = $required; return $this; }

    public function getOrdre(): int { return $this->ordre; }
    public function setOrdre(int $ordre): static { $this->ordre = $ordre; return $this; }

    public function getOptions(): ?array { return $this->options; }
    public function setOptions(?array $options): static { $this->options = $options; return $this; }
}