<?php

namespace App\Policies;

use App\Enums\ComplaintStatus;
use App\Enums\UserRole;
use App\Models\Complaint;
use App\Models\User;

class ComplaintPolicy
{
    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Complaint $complaint): bool
    {
        return $user->hasRole(UserRole::AGENT)
            || ((int) $complaint->id_user === (int) $user->id_user
                && $complaint->status === ComplaintStatus::PENDING);
    }

    public function delete(User $user, Complaint $complaint): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ((int) $complaint->id_user !== (int) $user->id_user) {
            return false;
        }

        return $user->role === UserRole::AGENT || $complaint->status === ComplaintStatus::PENDING;
    }

    public function manageStatus(User $user): bool
    {
        return $user->hasRole(UserRole::AGENT);
    }
}
