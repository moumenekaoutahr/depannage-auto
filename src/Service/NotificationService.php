<?php

namespace App\Service;

use App\Entity\Notification;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

class NotificationService
{
    public function __construct(
        private EntityManagerInterface $em,
        private HubInterface $hub
    ) {}

    public function envoyer(User $user, string $titre, string $corps): Notification
    {
        // Creer la notification en base
        $notification = new Notification();
        $notification->setTitre($titre);
        $notification->setCorps($corps);
        $notification->setLue(false);
        $notification->setUser($user);

        $this->em->persist($notification);
        $this->em->flush();

        // Envoyer via Mercure
        $update = new Update(
            'notifications/' . $user->getId(),
            json_encode([
                'id' => $notification->getId(),
                'titre' => $titre,
                'corps' => $corps,
                'lue' => false,
            ], JSON_UNESCAPED_UNICODE)
        );

        $this->hub->publish($update);

        return $notification;
    }
}
