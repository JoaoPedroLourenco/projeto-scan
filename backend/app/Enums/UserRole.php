<?php

namespace App\Enums;

use Illuminate\Support\Carbon;

enum UserRole: string
{
    case admin = 'admin';
    case manager = 'manager';
    case operator = 'operator';
    case customer = 'customer';

    public function label(): string {
        return match ($this) {
            self::admin => 'Administrador do Sistema',
            self::manager => 'Gerente',
            self::operator => 'Operador',
            self::customer => 'Cliente',
        };
    }

    public function expiresAt(): Carbon
    {
        return match ($this) {
            self::admin => now()->addHours(8),    
            self::operator => now()->addHours(12),   
            self::manager => now()->addHours(12),   
            self::customer => now()->addDays(14),       
        };
    }
}

