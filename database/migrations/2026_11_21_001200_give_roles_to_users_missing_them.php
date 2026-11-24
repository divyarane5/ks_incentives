<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * "Add User" used to save the chosen role only in users.role_id and never gave the
 * permission role itself, so those employees could not open anything. Give each such
 * user the role named by role_id. Only adds rows; nobody who already has a role changes.
 */
return new class extends Migration
{
    public function up(): void
    {
        $model = \App\Models\User::class;
        $missing = DB::table('users')
            ->whereNotNull('role_id')
            ->whereNotExists(fn ($q) => $q->select(DB::raw(1))->from('model_has_roles')
                ->whereColumn('model_has_roles.model_id', 'users.id')
                ->where('model_has_roles.model_type', $model))
            ->whereExists(fn ($q) => $q->select(DB::raw(1))->from('roles')->whereColumn('roles.id', 'users.role_id'))
            ->get(['id', 'role_id']);

        foreach ($missing as $u) {
            DB::table('model_has_roles')->insert([
                'role_id' => $u->role_id,
                'model_type' => $model,
                'model_id' => $u->id,
            ]);
        }

        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        // Left in place: removing roles again would lock these people out.
    }
};
