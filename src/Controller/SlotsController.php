<?php

namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

class SlotsController extends AbstractController {
    private const SYMBOLS = ['🍒', '🍋', '🍊', '🍉'];
    private const REWARDS = [
        '🍒' => 10,
        '🍋' => 20,
        '🍊' => 30,
        '🍉' => 40
    ];
    #[Route('/')]
    public function main(): Response
    {
        return $this->render('slots/index.html.twig');
    }
    #[Route('/start', name: 'start_game', methods: ['POST'])]
    public function startGame(SessionInterface $session): JsonResponse
    {
        $session->set('credits', 10);
        return new JsonResponse(['credits' => 10]);
    }
}