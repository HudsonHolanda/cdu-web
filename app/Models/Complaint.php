<?php

namespace App\Models;

use App\Enums\ComplaintStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

// `user_id`, `status` e `high_priority` ficam de fora do Fillable de propósito:
// o autor vem do usuário logado e status/prioridade só o agente/admin altera.
#[Fillable([
    'public_id', 'category', 'title', 'description', 'image_url',
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

    public function images(): HasMany
    {
        return $this->hasMany(ComplaintImage::class, 'complaint_id', 'complaint_id');
    }

    public function deletionRequests(): HasMany
    {
        return $this->hasMany(ComplaintDeletionRequest::class, 'complaint_id', 'complaint_id');
    }

    public function updateRequests(): HasMany
    {
        return $this->hasMany(ComplaintUpdateRequest::class, 'complaint_id', 'complaint_id');
    }
}
