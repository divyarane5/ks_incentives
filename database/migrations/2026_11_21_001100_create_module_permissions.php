<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

/**
 * RealtySuite modules as permissions, given to roles so that nobody loses a screen they use
 * today: a role gets a module if it already has any permission of that module. On top of
 * that: sales (FOS, TL, Sr. TL, CH) get KeyLeads and KeyEarn (own incentive), Marketing gets
 * KeyLeads, Admin gets everything (Superadmin always has everything). CRM keeps KeyEarn
 * for their own incentive only: their "see all incentives" permissions are removed.
 * Admins change this later on KeyPeople > Module access.
 */
return new class extends Migration
{
    private const FROM_EXISTING = [
        'keybookings' => ['booking-', 'project-', 'developer-', 'developer_ladder-', 'project_ladder-', 'mandate_project-', 'mandate-booking-view', 'mandate-booking-create', 'mandate-booking-edit', 'mandate-booking-approve', 'channel-partner-', 'client-enquiry-'],
        'keyaccounts' => ['payment-', 'company-bank-', 'brokerage-ledgers-', 'mandate-booking-ledger-'],
        'keypeople' => ['user-', 'role-', 'department-', 'designation-', 'location-', 'business_unit-', 'candidate-'],
        'keyearn' => ['incentive-'],
    ];

    private const EXTRA = [
        'Admin' => ['keyleads', 'keybookings', 'keyaccounts', 'keypeople', 'keyearn'],
        'Superadmin' => ['keyleads', 'keybookings', 'keyaccounts', 'keypeople', 'keyearn'],
        'FOS' => ['keyleads', 'keyearn'],
        'TL' => ['keyleads', 'keyearn'],
        'Sr. TL' => ['keyleads', 'keyearn'],
        'CH' => ['keyleads', 'keyearn'],
        'Marketing' => ['keyleads'],
        'Marketing Head' => ['keyleads'],
    ];

    public function up(): void
    {
        $now = now();
        $ids = [];
        foreach (['keyleads', 'keybookings', 'keyaccounts', 'keypeople', 'keyearn'] as $m) {
            $name = 'module-'.$m;
            $id = DB::table('permissions')->where('name', $name)->where('guard_name', 'web')->value('id');
            $ids[$m] = $id ?: DB::table('permissions')->insertGetId(['name' => $name, 'guard_name' => 'web', 'created_at' => $now, 'updated_at' => $now]);
        }

        $roles = DB::table('roles')->where('guard_name', 'web')->pluck('name', 'id');
        foreach ($roles as $roleId => $roleName) {
            $existing = DB::table('role_has_permissions')->join('permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
                ->where('role_has_permissions.role_id', $roleId)->pluck('permissions.name')->all();

            $grant = self::EXTRA[$roleName] ?? [];
            foreach (self::FROM_EXISTING as $module => $prefixes) {
                foreach ($existing as $perm) {
                    foreach ($prefixes as $p) {
                        if (str_starts_with($perm, $p)) {
                            $grant[] = $module;
                            continue 3;
                        }
                    }
                }
            }

            foreach (array_unique($grant) as $module) {
                DB::table('role_has_permissions')->insertOrIgnore(['permission_id' => $ids[$module], 'role_id' => $roleId]);
            }
        }

        // CRM sees only their own incentive in KeyEarn, not everyone's (user's decision 2026-11-20).
        $crm = DB::table('roles')->where('name', 'CRM')->where('guard_name', 'web')->value('id');
        if ($crm) {
            $incentivePerms = DB::table('permissions')->where('name', 'like', 'incentive-%')->pluck('id');
            DB::table('role_has_permissions')->where('role_id', $crm)->whereIn('permission_id', $incentivePerms)->delete();
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        $ids = DB::table('permissions')->where('name', 'like', 'module-%')->pluck('id');
        DB::table('role_has_permissions')->whereIn('permission_id', $ids)->delete();
        DB::table('model_has_permissions')->whereIn('permission_id', $ids)->delete();
        DB::table('permissions')->whereIn('id', $ids)->delete();
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
