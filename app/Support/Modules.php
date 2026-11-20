<?php

namespace App\Support;

/**
 * RealtySuite modules. Each module is one permission ("module-keyleads", ...), given to roles
 * (and, as extras, to single people) on the Module access page. A page also keeps its own
 * finer permission (booking-view, payment-edit, ...): you need both.
 */
class Modules
{
    public const ALL = [
        'keyleads' => ['name' => 'KeyLeads', 'about' => 'Leads and sales follow-ups', 'icon' => 'bx-phone-call'],
        'keybookings' => ['name' => 'KeyBookings', 'about' => 'Bookings, projects, developers, mandate', 'icon' => 'bx-book'],
        'keyaccounts' => ['name' => 'KeyAccounts', 'about' => 'Brokerage billing and collections', 'icon' => 'bx-money'],
        'keypeople' => ['name' => 'KeyPeople', 'about' => 'Employees, salaries, roles and masters', 'icon' => 'bx-user'],
        'keyearn' => ['name' => 'KeyEarn', 'about' => 'Incentives (own incentive for sales)', 'icon' => 'bx-rupee'],
    ];

    /** First part of the URL => modules that open it (any one is enough). */
    public const PATHS = [
        'booking' => ['keybookings', 'keyaccounts'],
        'project' => ['keybookings', 'keyaccounts'],
        'developer' => ['keybookings', 'keyaccounts'],
        'developer-billing' => ['keybookings', 'keyaccounts'],
        'mandate_projects' => ['keybookings'],
        'channel_partners' => ['keybookings'],
        'client-enquiries' => ['keybookings'],
        'mandate_bookings' => ['keybookings', 'keyaccounts'],
        'mandate-bookings' => ['keybookings', 'keyaccounts'],
        'brokerage-payments' => ['keyaccounts'],
        'brokerage-payments-aging' => ['keyaccounts'],
        'brokerage-payments-datatable' => ['keyaccounts'],
        'brokerage-payments-summary' => ['keyaccounts'],
        'brokerage-ledgers' => ['keyaccounts'],
        'company-bank' => ['keyaccounts'],
        'company-bank-accounts' => ['keyaccounts'],
        'users' => ['keypeople'],
        'import_users' => ['keypeople'],
        'org_tree' => ['keypeople'],
        'role' => ['keypeople'],
        'location' => ['keypeople'],
        'department' => ['keypeople'],
        'designation' => ['keypeople'],
        'business_unit' => ['keypeople'],
        'candidate' => ['keypeople'],
        'module-access' => ['keypeople'],
        'incentive-slabs' => ['keyearn'],
        'incentives' => ['keyearn'],
        'incentive-ledger' => ['keyearn'],
        'my-incentive' => ['keyearn'],
        'crm' => ['keyleads'],
    ];

    public static function permission(string $key): string
    {
        return 'module-'.$key;
    }

    /** Modules this user can open. */
    public static function for($user): array
    {
        if (! $user) {
            return [];
        }

        return array_values(array_filter(array_keys(self::ALL), fn ($k) => $user->can(self::permission($k))));
    }
}
