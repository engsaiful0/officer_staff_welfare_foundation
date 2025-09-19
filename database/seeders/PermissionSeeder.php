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
            'student-add',
            'student-edit',
            'student-view',
            'student-delete',
            'employee-add',
            'employee-edit',
            'employee-view',
            'employee-delete',
            'fee-collect-add',
            'fee-collect-view',
            'fee-collect-edit',
            'fee-collect-delete',
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
            'settings-view'
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission,'user_id' => 1]);
        }
    }
}
