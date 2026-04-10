<?php

namespace App\Security\Voter;

use App\Entity\Intervention;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class InterventionVoter extends Voter
{
    const VIEW = 'VIEW';
    const EDIT = 'EDIT';
    const DELETE = 'DELETE';

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::VIEW, self::EDIT, self::DELETE])
            && $subject instanceof Intervention;
    }

    protected function voteOnAttribute(string $attribute, $intervention, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // ❌ pas connecté
        if (!$user instanceof User) {
            return false;
        }

        // 👑 ADMIN → accès total
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        return match ($attribute) {
            self::VIEW => $this->canView($intervention, $user),
            self::EDIT => $this->canEdit($intervention, $user),
            self::DELETE => $this->canDelete($intervention, $user),
            default => false,
        };
    }

    private function canView(Intervention $intervention, User $user): bool
    {
        // CLIENT → ses interventions
        if ($intervention->getClient() === $user) {
            return true;
        }

        // DEPANNEUR → assigné
        if ($intervention->getDepanneur() === $user) {
            return true;
        }

        return false;
    }

    private function canEdit(Intervention $intervention, User $user): bool
    {
        // CLIENT uniquement
        return $intervention->getClient() === $user;
    }

    private function canDelete(Intervention $intervention, User $user): bool
    {
        // CLIENT uniquement
        return $intervention->getClient() === $user;
    }
}