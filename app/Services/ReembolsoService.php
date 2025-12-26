<?php

namespace App\Services;

use App\Jobs\EnviarEmailNotificacaoReembolso;
use App\Models\Devolucao;
use App\Models\Reembolso;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReembolsoService
{
    /**
     * Cria um registro de reembolso para uma devolução
     *
     * @param int $devolucaoId
     * @return Reembolso
     * @throws \Exception
     */
    public function criarReembolso(int $devolucaoId): Reembolso
    {
        try {
            DB::beginTransaction();

            $devolucao = Devolucao::with(['pedidoItem', 'produto'])->findOrFail($devolucaoId);

            // Verifica se já existe reembolso
            if ($devolucao->reembolso) {
                throw new \Exception("Já existe um reembolso para esta devolução");
            }

            // Calcula o valor do reembolso (quantidade * preço unitário do item)
            $valor = $devolucao->pedidoItem->preco_unitario * $devolucao->quantidade;

            $reembolso = Reembolso::create([
                'devolucao_id' => $devolucaoId,
                'cliente_id' => $devolucao->cliente_id,
                'valor' => $valor,
                'status' => 'pendente',
                'autorizado' => false,
            ]);

            DB::commit();

            Log::info("Reembolso criado", [
                'reembolso_id' => $reembolso->id,
                'devolucao_id' => $devolucaoId,
                'valor' => $valor,
            ]);

            return $reembolso->load(['devolucao', 'cliente']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erro ao criar reembolso", [
                'devolucao_id' => $devolucaoId,
                'erro' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Autoriza ou nega um reembolso
     *
     * @param int $reembolsoId
     * @param bool $autorizado
     * @param int|null $usuarioId
     * @param string|null $observacoes
     * @return Reembolso
     * @throws \Exception
     */
    public function autorizarReembolso(int $reembolsoId, bool $autorizado, ?int $usuarioId = null, ?string $observacoes = null): Reembolso
    {
        try {
            DB::beginTransaction();

            $reembolso = Reembolso::findOrFail($reembolsoId);

            if ($reembolso->autorizado && $autorizado) {
                throw new \Exception("Reembolso já foi autorizado");
            }

            $reembolso->update([
                'autorizado' => $autorizado,
                'autorizado_por' => $autorizado ? $usuarioId : null,
                'data_autorizacao' => $autorizado ? now() : null,
                'observacoes' => $observacoes ?? $reembolso->observacoes,
            ]);

            DB::commit();

            // Dispara e-mail para o cliente
            EnviarEmailNotificacaoReembolso::dispatch($reembolsoId, $autorizado);

            Log::info("Reembolso " . ($autorizado ? 'autorizado' : 'negado'), [
                'reembolso_id' => $reembolsoId,
                'autorizado' => $autorizado,
                'usuario_id' => $usuarioId,
            ]);

            return $reembolso->load(['devolucao', 'cliente', 'autorizadoPor']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erro ao autorizar reembolso", [
                'reembolso_id' => $reembolsoId,
                'erro' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Processa/libera um reembolso (apenas se autorizado)
     *
     * @param int $reembolsoId
     * @param string $metodo
     * @param int|null $usuarioId
     * @param string|null $observacoes
     * @return Reembolso
     * @throws \Exception
     */
    public function processarReembolso(int $reembolsoId, string $metodo, ?int $usuarioId = null, ?string $observacoes = null): Reembolso
    {
        try {
            DB::beginTransaction();

            $reembolso = Reembolso::findOrFail($reembolsoId);

            if ($reembolso->status !== 'pendente') {
                throw new \Exception("Reembolso já foi processado ou cancelado");
            }

            if (!$reembolso->autorizado) {
                throw new \Exception("Reembolso precisa ser autorizado antes de ser processado");
            }

            $reembolso->update([
                'status' => 'processado',
                'metodo' => $metodo,
                'data_processamento' => now(),
                'processado_por' => $usuarioId,
                'observacoes' => $observacoes ?? $reembolso->observacoes,
            ]);

            DB::commit();

            Log::info("Reembolso processado", [
                'reembolso_id' => $reembolsoId,
                'metodo' => $metodo,
                'usuario_id' => $usuarioId,
            ]);

            return $reembolso->load(['devolucao', 'cliente', 'processadoPor', 'autorizadoPor']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erro ao processar reembolso", [
                'reembolso_id' => $reembolsoId,
                'erro' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Lista reembolsos com filtros
     *
     * @param array $filtros
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function listarReembolsos(array $filtros = [])
    {
        $query = Reembolso::with(['devolucao', 'cliente', 'processadoPor']);

        if (isset($filtros['status'])) {
            $query->where('status', $filtros['status']);
        }

        if (isset($filtros['cliente_id'])) {
            $query->where('cliente_id', $filtros['cliente_id']);
        }

        return $query->orderBy('created_at', 'desc')
            ->paginate($filtros['per_page'] ?? 15);
    }

    /**
     * Obtém um reembolso específico
     *
     * @param int $reembolsoId
     * @return Reembolso
     */
    public function obterReembolso(int $reembolsoId): Reembolso
    {
        return Reembolso::with(['devolucao.produto', 'cliente', 'processadoPor', 'autorizadoPor'])
            ->findOrFail($reembolsoId);
    }
}

