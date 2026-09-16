<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'role' => 'admin',
                'password' => Hash::make('password'),
            ]
        );

        User::updateOrCreate(
            ['email' => 'petugas@example.com'],
            [
                'name' => 'Petugas',
                'role' => 'petugas',
                'password' => Hash::make('password'),
            ]
        );

        User::updateOrCreate(
            ['email' => 'petani1@example.com'],
            [
                'name' => 'Petani1',
                'role' => 'petani',
                'password' => Hash::make('password'),
            ]
        );

        User::updateOrCreate(
            ['email' => 'petani2@example.com'],
            [
                'name' => 'Petani2',
                'role' => 'petani',
                'password' => Hash::make('password'),
            ]
        );
    }
}
