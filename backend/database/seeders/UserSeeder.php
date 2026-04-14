<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\User\Infrastructure\Persistence\Models\EloquentUser;
use App\Restaurant\Infrastructure\Persistence\Models\EloquentRestaurant;

class UserSeeder extends Seeder
{

    public function run(): void
    {
        $users = [
            ['name' => 'Aarón', 'email' => 'aaronblvi@tpv.com', 'role' => 'admin', 'pin' => '1111'],
            ['name' => 'María', 'email' => 'mariahdz@tpv.com', 'role' => 'supervisor', 'pin' => '2222'],
            ['name' => 'Carla', 'email' => 'carlafdz@tpv.com', 'role' => 'supervisor', 'pin' => '3333'],
            ['name' => 'Raúl', 'email' => 'raulprz@tpv.com', 'role' => 'waiter', 'pin' => '4444'],
            ['name' => 'Álvaro', 'email' => 'alvarogtrz@tpv.com', 'role' => 'waiter', 'pin' => '5555'],
            ['name' => 'Clara', 'email' => 'claramtnz@tpv.com', 'role' => 'waiter', 'pin' => '6666'],
            ['name' => 'Germán', 'email' => 'germangdia@tpv.com', 'role' => 'waiter', 'pin' => '7777'],
            ['name' => 'Juan', 'email' => 'juansgra@tpv.com', 'role' => 'waiter', 'pin' => '8888'],
        ];

        $restaurant = EloquentRestaurant::first();

        foreach ($users as $user) {
            EloquentUser::create([
                'uuid' => \Illuminate\Support\Str::uuid(),
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role'],
                'pin' => $user['pin'],
                'restaurant_id' => $restaurant->id,
                'password' => \Illuminate\Support\Facades\Hash::make('password123'),
            ]);
        }
    }
}
