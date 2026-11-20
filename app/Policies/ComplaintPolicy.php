<?php

namespace App\Policies;

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
        return $user->hasRole(UserRole::AGENT);
    }

    public function delete(User $user, Complaint $complaint): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->hasRole(UserRole::AGENT);
    }

    public function manageStatus(User $user): bool
    {
        return $user->hasRole(UserRole::AGENT);
    }
}
