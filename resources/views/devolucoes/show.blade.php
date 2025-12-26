@extends('layouts.app')

@section('title', 'Detalhes da Devolução')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div>
        <a href="{{ route('devolucoes.index') }}" class="btn btn-ghost btn-sm mb-4 gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Voltar para lista
        </a>
        <div class="flex items-center gap-4">
            <h1 class="text-4xl font-bold">Devolução #{{ $devolucao->id }}</h1>
            <div class="badge {{ $devolucao->tipo === 'troca' ? 'badge-primary' : 'badge-ghost' }} badge-lg">
                {{ $devolucao->tipo === 'troca' ? 'Troca' : 'Devolução' }}
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Informações Principais -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Informações da Devolução
                </h2>
                
                <div class="space-y-6">
                    <!-- Status -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Status</span>
                        </label>
                        <div class="mt-2">
                            @if($devolucao->status === 'pendente')
                                <div class="badge badge-warning badge-lg gap-2">
                                    <span class="loading loading-spinner loading-xs"></span>
                                    Pendente
                                </div>
                            @elseif($devolucao->status === 'aprovada')
                                <div class="badge badge-success badge-lg gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Aprovada
                                </div>
                            @elseif($devolucao->status === 'recusada')
                                <div class="badge badge-error badge-lg gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Recusada
                                </div>
                            @else
                                <div class="badge badge-info badge-lg gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Concluída
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Cliente -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Cliente</span>
                        </label>
                        <div class="mt-2 space-y-1">
                            <div class="font-semibold text-lg">{{ $devolucao->cliente->nome }}</div>
                            <div class="text-sm text-base-content/70 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                {{ $devolucao->cliente->email }}
                            </div>
                            @if($devolucao->cliente->telefone)
                                <div class="text-sm text-base-content/70 flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                    {{ $devolucao->cliente->telefone }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Pedido -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Pedido Original</span>
                        </label>
                        <div class="mt-2 space-y-1">
                            <div class="font-semibold">Pedido #{{ $devolucao->pedidoItem->pedido->id }}</div>
                            <div class="text-sm text-base-content/70">Data: {{ $devolucao->pedidoItem->pedido->data_pedido->format('d/m/Y') }}</div>
                            <div class="text-sm text-base-content/70">Total: <span class="font-semibold text-primary">R$ {{ number_format($devolucao->pedidoItem->pedido->total, 2, ',', '.') }}</span></div>
                        </div>
                    </div>

                    <!-- Produto -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Produto {{ $devolucao->tipo === 'troca' ? 'Devolvido' : '' }}</span>
                        </label>
                        <div class="mt-2 space-y-1">
                            <div class="font-semibold text-lg">{{ $devolucao->produto->nome }}</div>
                            <div class="text-sm text-base-content/70">SKU: {{ $devolucao->produto->sku }}</div>
                            <div class="text-sm text-base-content/70">Preço: <span class="font-semibold">R$ {{ number_format($devolucao->produto->preco, 2, ',', '.') }}</span></div>
                            <div class="text-sm text-base-content/70">Quantidade: <span class="badge badge-outline">{{ $devolucao->quantidade }}</span></div>
                        </div>
                    </div>

                    @if($devolucao->tipo === 'troca' && $devolucao->produtoTroca)
                        <div class="divider">Produto de Troca</div>
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">Produto de Troca</span>
                            </label>
                            <div class="mt-2 space-y-1">
                                <div class="font-semibold text-lg text-primary">{{ $devolucao->produtoTroca->nome }}</div>
                                <div class="text-sm text-base-content/70">SKU: {{ $devolucao->produtoTroca->sku }}</div>
                                <div class="text-sm text-base-content/70">Preço: <span class="font-semibold">R$ {{ number_format($devolucao->produtoTroca->preco, 2, ',', '.') }}</span></div>
                            </div>
                        </div>
                    @endif

                    <!-- Motivo -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Motivo</span>
                        </label>
                        <div class="mt-2 p-4 bg-base-200 rounded-lg">
                            <p class="text-base-content/80">{{ $devolucao->motivo }}</p>
                        </div>
                    </div>

                    <!-- Datas -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">Data de Solicitação</span>
                            </label>
                            <div class="mt-2 text-sm">{{ $devolucao->data_solicitacao->format('d/m/Y H:i') }}</div>
                        </div>
                        @if($devolucao->data_status)
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-semibold">Última Atualização</span>
                                </label>
                                <div class="mt-2 text-sm">{{ $devolucao->data_status->format('d/m/Y H:i') }}</div>
                            </div>
                        @endif
                    </div>

                    @if($devolucao->tipo === 'troca' && $devolucao->motivo_troca)
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">Motivo da Troca</span>
                            </label>
                            <div class="mt-2 p-4 bg-base-200 rounded-lg">
                                <p class="text-base-content/80">{{ $devolucao->motivo_troca }}</p>
                            </div>
                        </div>
                    @endif

                    @if($devolucao->observacoes)
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">Observações</span>
                            </label>
                            <div class="mt-2 p-4 bg-base-200 rounded-lg">
                                <p class="text-base-content/80">{{ $devolucao->observacoes }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Código de Rastreamento -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Código de Rastreamento</span>
                        </label>
                        <div class="mt-2">
                            @if($devolucao->codigo_rastreamento)
                                <div class="alert alert-info">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <div>
                                        <div class="font-bold text-lg">{{ $devolucao->codigo_rastreamento }}</div>
                                        @if($devolucao->data_envio)
                                            <div class="text-sm">Enviado em: {{ $devolucao->data_envio->format('d/m/Y H:i') }}</div>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <form method="POST" action="{{ route('devolucoes.gerar-codigo', $devolucao->id) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="btn btn-primary gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                        Gerar Código de Rastreamento
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    @if($devolucao->tipo === 'troca' && $devolucao->pedidoTroca)
                        <div class="divider">Pedido de Troca</div>
                        <div class="alert alert-info">
                            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div>
                                <div class="font-semibold">Pedido de Troca Gerado</div>
                                <div class="text-sm">Pedido #{{ $devolucao->pedidoTroca->id }} - Total: R$ {{ number_format($devolucao->pedidoTroca->total, 2, ',', '.') }}</div>
                            </div>
                        </div>
                    @endif

                    @if($devolucao->tipo === 'devolucao' && $devolucao->reembolso)
                        <div class="divider">Reembolso</div>
                        <div class="alert alert-success">
                            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div>
                                <div class="font-semibold">Reembolso Criado</div>
                                <a href="{{ route('reembolsos.show', $devolucao->reembolso->id) }}" class="link link-hover font-bold text-lg">
                                    Ver Reembolso #{{ $devolucao->reembolso->id }} - R$ {{ number_format($devolucao->reembolso->valor, 2, ',', '.') }}
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Ações e Histórico -->
        <div class="space-y-6">
            @if($devolucao->status === 'pendente')
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h2 class="card-title mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            Atualizar Status
                        </h2>
                        
                        <form method="POST" action="{{ route('devolucoes.update', $devolucao->id) }}" class="space-y-4">
                            @csrf
                            @method('PUT')
                            
                            <div class="form-control w-full">
                                <label class="label">
                                    <span class="label-text font-semibold">Novo Status</span>
                                </label>
                                <select name="status" class="select select-bordered w-full focus:select-primary" required>
                                    <option value="">Selecione...</option>
                                    <option value="aprovada">Aprovada</option>
                                    <option value="recusada">Recusada</option>
                                </select>
                            </div>

                            <div class="form-control w-full">
                                <label class="label">
                                    <span class="label-text font-semibold">Observações</span>
                                </label>
                                <textarea 
                                    name="observacoes" 
                                    class="textarea textarea-bordered w-full focus:textarea-primary" 
                                    rows="5"
                                    placeholder="Adicione observações sobre a decisão..."></textarea>
                                <label class="label">
                                    <span class="label-text-alt">Máximo 1000 caracteres</span>
                                </label>
                            </div>

                            <div class="card-actions justify-end mt-6">
                                <button type="submit" class="btn btn-primary w-full gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Atualizar Status
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @elseif($devolucao->status === 'aprovada')
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h2 class="card-title mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Concluir Devolução
                        </h2>
                        
                        <form method="POST" action="{{ route('devolucoes.update', $devolucao->id) }}" class="space-y-4">
                            @csrf
                            @method('PUT')
                            
                            <input type="hidden" name="status" value="concluida">
                            
                            <div class="alert alert-info">
                                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div class="text-sm">
                                    Ao concluir, o estoque será ajustado automaticamente e 
                                    @if($devolucao->tipo === 'troca')
                                        um novo pedido será criado.
                                    @else
                                        um reembolso será gerado.
                                    @endif
                                </div>
                            </div>
                            
                            <div class="form-control w-full">
                                <label class="label">
                                    <span class="label-text font-semibold">Observações Finais</span>
                                </label>
                                <textarea 
                                    name="observacoes" 
                                    class="textarea textarea-bordered w-full focus:textarea-primary" 
                                    rows="5"
                                    placeholder="Adicione observações sobre a conclusão..."></textarea>
                                <label class="label">
                                    <span class="label-text-alt">Máximo 1000 caracteres</span>
                                </label>
                            </div>

                            <div class="card-actions justify-end mt-6">
                                <button type="submit" class="btn btn-success w-full gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Marcar como Concluída
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            <!-- Histórico -->
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <h2 class="card-title mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Histórico de Alterações
                    </h2>
                    
                    <div class="space-y-4">
                        @forelse($devolucao->historico as $historico)
                            <div class="border-l-4 border-primary pl-4 py-2">
                                <div class="flex justify-between items-start gap-4">
                                    <div class="flex-1">
                                        <p class="font-semibold text-lg">
                                            {{ ucfirst($historico->status_old ?? 'Novo') }} 
                                            <span class="text-primary">→</span> 
                                            {{ ucfirst($historico->status_new) }}
                                        </p>
                                        <p class="text-sm text-base-content/60 mt-1 flex items-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            {{ $historico->data_alteracao->format('d/m/Y H:i:s') }}
                                        </p>
                                        @if($historico->alteradoPor)
                                            <p class="text-sm text-base-content/60 flex items-center gap-2 mt-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                                Por: {{ $historico->alteradoPor->name }}
                                            </p>
                                        @endif
                                        @if($historico->observacoes)
                                            <div class="mt-3 p-3 bg-base-200 rounded-lg">
                                                <p class="text-sm text-base-content/80">{{ $historico->observacoes }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <p class="text-base-content/60">Nenhum histórico registrado.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
