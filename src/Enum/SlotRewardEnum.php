<?php

namespace App\Enum;

use App\Enum\SlotSymbolEnum;

enum SlotRewardEnum: int {
    case CHERRY = 10;
    case LEMON = 20;
    case ORANGE = 30;
    case WATERMELON = 40;

    public static function getReward(SlotSymbolEnum $symbol): SlotRewardEnum {
        return match($symbol) {
            SlotSymbolEnum::CHERRY => self::CHERRY,
            SlotSymbolEnum::LEMON => self::LEMON,
            SlotSymbolEnum::ORANGE => self::ORANGE,
            SlotSymbolEnum::WATERMELON => self::WATERMELON,
        };
    }
}
