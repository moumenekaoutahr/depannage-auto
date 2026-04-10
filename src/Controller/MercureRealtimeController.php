<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Annotation\Route;

class MercureRealtimeController extends AbstractController
{
    #[Route('/mercure/test', name: 'mercure_test', methods: ['GET'])]
    public function test(Request $request): Response
    {
        return $this->redirect($request->getBasePath() . '/mercure.html/test.html');
    }

    #[Route('/api/mercure/publish', name: 'mercure_publish', methods: ['POST'])]
    public function publish(Request $request, HubInterface $hub): JsonResponse
    {
        $data = json_decode($request->getContent(), true) ?? [];
        $topic = trim((string) ($data['topic'] ?? 'depannage_auto/messages'));
        $message = trim((string) ($data['message'] ?? ''));

        if ($topic === '' || $message === '') {
            return $this->json([
                'message' => 'Le topic et le message sont obligatoires.'
            ], 400);
        }

        $update = new Update(
            $topic,
            json_encode([
                'message' => $message,
                'topic' => $topic,
                'sentAt' => (new \DateTimeImmutable())->format(DATE_ATOM),
                'user' => $this->getUser()?->getUserIdentifier(),
            ], JSON_UNESCAPED_UNICODE)
        );

        $hub->publish($update);

        return $this->json([
            'message' => 'Message envoye avec succes.',
            'topic' => $topic,
            'payload' => $message,
        ]);
    }
}
