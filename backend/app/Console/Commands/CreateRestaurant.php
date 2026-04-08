<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateRestaurant extends Command
{
    protected $signature = 'restaurant:create
        {name : Nombre del restaurante}
        {legal_name : Razón social}
        {tax_id : NIF/CIF}
        {restaurant_email : Email del restaurante}
        {admin_email : Email del administrador}
        {admin_name : Nombre del administrador}
        {admin_password : Contraseña del administrador}';

    protected $description = 'Crea un restaurante y su usuario administrador';

    public function handle(): void
    {
        $restaurantId = DB::table('restaurants')->insertGetId([
            'uuid'       => Str::uuid(),
            'name'       => $this->argument('name'),
            'legal_name' => $this->argument('legal_name'),
            'tax_id'     => $this->argument('tax_id'),
            'email'      => $this->argument('restaurant_email'),
            'password'   => Hash::make(Str::random(16)),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('users')->insert([
            'uuid'          => Str::uuid(),
            'name'          => $this->argument('admin_name'),
            'email'         => $this->argument('admin_email'),
            'password'      => Hash::make($this->argument('admin_password')),
            'role'          => 'admin',
            'restaurant_id' => $restaurantId,
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        $this->info("Restaurante '{$this->argument('name')}' creado correctamente.");
        $this->info("Admin: {$this->argument('admin_email')}");
    }
}