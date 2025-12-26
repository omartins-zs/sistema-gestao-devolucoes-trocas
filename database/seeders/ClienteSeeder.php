<?php

namespace Database\Seeders;

use App\Models\Cliente;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClienteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clientes = [
            [
                'nome' => 'João Silva',
                'email' => 'joao.silva@email.com',
                'telefone' => '(11) 98765-4321',
            ],
            [
                'nome' => 'Maria Santos',
                'email' => 'maria.santos@email.com',
                'telefone' => '(11) 97654-3210',
            ],
            [
                'nome' => 'Pedro Oliveira',
                'email' => 'pedro.oliveira@email.com',
                'telefone' => '(11) 96543-2109',
            ],
            [
                'nome' => 'Ana Costa',
                'email' => 'ana.costa@email.com',
                'telefone' => '(11) 95432-1098',
            ],
            [
                'nome' => 'Carlos Ferreira',
                'email' => 'carlos.ferreira@email.com',
                'telefone' => '(11) 94321-0987',
            ],
            [
                'nome' => 'Juliana Almeida',
                'email' => 'juliana.almeida@email.com',
                'telefone' => '(11) 93210-9876',
            ],
            [
                'nome' => 'Roberto Souza',
                'email' => 'roberto.souza@email.com',
                'telefone' => '(11) 92109-8765',
            ],
            [
                'nome' => 'Fernanda Lima',
                'email' => 'fernanda.lima@email.com',
                'telefone' => '(11) 91098-7654',
            ],
            [
                'nome' => 'Lucas Martins',
                'email' => 'lucas.martins@email.com',
                'telefone' => '(11) 90987-6543',
            ],
            [
                'nome' => 'Patricia Rocha',
                'email' => 'patricia.rocha@email.com',
                'telefone' => '(11) 89876-5432',
            ],
            [
                'nome' => 'Ricardo Barbosa',
                'email' => 'ricardo.barbosa@email.com',
                'telefone' => '(11) 88765-4321',
            ],
            [
                'nome' => 'Camila Dias',
                'email' => 'camila.dias@email.com',
                'telefone' => '(11) 87654-3210',
            ],
            [
                'nome' => 'Bruno Carvalho',
                'email' => 'bruno.carvalho@email.com',
                'telefone' => '(11) 86543-2109',
            ],
            [
                'nome' => 'Larissa Mendes',
                'email' => 'larissa.mendes@email.com',
                'telefone' => '(11) 85432-1098',
            ],
            [
                'nome' => 'Thiago Araújo',
                'email' => 'thiago.araujo@email.com',
                'telefone' => '(11) 84321-0987',
            ],
        ];

        foreach ($clientes as $cliente) {
            Cliente::create($cliente);
        }

        $this->command->info('15 clientes criados com sucesso!');
    }
}
