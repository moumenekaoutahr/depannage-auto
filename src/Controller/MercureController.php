<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MercureController extends AbstractController
{
    #[Route('/send', name: 'send', methods: ['GET'])]
    public function send(Request $request): Response
    {
        return $this->redirect($request->getBasePath() . '/mercure/test');
    }
}
