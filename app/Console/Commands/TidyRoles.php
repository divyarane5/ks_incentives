<?php

namespace App\Console\Commands;

use App\Models\Designation;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Merges job-title roles into access roles (agreed 2026-11-24):
 *   Marketing <- Marketing Head, SEO (open KeyLeads)
 *   Content   <- Comm, Content Writer, Graphic Designer, Anchor, Vidographer (no KeyLeads)
 *   Backend   <- Office Asst., Front Desk
 * and removes the unused "User" role.
 * Nobody loses access: the new role gets every permission the old ones had, and each person's
 * old role name is kept as their designation when they have none. Shows the plan unless --apply.
 */
class TidyRoles extends Command
{
    protected $signature = 'roles:tidy {--apply : Make the changes (without it, only shows what would happen)}';

    protected $description = 'Merge Marketing, Content and Backend job-title roles into single access roles';

    private const MERGE = [
        'Marketing' => ['Marketing Head', 'SEO'],
        'Content' => ['Comm', 'Content Writer', 'Graphic Designer', 'Anchor', 'Vidographer', 'Videographer'],
        'Backend' => ['Office Asst.', 'Front Desk'],
    ];

    private const REMOVE_IF_EMPTY = ['User'];

    public function handle(): int
    {
        $apply = (bool) $this->option('apply');
        $this->info($apply ? 'Applying role clean-up…' : 'Plan only (run with --apply to make the changes):');

        DB::beginTransaction();
        try {
            foreach (self::MERGE as $targetName => $sourceNames) {
                $sources = Role::where('guard_name', 'web')->whereIn('name', $sourceNames)->get();
                if ($sources->isEmpty()) {
                    $this->line("- {$targetName}: nothing to merge");
                    continue;
                }
                $target = Role::where('guard_name', 'web')->where('name', $targetName)->first();
                $this->line("- {$targetName}".($target ? '' : ' (new role)').' gets: '.$sources->pluck('name')->implode(', '));

                if ($apply && ! $target) {
                    $target = Role::create(['name' => $targetName, 'guard_name' => 'web']);
                }

                // Every page any of the old roles could open.
                $perms = $sources->flatMap(fn ($r) => $r->permissions->pluck('name'))->unique()->values();
                $this->line('    pages/permissions carried over: '.$perms->count());
                if ($apply) {
                    $target->givePermissionTo($perms->all());
                }

                foreach ($sources as $src) {
                    $ids = DB::table('model_has_roles')->where('role_id', $src->id)->where('model_type', User::class)->pluck('model_id')
                        ->merge(DB::table('users')->where('role_id', $src->id)->pluck('id'))->unique()->values();
                    $people = User::withTrashed()->whereIn('id', $ids)->orderBy('name')->get();
                    $this->line("    {$src->name} ({$people->count()}): ".($people->pluck('name')->implode(', ') ?: '—'));

                    if (! $apply) {
                        continue;
                    }
                    $designation = Designation::firstOrCreate(['name' => $src->name]);
                    foreach ($people as $u) {
                        if (! $u->designation_id) {
                            $u->designation_id = $designation->id;
                        }
                        if ((int) $u->role_id === (int) $src->id) {
                            $u->role_id = $target->id;
                        }
                        $u->saveQuietly();
                        if (! $u->hasRole($target)) {
                            $u->assignRole($target);
                        }
                        $u->removeRole($src);
                    }
                    $this->deleteIfEmpty($src);
                }
            }

            foreach (self::REMOVE_IF_EMPTY as $name) {
                $role = Role::where('guard_name', 'web')->where('name', $name)->first();
                if ($role) {
                    $this->line("- Remove \"{$name}\" if nobody has it");
                    if ($apply) {
                        $this->deleteIfEmpty($role);
                    }
                }
            }

            $apply ? DB::commit() : DB::rollBack();
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error('Stopped, nothing changed: '.$e->getMessage());

            return self::FAILURE;
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $this->info($apply ? 'Done.' : 'Nothing was changed.');

        return self::SUCCESS;
    }

    private function deleteIfEmpty(Role $role): void
    {
        $held = DB::table('model_has_roles')->where('role_id', $role->id)->exists()
            || DB::table('users')->where('role_id', $role->id)->exists();
        if ($held) {
            $this->warn("    {$role->name} still held by someone, kept.");

            return;
        }
        $role->delete();
        $this->line("    {$role->name} removed.");
    }
}
