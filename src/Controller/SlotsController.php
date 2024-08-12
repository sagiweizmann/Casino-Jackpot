<?php

namespace App\Controller;

use App\Service\SlotMachineService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class SlotsController extends AbstractController
{
    private SlotMachineService $slotMachineService;

    public function __construct(SlotMachineService $slotMachineService)
    {
        $this->slotMachineService = $slotMachineService;
    }

    #[Route('/')]
    public function main() {
        return $this->render('slots/index.html.twig');
    }

    #[Route('/start', name: 'start_game', methods: ['POST'])]
    public function startGame(): JsonResponse
    {
        $credits = $this->slotMachineService->startGame();
        return new JsonResponse(['credits' => $credits]);
    }

    #[Route('/roll', name: 'roll_slots', methods: ['POST'])]
    public function rollSlots(): JsonResponse
    {
        $result = $this->slotMachineService->rollSlots();

        if (isset($result['error'])) {
            return new JsonResponse($result, 400);
        }

        return new JsonResponse($result);
    }

    #[Route('/cashout', name: 'cash_out', methods: ['POST'])]
    public function cashOut(): JsonResponse
    {
        $cashedOut = $this->slotMachineService->cashOut();
        return new JsonResponse(['cashed_out' => $cashedOut]);
    }
}
