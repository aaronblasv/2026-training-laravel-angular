<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\User\Infrastructure\Persistence\Models\EloquentUser;

class UserSeeder extends Seeder
{
    
    public function run(): void
    {
        $users = [
            ['name' => 'Aarón', 'email' => 'aaronblvi@tpv.com', 'role' => 'admin'],
            ['name' => 'María', 'email' => 'mariahdz@tpv.com', 'role' => 'supervisor'],
            ['name' => 'Carla', 'email' => 'carlafdz@tpv.com', 'role' => 'supervisor'],
            ['name' => 'Raúl', 'email' => 'raulprz@tpv.com', 'role' => 'waiter'],
            ['name' => 'Álvaro', 'email' => 'alvarogtrz@tpv.com', 'role' => 'waiter'],
            ['name' => 'Clara', 'email' => 'claramtnz@tpv.com', 'role' => 'waiter'],
            ['name' => 'Germán', 'email' => 'germangdia@tpv.com', 'role' => 'waiter'],
            ['name' => 'Juan', 'email' => 'juansgra@tpv.com', 'role' => 'waiter']
        ];

        foreach ($users as $user) {
            EloquentUser::create([
                'uuid' => \Illuminate\Support\Str::uuid(),
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role'],
                'password' => \Illuminate\Support\Facades\Hash::make('password123')
            ]);
        }


    }
}
