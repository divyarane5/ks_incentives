<?php

namespace App\Traits;

use App\Models\User;

trait UserHierarchyTrait
{
    public function getAccessibleUserIds($user)
    {
        // ✅ SuperAdmin → all users
        if ($user->hasRole('Superadmin')) {
            return User::pluck('id')->toArray();
        }

        // Start with self
        $ids = [$user->id];

        // BFS traversal to get all subordinates
        $queue = [$user->id];

        while (!empty($queue)) {
            $managerId = array_shift($queue);

            $children = User::where('reporting_manager_id', $managerId)
                ->pluck('id')
                ->toArray();

            foreach ($children as $childId) {
                if (!in_array($childId, $ids)) {
                    $ids[] = $childId;
                    $queue[] = $childId;
                }
            }
        }

        return $ids;
    }

    public function getUsersForDropdown(
        $user,
        array $businessUnitCodes = [],
        bool $applyHierarchy = true,
        array $roles = [] // optional: filter by role
    ) {
        $query = User::query();

        // 1️⃣ Business unit filter
        if (!empty($businessUnitCodes)) {
            $query->whereHas('businessUnit', function ($q) use ($businessUnitCodes) {
                $q->whereIn('code', $businessUnitCodes);
            });
        }

        // 2️⃣ Role filter
        if (!empty($roles)) {
            $query->whereHas('roles', function ($q) use ($roles) {
                $q->whereIn('name', $roles);
            });
        }

        // 3️⃣ Apply hierarchy
        if ($applyHierarchy && !$user->hasRole('Superadmin')) {
            $accessibleUserIds = $this->getAccessibleUserIds($user);
            $query->whereIn('id', $accessibleUserIds);
        }

        return $query->select('id', 'name')->orderBy('name')->get();
    }
}
