<?php

namespace App\Enums;

enum CartStatus: string {
    case open = 'open';
    case checking_out = 'checking_out';
    case completed = 'completed';
    case abandoned = 'abandoned';

    public function label(): string {
        return match ($this) {
            self::open => 'Aberto',
            self::checking_out => 'Finalizando',
            self::completed => 'Completo',
            self::abandoned => 'Abandonado'
        };
    }
}