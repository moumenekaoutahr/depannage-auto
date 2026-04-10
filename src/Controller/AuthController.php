<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Role;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AuthController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $passwordHasher,
        private JWTTokenManagerInterface $jwtManager   // ← Ajouter
    ) {}

    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        // Générer le token JWT manuellement
        $token = $this->jwtManager->create($user);

        return $this->json([
            'token'  => $token,   // ← Token réel
            'email'  => $user->getUserIdentifier(),
            'roles'  => $user->getRoles(),
            'nom'    => $user->getNom(),
            'prenom' => $user->getPrenom(),
        ]);
    }

    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    public function register(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['email']) || empty($data['motDePasse']) 
            || empty($data['nom']) || empty($data['prenom'])) {
            return $this->json(['message' => 'Champs obligatoires manquants'], 400);
        }

        $existingUser = $this->em->getRepository(User::class)
            ->findOneBy(['email' => $data['email']]);
        if ($existingUser) {
            return $this->json(['message' => 'Email déjà utilisé'], 409);
        }

        $roleName = $data['role'] ?? 'client';
        $role = $this->em->getRepository(Role::class)->findOneBy(['nom' => $roleName]);

        if (!$role) {
            return $this->json(['message' => 'Rôle non trouvé : ' . $roleName], 404);
        }
        
        $user = new User();
        $user->setNom($data['nom']);
        $user->setPrenom($data['prenom']);
        $user->setEmail($data['email']);
        $user->setTelephone($data['telephone'] ?? null);
        $user->setRole($role);

        $hashedPassword = $this->passwordHasher->hashPassword($user, $data['motDePasse']);
        $user->setMotDePasse($hashedPassword);

        $this->em->persist($user);
        $this->em->flush();

        // Générer token directement après inscription
        $token = $this->jwtManager->create($user);

        return $this->json([
            'message' => 'Inscription réussie',
            'token'   => $token,   // ← Connecté directement
            'email'   => $user->getEmail(),
            'role'    => $role->getNom(),
        ], 201);
    }

    #[Route('/api/me', name: 'api_me', methods: ['GET'])]
    public function me(): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->json([
            'id'        => $user->getId(),
            'email'     => $user->getUserIdentifier(),
            'nom'       => $user->getNom(),
            'prenom'    => $user->getPrenom(),
            'telephone' => $user->getTelephone(),
            'roles'     => $user->getRoles(),
            'role'      => $user->getRole()?->getNom(),
        ]);
    }
}