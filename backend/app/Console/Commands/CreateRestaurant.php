<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Restaurant\Application\CreateRestaurant\CreateRestaurant as CreateRestaurantUseCase;
use Illuminate\Console\Command;

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

    public function __construct(
        private CreateRestaurantUseCase $useCase,
    ) {
        parent::__construct();
    }

    public function handle(): void
    {
        $response = ($this->useCase)(
            $this->argument('name'),
            $this->argument('legal_name'),
            $this->argument('tax_id'),
            $this->argument('restaurant_email'),
            $this->argument('admin_name'),
            $this->argument('admin_email'),
            $this->argument('admin_password'),
        );

        $this->info("Restaurante '{$response->restaurantName}' creado correctamente.");
        $this->info("Admin: {$response->adminEmail}");
    }
}