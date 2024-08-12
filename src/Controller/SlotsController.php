<?php

namespace App\Controller;

use App\Enum\SlotSymbolEnum;
use App\Enum\SlotRewardEnum;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class SlotsController extends AbstractController {

    #[Route('/')]
    public function main(): Response {
        return $this->render('slots/index.html.twig');
    }

    #[Route('/start', name: 'start_game', methods: ['POST'])]
    public function startGame(SessionInterface $session): JsonResponse {
        $session->set('credits', 10);
        return new JsonResponse(['credits' => 10]);
    }

    #[Route('/roll', name: 'roll_slots', methods: ['POST'])]
    public function rollSlots(SessionInterface $session): JsonResponse {
        $credits = $session->get('credits', 0);
        if ($credits <= 0) {
            return new JsonResponse(['error' => 'No credits left'], 400);
        }

        $result = $this->generateRoll($credits);
        $session->set('credits', $result['credits']);

        return new JsonResponse($result);
    }

    #[Route('/cashout', name: 'cash_out', methods: ['POST'])]
    public function cashOut(SessionInterface $session): JsonResponse {
        $credits = $session->get('credits', 0);
        $session->set('credits', 0);

        return new JsonResponse(['cashed_out' => $credits]);
    }

    private function generateRoll(int $credits): array {
        $symbols = [
            SlotSymbolEnum::cases()[array_rand(SlotSymbolEnum::cases())]->value,
            SlotSymbolEnum::cases()[array_rand(SlotSymbolEnum::cases())]->value,
            SlotSymbolEnum::cases()[array_rand(SlotSymbolEnum::cases())]->value
        ];

        $win = ($symbols[0] === $symbols[1]) && ($symbols[1] === $symbols[2]);

        if ($win) {
            // Slightly cheat ;)
            if ($credits >= 40 && $credits <= 60 && random_int(1, 100) <= 30) {
                return $this->generateRoll($credits);
            } else if ($credits > 60 && random_int(1, 100) <= 60) {
                return $this->generateRoll($credits);
            }
            $reward = (int) SlotRewardEnum::getReward(SlotSymbolEnum::from($symbols[0]));
            $credits += $reward;
        } else {
            $credits -= 1;
        }

        return [
            'symbols' => $symbols,
            'win' => $win,
            'reward' => $win ? $reward : 0,
            'credits' => $credits,
        ];
    }
}