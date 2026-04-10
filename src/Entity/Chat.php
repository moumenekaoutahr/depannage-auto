<?php

namespace App\Entity;

use App\Repository\ChatRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Metadata\ApiResource;
#[ApiResource]

#[ORM\Entity(repositoryClass: ChatRepository::class)]
class Chat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $ouvertAt = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $fermetAt = null;

    #[ORM\OneToOne(targetEntity: Intervention::class, inversedBy: 'chat')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Intervention $intervention = null;

    #[ORM\OneToMany(mappedBy: 'chat', targetEntity: Message::class, cascade: ['persist', 'remove'])]
    private Collection $messages;

    public function __construct()
    {
        $this->messages  = new ArrayCollection();
        $this->ouvertAt  = new \DateTime();
    }

    public function getId(): ?int { return $this->id; }

    public function getOuvertAt(): ?\DateTimeInterface { return $this->ouvertAt; }
    public function setOuvertAt(\DateTimeInterface $ouvertAt): static { $this->ouvertAt = $ouvertAt; return $this; }

    public function getFermetAt(): ?\DateTimeInterface { return $this->fermetAt; }
    public function setFermetAt(?\DateTimeInterface $fermetAt): static { $this->fermetAt = $fermetAt; return $this; }

    public function getIntervention(): ?Intervention { return $this->intervention; }
    public function setIntervention(?Intervention $intervention): static { $this->intervention = $intervention; return $this; }

    public function getMessages(): Collection { return $this->messages; }

    public function addMessage(Message $message): static
    {
        if (!$this->messages->contains($message)) {
            $this->messages->add($message);
            $message->setChat($this);
        }
        return $this;
    }

    public function removeMessage(Message $message): static
    {
        if ($this->messages->removeElement($message)) {
            if ($message->getChat() === $this) {
                $message->setChat(null);
            }
        }
        return $this;
    }
}