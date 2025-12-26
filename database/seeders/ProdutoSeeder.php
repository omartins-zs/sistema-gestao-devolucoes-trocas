<?php

namespace Database\Seeders;

use App\Models\EstoqueAtual;
use App\Models\Produto;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProdutoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $produtos = [
            [
                'sku' => 'PROD-001',
                'nome' => 'Notebook Dell Inspiron 15',
                'preco' => 3299.90,
                'estoque' => 10,
            ],
            [
                'sku' => 'PROD-002',
                'nome' => 'Mouse Logitech MX Master 3',
                'preco' => 599.90,
                'estoque' => 25,
            ],
            [
                'sku' => 'PROD-003',
                'nome' => 'Teclado Mecânico RGB',
                'preco' => 449.90,
                'estoque' => 15,
            ],
            [
                'sku' => 'PROD-004',
                'nome' => 'Monitor LG UltraWide 29"',
                'preco' => 1299.90,
                'estoque' => 8,
            ],
            [
                'sku' => 'PROD-005',
                'nome' => 'Webcam Logitech C920',
                'preco' => 399.90,
                'estoque' => 20,
            ],
            [
                'sku' => 'PROD-006',
                'nome' => 'Headset HyperX Cloud II',
                'preco' => 549.90,
                'estoque' => 12,
            ],
            [
                'sku' => 'PROD-007',
                'nome' => 'SSD Samsung 1TB NVMe',
                'preco' => 699.90,
                'estoque' => 18,
            ],
            [
                'sku' => 'PROD-008',
                'nome' => 'Memória RAM 16GB DDR4',
                'preco' => 449.90,
                'estoque' => 22,
            ],
            [
                'sku' => 'PROD-009',
                'nome' => 'Placa de Vídeo RTX 3060',
                'preco' => 2499.90,
                'estoque' => 5,
            ],
            [
                'sku' => 'PROD-010',
                'nome' => 'Fonte 750W 80 Plus Gold',
                'preco' => 599.90,
                'estoque' => 15,
            ],
            [
                'sku' => 'PROD-011',
                'nome' => 'Bola de Basquete Spalding',
                'preco' => 199.90,
                'estoque' => 30,
            ],
            [
                'sku' => 'PROD-012',
                'nome' => 'Bola de Futsal Penalty',
                'preco' => 149.90,
                'estoque' => 35,
            ],
            [
                'sku' => 'PROD-013',
                'nome' => 'Tênis Nike Air Max Preto',
                'preco' => 799.90,
                'estoque' => 10,
            ],
            [
                'sku' => 'PROD-014',
                'nome' => 'Tênis Nike Air Max Azul',
                'preco' => 799.90,
                'estoque' => 12,
            ],
            [
                'sku' => 'PROD-015',
                'nome' => 'Smartphone Samsung Galaxy S23',
                'preco' => 3999.90,
                'estoque' => 8,
            ],
        ];

        foreach ($produtos as $produtoData) {
            $estoque = $produtoData['estoque'];
            unset($produtoData['estoque']);

            $produto = Produto::create($produtoData);

            EstoqueAtual::create([
                'produto_id' => $produto->id,
                'quantidade' => $estoque,
            ]);
        }

        $this->command->info('15 produtos criados com sucesso!');
    }
}
