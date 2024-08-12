<?php

namespace App\Service;

use App\Enum\SlotSymbolEnum;
use App\Enum\SlotRewardEnum;
use Symfony\Component\HttpFoundation\Session\SessionFactoryInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SlotMachineService
{
    private SessionInterface $session;

    public function __construct(SessionFactoryInterface $sessionFactory)
    {
        $this->session = $sessionFactory->createSession();
    }

    public function startGame(): int
    {
        $credits = 10;
        $this->session->set('credits', $credits);
        return $credits;
    }

    public function rollSlots(): array
    {
        $credits = $this->session->get('credits', 0);

        if ($credits <= 0) {
            return ['error' => 'No credits left'];
        }

        $result = $this->generateRoll($credits);
        $this->session->set('credits', $result['credits']);

        return $result;
    }

    public function cashOut(): int
    {
        $credits = $this->session->get('credits', 0);
        $this->session->set('credits', 0);
        return $credits;
    }

    private function generateRoll(int $credits): array
    {
        $symbols = $this->getRandomSymbols();

        $win = $this->isWinningCombination($symbols);

        if ($win) {
            if ($this->shouldCheat($credits)) {
                return $this->generateRoll($credits);
            }

            $reward = SlotRewardEnum::getReward(SlotSymbolEnum::from($symbols[0]));
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

    private function getRandomSymbols(): array
    {
        return [
            SlotSymbolEnum::cases()[array_rand(SlotSymbolEnum::cases())]->value,
            SlotSymbolEnum::cases()[array_rand(SlotSymbolEnum::cases())]->value,
            SlotSymbolEnum::cases()[array_rand(SlotSymbolEnum::cases())]->value
        ];
    }

    private function isWinningCombination(array $symbols): bool
    {
        return ($symbols[0] === $symbols[1]) && ($symbols[1] === $symbols[2]);
    }

    private function shouldCheat(int $credits): bool
    {
        if ($credits >= 40 && $credits <= 60 && random_int(1, 100) <= 30) {
            return true;
        } elseif ($credits > 60 && random_int(1, 100) <= 60) {
            return true;
        }

        return false;
    }
}
