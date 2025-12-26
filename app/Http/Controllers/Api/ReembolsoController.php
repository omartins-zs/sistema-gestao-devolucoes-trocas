<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AutorizarReembolsoRequest;
use App\Http\Requests\ProcessarReembolsoRequest;
use App\Services\ReembolsoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReembolsoController extends Controller
{
    public function __construct(
        private ReembolsoService $reembolsoService
    ) {
    }

    /**
     * Lista reembolsos com filtros opcionais
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filtros = $request->only(['status', 'cliente_id', 'per_page']);
            $reembolsos = $this->reembolsoService->listarReembolsos($filtros);

            return response()->json([
                'status' => 'success',
                'message' => 'Reembolsos listados com sucesso',
                'data' => $reembolsos,
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao listar reembolsos', [
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao listar reembolsos',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Exibe um reembolso específico
     */
    public function show(string $id): JsonResponse
    {
        try {
            $reembolso = $this->reembolsoService->obterReembolso((int) $id);

            return response()->json([
                'status' => 'success',
                'message' => 'Reembolso encontrado',
                'data' => $reembolso,
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar reembolso', [
                'reembolso_id' => $id,
                'erro' => $e->getMessage(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Reembolso não encontrado',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Autoriza ou nega um reembolso
     */
    public function autorizar(AutorizarReembolsoRequest $request, string $id): JsonResponse
    {
        try {
            $usuarioId = auth()->id();
            $dados = $request->validated();

            $reembolso = $this->reembolsoService->autorizarReembolso(
                (int) $id,
                $dados['autorizado'],
                $usuarioId,
                $dados['observacoes'] ?? null
            );

            $mensagem = $dados['autorizado'] ? 'Reembolso autorizado com sucesso' : 'Reembolso negado';

            return response()->json([
                'status' => 'success',
                'message' => $mensagem,
                'data' => $reembolso,
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao autorizar reembolso', [
                'reembolso_id' => $id,
                'dados' => $request->validated(),
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao autorizar reembolso',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Processa/libera um reembolso
     */
    public function processar(ProcessarReembolsoRequest $request, string $id): JsonResponse
    {
        try {
            $usuarioId = auth()->id();
            $dados = $request->validated();

            $reembolso = $this->reembolsoService->processarReembolso(
                (int) $id,
                $dados['metodo'],
                $usuarioId,
                $dados['observacoes'] ?? null
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Reembolso processado com sucesso',
                'data' => $reembolso,
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao processar reembolso', [
                'reembolso_id' => $id,
                'dados' => $request->validated(),
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao processar reembolso',
                'error' => $e->getMessage(),
            ], 400);
        }
    }
}

