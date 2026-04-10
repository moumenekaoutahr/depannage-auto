<?php

namespace App\Controller;

use App\Entity\Demande;
use App\Entity\StatutDemande;
use App\Repository\DemandeRepository;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/demandes')]
class DemandeController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private DemandeRepository $demandeRepository,
        private NotificationService $notificationService
    ) {}

    #[Route('', name: 'demande_list', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $demandes = $this->demandeRepository->findAll();
        $data = array_map(fn($d) => $this->serialize($d), $demandes);

        return $this->json($data);
    }

    #[Route('/{id}', name: 'demande_show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $demande = $this->demandeRepository->find($id);
        if (!$demande) {
            return $this->json(['message' => 'Demande non trouvee'], 404);
        }

        return $this->json($this->serialize($demande));
    }

    #[Route('', name: 'demande_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $demande = new Demande();
        $demande->setLatitudeClient($data['latitudeClient'] ?? 0);
        $demande->setLongitudeClient($data['longitudeClient'] ?? 0);
        $demande->setAdresseClient($data['adresseClient'] ?? '');
        $demande->setDescription($data['description'] ?? null);
        $demande->setStatut(StatutDemande::EN_ATTENTE);
        $demande->setUser($this->getUser());

        $this->em->persist($demande);
        $this->em->flush();

        return $this->json($this->serialize($demande), 201);
    }

    #[Route('/{id}', name: 'demande_update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $demande = $this->demandeRepository->find($id);
        if (!$demande) {
            return $this->json(['message' => 'Demande non trouvee'], 404);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['description'])) {
            $demande->setDescription($data['description']);
        }

        if (isset($data['adresseClient'])) {
            $demande->setAdresseClient($data['adresseClient']);
        }

        $this->em->flush();

        return $this->json($this->serialize($demande));
    }

    #[Route('/{id}/accepter', name: 'demande_accepter', methods: ['PUT'])]
    public function accepter(int $id): JsonResponse
    {
        $demande = $this->demandeRepository->find($id);
        if (!$demande) {
            return $this->json(['message' => 'Demande non trouvee'], 404);
        }

        $demande->setStatut(StatutDemande::ACCEPTEE);
        $this->em->flush();

        $this->notificationService->envoyer(
            $demande->getUser(),
            'Demande acceptee',
            'Votre demande de depannage a ete acceptee !'
        );

        return $this->json(['message' => 'Demande acceptee']);
    }

    #[Route('/{id}/annuler', name: 'demande_annuler', methods: ['PUT'])]
    public function annuler(int $id): JsonResponse
    {
        $demande = $this->demandeRepository->find($id);
        if (!$demande) {
            return $this->json(['message' => 'Demande non trouvee'], 404);
        }

        $demande->setStatut(StatutDemande::ANNULEE);
        $this->em->flush();

        return $this->json(['message' => 'Demande annulee']);
    }

    #[Route('/{id}/cloturer', name: 'demande_cloturer', methods: ['PUT'])]
    public function cloturer(int $id): JsonResponse
    {
        $demande = $this->demandeRepository->find($id);
        if (!$demande) {
            return $this->json(['message' => 'Demande non trouvee'], 404);
        }

        $demande->setStatut(StatutDemande::CLOTURE);
        $this->em->flush();

        return $this->json(['message' => 'Demande cloturee']);
    }

    #[Route('/{id}', name: 'demande_delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $demande = $this->demandeRepository->find($id);
        if (!$demande) {
            return $this->json(['message' => 'Demande non trouvee'], 404);
        }

        $this->em->remove($demande);
        $this->em->flush();

        return $this->json(['message' => 'Demande supprimee']);
    }

    private function serialize(Demande $d): array
    {
        return [
            'id' => $d->getId(),
            'latitudeClient' => $d->getLatitudeClient(),
            'longitudeClient' => $d->getLongitudeClient(),
            'adresseClient' => $d->getAdresseClient(),
            'description' => $d->getDescription(),
            'statut' => $d->getStatut(),
            'user' => $d->getUser()?->getEmail(),
        ];
    }
}