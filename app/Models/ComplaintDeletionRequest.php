<?php

namespace App\Models;

use App\Enums\ComplaintDeletionRequestStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['complaint_id', 'requested_by', 'reason', 'status', 'reviewed_by', 'reviewer_reason', 'reviewed_at'])]
class ComplaintDeletionRequest extends Model
{
    protected $primaryKey = 'complaint_deletion_request_id';

    protected function casts(): array
    {
        return [
            'status' => ComplaintDeletionRequestStatus::class,
            'reviewed_at' => 'datetime',
        ];
    }

    public function complaint(): BelongsTo
    {
        return $this->belongsTo(Complaint::class, 'complaint_id', 'complaint_id');
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by', 'id_user');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by', 'id_user');
    }
}
