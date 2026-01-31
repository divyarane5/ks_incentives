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

    public function getAccessibleUsersByBusinessUnit($user, string $unitCode)
    {
        $query = User::whereHas('businessUnit', function ($q) use ($unitCode) {
            $q->where('code', $unitCode);
        });

        if (!$user->hasRole('Superadmin')) {
            $accessibleUserIds = $this->getAccessibleUserIds($user);
            $query->whereIn('id', $accessibleUserIds);
        }

        return $query->select('id', 'name')->orderBy('name')->get();
    }
}
