<?php

namespace App\Models;

use App\Enums\ComplaintStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// `user_id`, `status` e `high_priority` ficam de fora do Fillable de propósito:
// o autor vem do usuário logado e status/prioridade só o agente/admin altera.
#[Fillable([
    'title', 'description', 'image_url',
    'latitude', 'longitude', 'address', 'district', 'cep', 'complement',
])]
class Complaint extends Model
{
    use HasFactory;

    protected $primaryKey = 'complaint_id';

    protected function casts(): array
    {
        return [
            'status' => ComplaintStatus::class,
            'high_priority' => 'boolean',
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}