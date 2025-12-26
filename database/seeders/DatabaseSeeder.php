<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Criar usuário administrador
        User::factory()->create([
            'name' => 'Administrador',
            'email' => 'admin@example.com',
        ]);

        // Executar seeders na ordem correta
        $this->call([
            ClienteSeeder::class,
            ProdutoSeeder::class,
            PedidoSeeder::class,
            DevolucaoSeeder::class,
        ]);
    }
}
