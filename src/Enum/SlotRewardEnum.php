<?php

namespace App\Enum;

use App\Enum\SlotSymbolEnum;

enum SlotRewardEnum: int {
    case CHERRY = 10;
    case LEMON = 20;
    case ORANGE = 30;
    case WATERMELON = 40;

    public static function getReward(SlotSymbolEnum $symbol): int {
        return match($symbol) {
            SlotSymbolEnum::CHERRY => self::CHERRY->value,
            SlotSymbolEnum::LEMON => self::LEMON->value,
            SlotSymbolEnum::ORANGE => self::ORANGE->value,
            SlotSymbolEnum::WATERMELON => self::WATERMELON->value,
        };
    }
}