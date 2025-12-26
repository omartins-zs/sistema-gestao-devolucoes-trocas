<?php

namespace Database\Seeders;

use App\Models\Devolucao;
use App\Models\PedidoItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DevolucaoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pedidoItems = PedidoItem::with('pedido.cliente', 'produto')->get();

        if ($pedidoItems->isEmpty()) {
            $this->command->warn('Itens de pedido não encontrados. Execute PedidoSeeder primeiro.');
            return;
        }

        $statuses = ['pendente', 'aprovada', 'recusada', 'concluida'];
        $motivos = [
            'Produto com defeito na tela',
            'Produto não corresponde à descrição',
            'Produto danificado na entrega',
            'Tamanho incorreto',
            'Produto diferente do pedido',
            'Arrependimento da compra',
            'Produto não funciona corretamente',
            'Produto chegou quebrado',
            'Produto com defeito de fabricação',
            'Produto não atende às expectativas',
            'Produto com avaria na embalagem',
            'Produto com peças faltando',
        ];

        $motivosTroca = [
            'Foi enviado o pedido errado. Era a cor preta e veio azul',
            'Pedi uma bola de basquete e veio uma de futsal',
            'Tamanho incorreto. Preciso de um tamanho maior',
            'Cor diferente da solicitada',
            'Modelo errado. Preciso de outro modelo',
            'Produto veio com defeito e quero trocar por outro igual',
            'Pedi tamanho 42 e veio 40',
            'Cor errada. Pedi preto e veio branco',
            'Produto diferente do anunciado',
            'Quero trocar por outro modelo mais adequado',
        ];

        $produtos = \App\Models\Produto::all();

        // Criar 50 devoluções com exemplos variados
        for ($i = 0; $i < 50; $i++) {
            $pedidoItem = $pedidoItems->random();
            $status = $statuses[array_rand($statuses)];
            $motivo = $motivos[array_rand($motivos)];
            $quantidade = rand(1, min(2, $pedidoItem->quantidade));
            
            // Distribuição: 20 devoluções, 15 trocas, 15 reembolsos
            if ($i < 20) {
                $tipo = 'devolucao';
            } elseif ($i < 35) {
                $tipo = 'troca';
            } else {
                $tipo = 'devolucao'; // Será reembolso quando concluída
            }
            
            $dataSolicitacao = now()->subDays(rand(1, 60));
            $dataStatus = $status !== 'pendente' ? $dataSolicitacao->copy()->addDays(rand(1, 7)) : null;

            $dados = [
                'pedido_item_id' => $pedidoItem->id,
                'cliente_id' => $pedidoItem->pedido->cliente_id,
                'produto_id' => $pedidoItem->produto_id,
                'quantidade' => $quantidade,
                'motivo' => $motivo,
                'status' => $status,
                'tipo' => $tipo,
                'data_solicitacao' => $dataSolicitacao,
                'data_status' => $dataStatus,
                'observacoes' => $status !== 'pendente' ? 'Processado pelo sistema de seeders' : null,
            ];

            // Se for troca, adiciona produto de troca e motivo da troca
            if ($tipo === 'troca' && $produtos->count() > 1) {
                $produtoTroca = $produtos->where('id', '!=', $pedidoItem->produto_id)->random();
                $dados['produto_troca_id'] = $produtoTroca->id;
                $dados['motivo_troca'] = $motivosTroca[array_rand($motivosTroca)];
            }

            // Se for aprovada ou concluída, 70% têm código de rastreamento
            if (in_array($status, ['aprovada', 'concluida']) && rand(1, 10) <= 7) {
                $random = strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
                $dados['codigo_rastreamento'] = 'BR' . str_pad($i + 1, 4, '0', STR_PAD_LEFT) . $random . 'BR';
                $dados['data_envio'] = $dataStatus ? $dataStatus->copy()->addDays(rand(1, 3)) : now()->subDays(rand(1, 10));
            }

            $devolucao = Devolucao::create($dados);

            // Se for devolução concluída, criar reembolso
            if ($tipo === 'devolucao' && $status === 'concluida') {
                $valor = $pedidoItem->preco_unitario * $quantidade;
                $reembolsoStatus = ['pendente', 'processado'][rand(0, 1)];
                $reembolsoAutorizado = rand(0, 1);
                
                $reembolso = \App\Models\Reembolso::create([
                    'devolucao_id' => $devolucao->id,
                    'cliente_id' => $devolucao->cliente_id,
                    'valor' => $valor,
                    'status' => $reembolsoStatus,
                    'autorizado' => $reembolsoAutorizado,
                    'data_autorizacao' => $reembolsoAutorizado ? $dataStatus->copy()->addDays(rand(1, 3)) : null,
                ]);

                if ($reembolso->status === 'processado') {
                    $metodos = ['credito_estorno', 'transferencia', 'boleto', 'pix'];
                    $reembolso->update([
                        'metodo' => $metodos[array_rand($metodos)],
                        'data_processamento' => $dataStatus->copy()->addDays(rand(4, 7)),
                        'processado_por' => 1, // Assumindo que existe usuário ID 1
                    ]);
                }
            }

            // Se for troca concluída, criar pedido de troca
            if ($tipo === 'troca' && $status === 'concluida' && $devolucao->produto_troca_id) {
                $produtoTroca = \App\Models\Produto::find($devolucao->produto_troca_id);
                $total = $produtoTroca->preco * $quantidade;
                
                $pedidoTroca = \App\Models\Pedido::create([
                    'cliente_id' => $devolucao->cliente_id,
                    'devolucao_id' => $devolucao->id,
                    'data_pedido' => $dataStatus ? $dataStatus->copy()->addDays(rand(1, 2)) : now(),
                    'total' => $total,
                    'eh_pedido_troca' => true,
                ]);

                \App\Models\PedidoItem::create([
                    'pedido_id' => $pedidoTroca->id,
                    'produto_id' => $devolucao->produto_troca_id,
                    'quantidade' => $quantidade,
                    'preco_unitario' => $produtoTroca->preco,
                ]);
            }
        }

        $this->command->info('50 devoluções criadas com sucesso!');
    }
}
