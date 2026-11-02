<?php

namespace App\Enums;

enum StockMovementType: string {
    case entry = 'entry';
    case exit = 'exit';
    case loss = 'loss';
    case adjustment = 'adjustment';

    public function label(): string {
        return match ($this) {
            self::entry => 'Entrada',
            self::exit => 'Saida',
            self::loss => 'Perda',
            self::adjustment => 'Ajuste'
        };
    }
}