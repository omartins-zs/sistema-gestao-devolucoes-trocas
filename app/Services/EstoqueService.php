<?php

namespace App\Services;

use App\Models\EstoqueAtual;
use App\Models\Produto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EstoqueService
{
    /**
     * Incrementa a quantidade de um produto no estoque
     *
     * @param int $produtoId
     * @param int $quantidade
     * @return bool
     */
    public function incrementarEstoque(int $produtoId, int $quantidade): bool
    {
        try {
            DB::beginTransaction();

            $estoque = EstoqueAtual::firstOrCreate(
                ['produto_id' => $produtoId],
                ['quantidade' => 0]
            );

            $estoque->increment('quantidade', $quantidade);

            DB::commit();

            Log::info("Estoque incrementado", [
                'produto_id' => $produtoId,
                'quantidade' => $quantidade,
                'estoque_atual' => $estoque->quantidade,
            ]);

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erro ao incrementar estoque", [
                'produto_id' => $produtoId,
                'quantidade' => $quantidade,
                'erro' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Decrementa a quantidade de um produto no estoque
     *
     * @param int $produtoId
     * @param int $quantidade
     * @return bool
     * @throws \Exception
     */
    public function decrementarEstoque(int $produtoId, int $quantidade): bool
    {
        try {
            DB::beginTransaction();

            $estoque = EstoqueAtual::where('produto_id', $produtoId)->first();

            if (!$estoque) {
                throw new \Exception("Estoque não encontrado para o produto ID: {$produtoId}");
            }

            if ($estoque->quantidade < $quantidade) {
                throw new \Exception("Estoque insuficiente. Disponível: {$estoque->quantidade}, Solicitado: {$quantidade}");
            }

            $estoque->decrement('quantidade', $quantidade);

            DB::commit();

            Log::info("Estoque decrementado", [
                'produto_id' => $produtoId,
                'quantidade' => $quantidade,
                'estoque_atual' => $estoque->quantidade,
            ]);

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erro ao decrementar estoque", [
                'produto_id' => $produtoId,
                'quantidade' => $quantidade,
                'erro' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Obtém a quantidade atual em estoque de um produto
     *
     * @param int $produtoId
     * @return int
     */
    public function obterQuantidadeEstoque(int $produtoId): int
    {
        $estoque = EstoqueAtual::where('produto_id', $produtoId)->first();

        return $estoque ? $estoque->quantidade : 0;
    }
}

