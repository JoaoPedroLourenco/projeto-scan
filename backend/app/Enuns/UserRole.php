<?php

namespace App\Enuns;

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
}

