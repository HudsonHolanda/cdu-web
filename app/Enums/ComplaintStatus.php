<?php

namespace App\Enums;

enum ComplaintStatus: int
{
    case PENDING = 0;
    case IN_PROGRESS = 1;
    case RESOLVED = 2;
    case REJECTED = 3;
    case UNDER_REVIEW = 4;
    case DUPLICATE = 5;

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pendente',
            self::IN_PROGRESS => 'Em andamento',
            self::RESOLVED => 'Concluído',
            self::REJECTED => 'Rejeitado',
            self::UNDER_REVIEW => 'Em análise',
            self::DUPLICATE => 'Duplicado',
        };
    }
}
