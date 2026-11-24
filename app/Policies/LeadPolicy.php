<?php

namespace App\Policies;

use App\Models\Lead;
use App\Models\User;

class LeadPolicy
{
    public function view(User $user, Lead $lead): bool
    {
        if ($user->isAdmin()) {
            return true;
        }
        if ($lead->owner_id === null) {
            return $user->role !== User::ROLE_SALES;
        }
        $team = $user->teamUserIds();

        return in_array($lead->owner_id, $team, true) || in_array($lead->secondary_owner_id, $team, true);
    }

    public function update(User $user, Lead $lead): bool
    {
        return $this->view($user, $lead);
    }

    /** Only managers and above assign leads. */
    public function assign(User $user): bool
    {
        return $user->role !== User::ROLE_SALES;
    }

    public function delete(User $user, Lead $lead): bool
    {
        return $user->isAdmin();
    }
}
