<?php

namespace App\Entity;

enum StatutDemande: string
{
    case EN_ATTENTE = 'en attente';
    case ACCEPTEE   = 'acceptee';
    case EN_COURS   = 'en cours';
    case CLOTURE    = 'cloture';
    case ANNULEE    = 'annulee';
}