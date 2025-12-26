<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Models\Produto;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PedidoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clientes = Cliente::all();
        $produtos = Produto::all();

        if ($clientes->isEmpty() || $produtos->isEmpty()) {
            $this->command->warn('Clientes ou produtos não encontrados. Execute ClienteSeeder e ProdutoSeeder primeiro.');
            return;
        }

        // Criar 30 pedidos
        for ($i = 0; $i < 30; $i++) {
            $cliente = $clientes->random();
            $dataPedido = now()->subDays(rand(1, 90));

            $pedido = Pedido::create([
                'cliente_id' => $cliente->id,
                'data_pedido' => $dataPedido,
                'total' => 0,
            ]);

            $totalPedido = 0;
            $numItens = rand(1, 4);

            // Adicionar itens ao pedido
            $produtosUsados = [];
            for ($j = 0; $j < $numItens; $j++) {
                $produto = $produtos->whereNotIn('id', $produtosUsados)->random();
                $produtosUsados[] = $produto->id;
                
                $quantidade = rand(1, 3);
                $precoUnitario = $produto->preco;

                PedidoItem::create([
                    'pedido_id' => $pedido->id,
                    'produto_id' => $produto->id,
                    'quantidade' => $quantidade,
                    'preco_unitario' => $precoUnitario,
                ]);

                $totalPedido += $precoUnitario * $quantidade;
            }

            $pedido->update(['total' => $totalPedido]);
        }

        $this->command->info('30 pedidos criados com sucesso!');
    }
}
