<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * Who uses KeyLeads and how many open leads each has. People, roles and managers are
 * edited in the back office (Users), so there is only one employee list.
 */
class TeamController extends Controller
{
    public const ROLES = [
        User::ROLE_SALES => 'Sales',
        User::ROLE_MANAGER => 'Manager',
        User::ROLE_GENERAL_MANAGER => 'General manager',
        User::ROLE_ADMIN => 'Admin',
    ];

    public function index(Request $request)
    {
        $users = User::active()->crmUsers()->with(['manager:id,name', 'roles:id,name'])
            ->withCount(['leads as active_leads_count' => fn ($q) => $q->where('is_archived', false)])
            ->when($request->query('q'), fn ($q, $v) => $q->where('name', 'like', "%{$v}%"))
            ->orderBy('name')
            ->paginate(50)->withQueryString();

        $noManager = User::active()->crmUsers([User::ROLE_SALES, User::ROLE_MANAGER, User::ROLE_GENERAL_MANAGER])
            ->whereNull('reporting_manager_id')->count();

        return view('crm.team.index', ['users' => $users, 'roles' => self::ROLES, 'noManager' => $noManager]);
    }
}
