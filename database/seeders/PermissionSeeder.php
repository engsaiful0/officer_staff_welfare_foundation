<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'employee-add',
            'employee-edit',
            'employee-view',
            'employee-delete',
            'fee-collect-add',
            'rule-add',
            'rule-edit',
            'rule-delete',
            'expense-add',
            'expense-view',
            'expense-edit',
            'expense-delete',
            'member-add',
            'member-edit',
            'member-view',
            'member-delete',
            'fee-summary-view',
            'my-collection-report-view',
            'settings-view',
            'deposit-add',
            'deposit-view',
            'deposit-edit',
            'deposit-delete',
            'deposit-import',
            'deposit-reports',
            'deposit-ledger-add',
            'deposit-ledger-view',
            'deposit-ledger-edit',
            'deposit-ledger-delete',
            'deposit-ledger-import',
            'deposit-ledger-reports',
            'investment-add',
            'investment-view',
            'investment-edit',
            'investment-delete',
            'investment-import',
            'investment-reports',
            'investment-ledger-add',
            'investment-ledger-view',
            'investment-ledger-edit',
            'investment-ledger-delete',
            'investment-ledger-import',
            'investment-ledger-reports'


        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission],
                ['user_id' => 1]
            );
        }
    }
}
