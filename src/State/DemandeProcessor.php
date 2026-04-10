<?php

namespace App\State;

use App\Entity\Demande;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;

#[AsTaggedItem('api_platform.state_processor')]

class DemandeProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private Security $security
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if (!$data instanceof Demande) {
            return $data;
        }

        // Assigner l'utilisateur connecté automatiquement
        $user = $this->security->getUser();
        if ($user && !$data->getUser()) {
            $data->setUser($user);
        }

        $this->em->persist($data);
        $this->em->flush();

        return $data;
    }
}