<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\AutorizarReembolsoRequest;
use App\Http\Requests\ProcessarReembolsoRequest;
use App\Services\ReembolsoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ReembolsoController extends Controller
{
    public function __construct(
        private ReembolsoService $reembolsoService
    ) {
    }

    /**
     * Lista reembolsos pendentes
     */
    public function index(Request $request): View
    {
        try {
            $filtros = $request->only(['status', 'cliente_id']);
            // Remove status se for vazio (todos os status)
            if (empty($filtros['status'])) {
                unset($filtros['status']);
            }
            $filtros['per_page'] = 15;

            $reembolsos = $this->reembolsoService->listarReembolsos($filtros);

            return view('reembolsos.index', [
                'reembolsos' => $reembolsos,
                'filtros' => $filtros,
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao listar reembolsos na interface web', [
                'erro' => $e->getMessage(),
            ]);

            return view('reembolsos.index', [
                'reembolsos' => collect(),
                'filtros' => [],
                'erro' => 'Erro ao carregar reembolsos',
            ]);
        }
    }

    /**
     * Exibe detalhes de um reembolso
     */
    public function show(string $id): View
    {
        try {
            $reembolso = $this->reembolsoService->obterReembolso((int) $id);

            return view('reembolsos.show', [
                'reembolso' => $reembolso,
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar reembolso na interface web', [
                'reembolso_id' => $id,
                'erro' => $e->getMessage(),
            ]);

            return redirect()->route('reembolsos.index')
                ->with('error', 'Reembolso não encontrado');
        }
    }

    /**
     * Processa/libera um reembolso
     */
    public function processar(ProcessarReembolsoRequest $request, string $id): RedirectResponse
    {
        try {
            $usuarioId = auth()->id();
            $dados = $request->validated();

            $this->reembolsoService->processarReembolso(
                (int) $id,
                $dados['metodo'],
                $usuarioId,
                $dados['observacoes'] ?? null
            );

            return redirect()->route('reembolsos.show', $id)
                ->with('success', 'Reembolso processado com sucesso');
        } catch (\Exception $e) {
            Log::error('Erro ao processar reembolso na interface web', [
                'reembolso_id' => $id,
                'dados' => $request->validated(),
                'erro' => $e->getMessage(),
            ]);

            return redirect()->route('reembolsos.show', $id)
                ->with('error', 'Erro ao processar reembolso: ' . $e->getMessage());
        }
    }

    /**
     * Autoriza ou nega um reembolso
     */
    public function autorizar(AutorizarReembolsoRequest $request, string $id): RedirectResponse
    {
        try {
            $usuarioId = auth()->id();
            $dados = $request->validated();

            $this->reembolsoService->autorizarReembolso(
                (int) $id,
                $dados['autorizado'],
                $usuarioId,
                $dados['observacoes'] ?? null
            );

            $mensagem = $dados['autorizado'] ? 'Reembolso autorizado com sucesso' : 'Reembolso negado';

            return redirect()->route('reembolsos.show', $id)
                ->with('success', $mensagem);
        } catch (\Exception $e) {
            Log::error('Erro ao autorizar reembolso na interface web', [
                'reembolso_id' => $id,
                'dados' => $request->validated(),
                'erro' => $e->getMessage(),
            ]);

            return redirect()->route('reembolsos.show', $id)
                ->with('error', 'Erro ao autorizar reembolso: ' . $e->getMessage());
        }
    }
}
