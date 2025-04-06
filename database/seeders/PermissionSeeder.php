<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            [
                'name' => 'assign_permissions',
                
            ],
            [
                'name' => 'revoke_permissions',
              
            ],
            [
                'name' => 'enable_account',
            
            ],
            [
                'name' => 'dissable_account',
            
            ],
            [
                'name' => 'create_course',
              
            ],
            [
                'name' => 'edit_course_materials',
              
            ],
            [
                'name' => 'view_course_materials',
             
            ],
        ];

        // Insert the permissions into the database
        foreach ($permissions as $permission) {
            Permission::create($permission);
        }
    }
}