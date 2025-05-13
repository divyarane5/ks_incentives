<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            'location-view',
            'location-create',
            'location-edit',
            'location-delete',
            'project-view',
            'project-create',
            'project-edit',
            'project-delete',
            'department-view',
            'department-create',
            'department-edit',
            'department-delete',
            'designation-view',
            'designation-create',
            'designation-edit',
            'designation-delete',
            'user-view',
            'user-create',
            'user-edit',
            'user-delete',
            'expense-view',
            'expense-create',
            'expense-edit',
            'expense-delete',
            'vendor-view',
            'vendor-create',
            'vendor-edit',
            'vendor-delete',
            'business_unit-view',
            'business_unit-create',
            'business_unit-edit',
            'business_unit-delete',
            'payment_method-view',
            'payment_method-create',
            'payment_method-edit',
            'payment_method-delete',
            'configuration-view',
            'configuration-create',
            'configuration-edit',
            'configuration-delete',
            'role-view',
            'role-create',
            'role-edit',
            'role-delete',
            'project-view',
            'developer-view',
            'indent-view-all',
            'indent-view-own',
            'indent-create',
            'indent-edit',
            'indent-delete',
            'indent-payment-conclude',
            'reimbursement-view-all',
            'reimbursement-view-own',
            'reimbursement-create',
            'reimbursement-edit',
            'reimbursement-delete',
            'reimbursement-settlement',
            'referral-template-view',
            'referral-template-create',
            'referral-template-edit',
            'referral-template-delete',
            'referral-client-view',
            'referral-client-create',
            'referral-client-edit',
            'referral-client-delete',
            'referral-client-send-email',
            'response-view',
            'response-edit',
            'booking-view',
            'booking-edit',
            'booking-create',
            'booking-delete',
            'project-offer-view',
            'project-offer-create',
            'project-offer-edit',
            'project-offer-send-image-email',
            'project-offer-send-pdf-email',
         ];

         foreach ($permissions as $permission) {
              Permission::create(['name' => $permission]);
         }
    }
}
