<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDevolucaoRequest;
use App\Http\Requests\UpdateDevolucaoStatusRequest;
use App\Services\DevolucaoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DevolucaoController extends Controller
{
    public function __construct(
        private DevolucaoService $devolucaoService
    ) {
    }

    /**
     * Lista todas as devoluções com filtros opcionais
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filtros = $request->only(['status', 'cliente_id', 'produto_id', 'per_page']);
            $devolucoes = $this->devolucaoService->listarDevolucoes($filtros);

            return response()->json([
                'status' => 'success',
                'message' => 'Devoluções listadas com sucesso',
                'data' => $devolucoes,
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao listar devoluções', [
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao listar devoluções',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Cria uma nova solicitação de devolução
     */
    public function store(StoreDevolucaoRequest $request): JsonResponse
    {
        try {
            $devolucao = $this->devolucaoService->criarDevolucao($request->validated());

            return response()->json([
                'status' => 'success',
                'message' => 'Devolução criada com sucesso',
                'data' => $devolucao,
            ], 201);
        } catch (\Exception $e) {
            Log::error('Erro ao criar devolução', [
                'dados' => $request->validated(),
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao criar devolução',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Exibe uma devolução específica
     */
    public function show(string $id): JsonResponse
    {
        try {
            $devolucao = $this->devolucaoService->obterDevolucao((int) $id);

            return response()->json([
                'status' => 'success',
                'message' => 'Devolução encontrada',
                'data' => $devolucao,
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar devolução', [
                'devolucao_id' => $id,
                'erro' => $e->getMessage(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Devolução não encontrada',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Atualiza o status de uma devolução
     */
    public function update(UpdateDevolucaoStatusRequest $request, string $id): JsonResponse
    {
        try {
            $usuarioId = auth()->id();
            $dados = $request->validated();

            $devolucao = $this->devolucaoService->atualizarStatus(
                (int) $id,
                $dados['status'],
                $usuarioId,
                $dados['observacoes'] ?? null
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Status da devolução atualizado com sucesso',
                'data' => $devolucao,
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar status da devolução', [
                'devolucao_id' => $id,
                'dados' => $request->validated(),
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao atualizar status da devolução',
                'error' => $e->getMessage(),
            ], 400);
        }
    }
}
