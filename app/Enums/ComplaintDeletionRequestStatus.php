<?php

namespace App\Enums;

enum ComplaintDeletionRequestStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pendente',
            self::APPROVED => 'Aprovada',
            self::REJECTED => 'Recusada',
        };
    }
}
