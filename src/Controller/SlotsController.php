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

    #[Route('/roll', name: 'roll_slots', methods: ['POST'])]
    public function rollSlots(SessionInterface $session): JsonResponse
    {
        $credits = $session->get('credits', 0);
        if ($credits <= 0) {
            return new JsonResponse(['error' => 'No credits left'], 400);
        }

        $result = $this->generateRoll($credits);
        $session->set('credits', $result['credits']);

        return new JsonResponse($result);
    }

    #[Route('/cashout', name: 'cash_out', methods: ['POST'])]
    public function cashOut(SessionInterface $session): JsonResponse
    {
        $credits = $session->get('credits', 0);
        $session->set('credits', 0);

        return new JsonResponse(['cashed_out' => $credits]);
    }

    private function generateRoll(int $credits): array
    {
        $symbols = [
            self::SYMBOLS[array_rand(self::SYMBOLS)],
            self::SYMBOLS[array_rand(self::SYMBOLS)],
            self::SYMBOLS[array_rand(self::SYMBOLS)]
        ];

        $win = ($symbols[0] === $symbols[1]) && ($symbols[1] === $symbols[2]);
        $cheat = false;

        if ($win) {
            $reward = self::REWARDS[$symbols[0]];
            $credits += $reward;


            // Cheat logic
            if ($credits >= 40 && $credits <= 60 && random_int(1, 100) <= 30) {
                $cheat = true;
                return $this->generateRoll($credits - $reward);
            } elseif ($credits > 60 && random_int(1, 100) <= 60) {
                $cheat = true;
                return $this->generateRoll($credits - $reward);
            }
        } else {
            $credits -= 1;
        }

        return [
            'symbols' => $symbols,
            'win' => $win,
            'reward' => $win ? $reward : 0,
            'credits' => $credits,
            'cheat' => $cheat
        ];
    }
}