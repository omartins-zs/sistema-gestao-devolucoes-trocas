<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateDevolucaoStatusRequest;
use App\Services\DevolucaoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class DevolucaoController extends Controller
{
    public function __construct(
        private DevolucaoService $devolucaoService
    ) {
    }

    /**
     * Lista devoluções pendentes (interface administrativa)
     */
    public function index(Request $request): View
    {
        try {
            $filtros = $request->only(['status', 'cliente_id', 'produto_id']);
            $filtros['status'] = $filtros['status'] ?? 'pendente';
            $filtros['per_page'] = 15;

            $devolucoes = $this->devolucaoService->listarDevolucoes($filtros);

            return view('devolucoes.index', [
                'devolucoes' => $devolucoes,
                'filtros' => $filtros,
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao listar devoluções na interface web', [
                'erro' => $e->getMessage(),
            ]);

            return view('devolucoes.index', [
                'devolucoes' => collect(),
                'filtros' => [],
                'erro' => 'Erro ao carregar devoluções',
            ]);
        }
    }

    /**
     * Exibe detalhes de uma devolução
     */
    public function show(string $id): View
    {
        try {
            $devolucao = $this->devolucaoService->obterDevolucao((int) $id);

            return view('devolucoes.show', [
                'devolucao' => $devolucao,
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar devolução na interface web', [
                'devolucao_id' => $id,
                'erro' => $e->getMessage(),
            ]);

            return redirect()->route('devolucoes.index')
                ->with('error', 'Devolução não encontrada');
        }
    }

    /**
     * Atualiza o status de uma devolução
     */
    public function update(UpdateDevolucaoStatusRequest $request, string $id): RedirectResponse
    {
        try {
            $usuarioId = auth()->id();
            $dados = $request->validated();

            $this->devolucaoService->atualizarStatus(
                (int) $id,
                $dados['status'],
                $usuarioId,
                $dados['observacoes'] ?? null
            );

            return redirect()->route('devolucoes.show', $id)
                ->with('success', 'Status da devolução atualizado com sucesso');
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar status da devolução na interface web', [
                'devolucao_id' => $id,
                'dados' => $request->validated(),
                'erro' => $e->getMessage(),
            ]);

            return redirect()->route('devolucoes.show', $id)
                ->with('error', 'Erro ao atualizar status: ' . $e->getMessage());
        }
    }

    /**
     * Gera código de rastreamento para uma devolução
     */
    public function gerarCodigoRastreamento(string $id): RedirectResponse
    {
        try {
            $codigo = $this->devolucaoService->gerarCodigoRastreamento((int) $id);

            return redirect()->route('devolucoes.show', $id)
                ->with('success', "Código de rastreamento gerado: {$codigo}");
        } catch (\Exception $e) {
            Log::error('Erro ao gerar código de rastreamento na interface web', [
                'devolucao_id' => $id,
                'erro' => $e->getMessage(),
            ]);

            return redirect()->route('devolucoes.show', $id)
                ->with('error', 'Erro ao gerar código de rastreamento: ' . $e->getMessage());
        }
    }
}
