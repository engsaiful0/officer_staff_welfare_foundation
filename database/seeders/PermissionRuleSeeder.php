<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\PermissionRule;

class PermissionRuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userId = 1;   // assign to user_id = 1
        $ruleId = 1;   // assign to rule_id = 1

        // fetch all permissions
        $permissions = Permission::all();

        foreach ($permissions as $permission) {
            PermissionRule::firstOrCreate([
                'permission_id' => $permission->id,
                'rule_id'       => $ruleId,
                'user_id'       => $userId,
            ]);
        }
    }
}
