<?php
namespace App\Repositories;

use App\Interfaces\RoleRepositoryInterface;
use Spatie\Permission\Models\Permission;
use DB;

class RoleRepository implements RoleRepositoryInterface
{
    public function getPermissions()
    {
        $permissions = Permission::select(['id', 'name', DB::raw("SUBSTRING_INDEX(name, '-', 1) as module, SUBSTRING(name, INSTR(name, '-')+1) as action")])
                            ->orderBy('id', 'asc')
                            ->get();
        return $permissions->groupBy('module');
    }
}
