<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => 'password',
            ],
            [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => 'password',
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'password' => 'password',
            ],
            [
                'name' => 'Bob Wilson',
                'email' => 'bob@example.com',
                'password' => 'password',
            ],
            [
                'name' => 'Alice Johnson',
                'email' => 'alice@example.com',
                'password' => 'password',
            ],
            [
                'name' => 'Charlie Brown',
                'email' => 'charlie@example.com',
                'password' => 'password',
            ],
            [
                'name' => 'Diana Martinez',
                'email' => 'diana@example.com',
                'password' => 'password',
            ],
            [
                'name' => 'Edward Davis',
                'email' => 'edward@example.com',
                'password' => 'password',
            ],
            [
                'name' => 'Fiona Garcia',
                'email' => 'fiona@example.com',
                'password' => 'password',
            ],
            [
                'name' => 'George Taylor',
                'email' => 'george@example.com',
                'password' => 'password',
            ],
        ];

        foreach ($users as $userData) {
            User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => Hash::make($userData['password']),
            ]);
        }

        $this->command->info('Created '.User::count().' users. Admin credentials: admin@example.com / password');
    }
}
