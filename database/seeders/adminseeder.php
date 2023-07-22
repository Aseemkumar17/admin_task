<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AdminLogin;
class adminseeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AdminLogin::create([
            'name' => 'admin',
            'email' => 'admin@email.com',
            'password' => bcrypt('Admin@123'),
            'role' => '1',
        ]);
    }
}
