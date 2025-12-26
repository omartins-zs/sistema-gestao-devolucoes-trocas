<?php

namespace App\Services;

use App\Jobs\EnviarEmailNotificacaoDevolucao;
use App\Models\Devolucao;
use App\Models\DevolucaoHistorico;
use App\Models\Pedido;
use App\Models\PedidoItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DevolucaoService
{
    public function __construct(
        private EstoqueService $estoqueService,
        private ReembolsoService $reembolsoService
    ) {
    }

    /**
     * Cria uma nova solicitação de devolução
     *
     * @param array $dados
     * @return Devolucao
     * @throws \Exception
     */
    public function criarDevolucao(array $dados): Devolucao
    {
        try {
            DB::beginTransaction();

            $pedidoItem = PedidoItem::with('pedido')->findOrFail($dados['pedido_item_id']);

            if ($dados['quantidade'] > $pedidoItem->quantidade) {
                throw new \Exception("Quantidade solicitada ({$dados['quantidade']}) excede a quantidade do pedido ({$pedidoItem->quantidade})");
            }

            // Validação específica para troca
            if (($dados['tipo'] ?? 'devolucao') === 'troca') {
                if (empty($dados['produto_troca_id'])) {
                    throw new \Exception("Produto de troca é obrigatório quando o tipo é troca");
                }
                if ($dados['produto_troca_id'] == $pedidoItem->produto_id) {
                    throw new \Exception("O produto de troca deve ser diferente do produto devolvido");
                }
            }

            $devolucao = Devolucao::create([
                'pedido_item_id' => $dados['pedido_item_id'],
                'cliente_id' => $pedidoItem->pedido->cliente_id,
                'produto_id' => $pedidoItem->produto_id,
                'produto_troca_id' => $dados['produto_troca_id'] ?? null,
                'quantidade' => $dados['quantidade'],
                'motivo' => $dados['motivo'],
                'motivo_troca' => $dados['motivo_troca'] ?? null,
                'status' => 'pendente',
                'tipo' => $dados['tipo'] ?? 'devolucao',
                'data_solicitacao' => now(),
            ]);

            $this->registrarHistorico($devolucao->id, null, 'pendente', null, 'Solicitação de devolução criada');

            DB::commit();

            Log::info("Devolução criada", [
                'devolucao_id' => $devolucao->id,
                'pedido_item_id' => $dados['pedido_item_id'],
                'cliente_id' => $devolucao->cliente_id,
            ]);

            return $devolucao->load(['cliente', 'produto', 'produtoTroca', 'pedidoItem.pedido']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erro ao criar devolução", [
                'dados' => $dados,
                'erro' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Atualiza o status de uma devolução
     *
     * @param int $devolucaoId
     * @param string $novoStatus
     * @param int|null $usuarioId
     * @param string|null $observacoes
     * @return Devolucao
     * @throws \Exception
     */
    public function atualizarStatus(int $devolucaoId, string $novoStatus, ?int $usuarioId = null, ?string $observacoes = null): Devolucao
    {
        try {
            DB::beginTransaction();

            $devolucao = Devolucao::findOrFail($devolucaoId);
            $statusAnterior = $devolucao->status;

            if ($statusAnterior === $novoStatus) {
                throw new \Exception("A devolução já está com status '{$novoStatus}'");
            }

            $this->validarTransicaoStatus($statusAnterior, $novoStatus);

            $devolucao->update([
                'status' => $novoStatus,
                'data_status' => now(),
                'observacoes' => $observacoes ?? $devolucao->observacoes,
            ]);

            $this->registrarHistorico($devolucaoId, $statusAnterior, $novoStatus, $usuarioId, $observacoes);

            if ($novoStatus === 'concluida' && $statusAnterior === 'aprovada') {
                $this->processarConclusaoDevolucao($devolucao);
            }

            DB::commit();

            // Dispara job de notificação por e-mail (assíncrono)
            // Justificativa: O envio de e-mail é uma operação que pode demorar e não deve bloquear
            // a resposta da API. Além disso, permite retry automático em caso de falha.
            EnviarEmailNotificacaoDevolucao::dispatch($devolucaoId, $statusAnterior, $novoStatus);

            Log::info("Status da devolução atualizado", [
                'devolucao_id' => $devolucaoId,
                'status_anterior' => $statusAnterior,
                'status_novo' => $novoStatus,
                'usuario_id' => $usuarioId,
            ]);

            return $devolucao->load(['cliente', 'produto', 'pedidoItem.pedido', 'historico.alteradoPor']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erro ao atualizar status da devolução", [
                'devolucao_id' => $devolucaoId,
                'novo_status' => $novoStatus,
                'erro' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Valida se a transição de status é permitida
     *
     * @param string $statusAnterior
     * @param string $novoStatus
     * @return void
     * @throws \Exception
     */
    private function validarTransicaoStatus(string $statusAnterior, string $novoStatus): void
    {
        $transicoesPermitidas = [
            'pendente' => ['aprovada', 'recusada'],
            'aprovada' => ['concluida'],
            'recusada' => [],
            'concluida' => [],
        ];

        if (!in_array($novoStatus, $transicoesPermitidas[$statusAnterior] ?? [])) {
            throw new \Exception("Transição de status inválida: de '{$statusAnterior}' para '{$novoStatus}'");
        }
    }

    /**
     * Processa a conclusão da devolução (ajusta estoque)
     *
     * @param Devolucao $devolucao
     * @return void
     */
    private function processarConclusaoDevolucao(Devolucao $devolucao): void
    {
        // Recarrega relacionamentos necessários
        $devolucao->load(['produtoTroca', 'pedidoItem']);

        // Sempre incrementa o estoque do produto devolvido
        $this->estoqueService->incrementarEstoque(
            $devolucao->produto_id,
            $devolucao->quantidade
        );

        Log::info("Produto devolvido - estoque incrementado", [
            'devolucao_id' => $devolucao->id,
            'produto_id' => $devolucao->produto_id,
            'quantidade' => $devolucao->quantidade,
        ]);

        // Se for troca, decrementa o estoque do produto de troca
        if ($devolucao->tipo === 'troca' && $devolucao->produto_troca_id) {
            // Verifica se há estoque suficiente do produto de troca
            $estoqueDisponivel = $this->estoqueService->obterQuantidadeEstoque($devolucao->produto_troca_id);
            
            if ($estoqueDisponivel < $devolucao->quantidade) {
                throw new \Exception(
                    "Estoque insuficiente do produto de troca. Disponível: {$estoqueDisponivel}, Solicitado: {$devolucao->quantidade}"
                );
            }

            $this->estoqueService->decrementarEstoque(
                $devolucao->produto_troca_id,
                $devolucao->quantidade
            );

            Log::info("Troca processada - estoque do produto de troca decrementado", [
                'devolucao_id' => $devolucao->id,
                'produto_troca_id' => $devolucao->produto_troca_id,
                'quantidade' => $devolucao->quantidade,
            ]);
        }

        // Se for troca, cria novo pedido automaticamente
        if ($devolucao->tipo === 'troca' && $devolucao->produto_troca_id) {
            $this->criarPedidoTroca($devolucao);
        }

        // Se for devolução (não troca), cria reembolso automaticamente
        if ($devolucao->tipo === 'devolucao') {
            $this->reembolsoService->criarReembolso($devolucao->id);
        }

        Log::info("Devolução/Troca concluída e estoque ajustado", [
            'devolucao_id' => $devolucao->id,
            'tipo' => $devolucao->tipo,
            'produto_id' => $devolucao->produto_id,
            'produto_troca_id' => $devolucao->produto_troca_id,
            'quantidade' => $devolucao->quantidade,
        ]);
    }

    /**
     * Gera um código de rastreamento único para a devolução
     *
     * @param int $devolucaoId
     * @return string
     * @throws \Exception
     */
    public function gerarCodigoRastreamento(int $devolucaoId): string
    {
        try {
            DB::beginTransaction();

            $devolucao = Devolucao::findOrFail($devolucaoId);

            if ($devolucao->codigo_rastreamento) {
                throw new \Exception("Esta devolução já possui um código de rastreamento");
            }

            // Gera código no formato: BR{ID}{RANDOM}BR
            // Exemplo: BR1234A5B6C7D8BR
            $random = strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
            $codigo = 'BR' . str_pad($devolucao->id, 4, '0', STR_PAD_LEFT) . $random . 'BR';

            $devolucao->update([
                'codigo_rastreamento' => $codigo,
                'data_envio' => now(),
            ]);

            DB::commit();

            Log::info("Código de rastreamento gerado", [
                'devolucao_id' => $devolucaoId,
                'codigo' => $codigo,
            ]);

            return $codigo;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erro ao gerar código de rastreamento", [
                'devolucao_id' => $devolucaoId,
                'erro' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Cria um novo pedido automaticamente para uma troca concluída
     *
     * @param Devolucao $devolucao
     * @return Pedido
     */
    private function criarPedidoTroca(Devolucao $devolucao): Pedido
    {
        $produtoTroca = $devolucao->produtoTroca;
        $precoUnitario = $produtoTroca->preco;
        $total = $precoUnitario * $devolucao->quantidade;

        // Cria o pedido de troca
        $pedido = Pedido::create([
            'cliente_id' => $devolucao->cliente_id,
            'devolucao_id' => $devolucao->id,
            'data_pedido' => now(),
            'total' => $total,
            'eh_pedido_troca' => true,
        ]);

        // Cria o item do pedido com o produto de troca
        PedidoItem::create([
            'pedido_id' => $pedido->id,
            'produto_id' => $devolucao->produto_troca_id,
            'quantidade' => $devolucao->quantidade,
            'preco_unitario' => $precoUnitario,
        ]);

        Log::info("Pedido de troca criado automaticamente", [
            'pedido_id' => $pedido->id,
            'devolucao_id' => $devolucao->id,
            'produto_troca_id' => $devolucao->produto_troca_id,
            'total' => $total,
        ]);

        return $pedido;
    }

    /**
     * Registra uma alteração no histórico da devolução
     *
     * @param int $devolucaoId
     * @param string|null $statusOld
     * @param string $statusNew
     * @param int|null $alteradoPor
     * @param string|null $observacoes
     * @return DevolucaoHistorico
     */
    private function registrarHistorico(
        int $devolucaoId,
        ?string $statusOld,
        string $statusNew,
        ?int $alteradoPor,
        ?string $observacoes = null
    ): DevolucaoHistorico {
        return DevolucaoHistorico::create([
            'devolucao_id' => $devolucaoId,
            'status_old' => $statusOld ?? $statusNew,
            'status_new' => $statusNew,
            'alterado_por' => $alteradoPor,
            'data_alteracao' => now(),
            'observacoes' => $observacoes,
        ]);
    }

    /**
     * Lista devoluções com filtros opcionais
     *
     * @param array $filtros
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function listarDevolucoes(array $filtros = [])
    {
        $query = Devolucao::with(['cliente', 'produto', 'produtoTroca', 'pedidoItem.pedido', 'historico.alteradoPor']);

        if (isset($filtros['status'])) {
            $query->where('status', $filtros['status']);
        }

        if (isset($filtros['cliente_id'])) {
            $query->where('cliente_id', $filtros['cliente_id']);
        }

        if (isset($filtros['produto_id'])) {
            $query->where('produto_id', $filtros['produto_id']);
        }

        return $query->orderBy('data_solicitacao', 'desc')
            ->paginate($filtros['per_page'] ?? 15);
    }

    /**
     * Obtém uma devolução com todos os relacionamentos
     *
     * @param int $devolucaoId
     * @return Devolucao
     */
    public function obterDevolucao(int $devolucaoId): Devolucao
    {
        return Devolucao::with([
            'cliente',
            'produto',
            'produtoTroca',
            'pedidoItem.pedido',
            'historico.alteradoPor',
            'lembretesEmail',
        ])->findOrFail($devolucaoId);
    }
}

