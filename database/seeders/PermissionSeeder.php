<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Access_permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions=[
            'dashboard',
            'user',
            'products',
            'faq',
            'content',
            'notification',
            'contact',
            'staff',
            'db_backup',
        ];
        foreach ($permissions as $permission) {
           Access_permission ::create([
                'permission' => $permission,
            ]);
        }
    }
}
