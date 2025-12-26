@extends('layouts.app')

@section('title', 'Detalhes do Reembolso')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div>
        <a href="{{ route('reembolsos.index') }}" class="btn btn-ghost btn-sm mb-4 gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Voltar para lista
        </a>
        <div class="flex items-center gap-4">
            <h1 class="text-4xl font-bold">Reembolso #{{ $reembolso->id }}</h1>
            <div class="badge badge-lg {{ $reembolso->status === 'processado' ? 'badge-success' : ($reembolso->status === 'cancelado' ? 'badge-error' : 'badge-warning') }}">
                {{ ucfirst($reembolso->status) }}
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
                    Informações do Reembolso
                </h2>
                
                <div class="space-y-6">
                    <!-- Valor -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Valor do Reembolso</span>
                        </label>
                        <div class="mt-2">
                            <div class="stat bg-primary text-primary-content rounded-lg p-6">
                                <div class="stat-title text-primary-content/70">Total</div>
                                <div class="stat-value text-4xl">R$ {{ number_format($reembolso->valor, 2, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Status</span>
                        </label>
                        <div class="mt-2">
                            @if($reembolso->status === 'pendente')
                                <div class="badge badge-warning badge-lg gap-2">
                                    <span class="loading loading-spinner loading-xs"></span>
                                    Pendente
                                </div>
                            @elseif($reembolso->status === 'processado')
                                <div class="badge badge-success badge-lg gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Processado
                                </div>
                            @else
                                <div class="badge badge-error badge-lg gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Cancelado
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Autorização -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Autorização</span>
                        </label>
                        <div class="mt-2">
                            @if($reembolso->autorizado)
                                <div class="alert alert-success">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <div>
                                        <div class="font-semibold">Autorizado</div>
                                        @if($reembolso->autorizadoPor)
                                            <div class="text-sm">Por: {{ $reembolso->autorizadoPor->name }} em {{ $reembolso->data_autorizacao->format('d/m/Y H:i') }}</div>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <div>
                                        <div class="font-semibold">Não Autorizado</div>
                                        <div class="text-sm">Este reembolso precisa ser autorizado antes de ser processado</div>
                                    </div>
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
                            <div class="font-semibold text-lg">{{ $reembolso->cliente->nome }}</div>
                            <div class="text-sm text-base-content/70 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                {{ $reembolso->cliente->email }}
                            </div>
                        </div>
                    </div>

                    <!-- Devolução -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Devolução Relacionada</span>
                        </label>
                        <div class="mt-2 space-y-1">
                            <a href="{{ route('devolucoes.show', $reembolso->devolucao_id) }}" class="link link-primary font-semibold text-lg">
                                Devolução #{{ $reembolso->devolucao_id }}
                            </a>
                            <div class="text-sm text-base-content/70">Produto: {{ $reembolso->devolucao->produto->nome }}</div>
                        </div>
                    </div>

                    @if($reembolso->metodo)
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">Método de Reembolso</span>
                            </label>
                            <div class="mt-2">
                                <div class="badge badge-outline badge-lg">
                                    {{ ucfirst(str_replace('_', ' ', $reembolso->metodo)) }}
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($reembolso->data_processamento)
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">Data de Processamento</span>
                            </label>
                            <div class="mt-2 text-sm">{{ $reembolso->data_processamento->format('d/m/Y H:i:s') }}</div>
                        </div>
                    @endif

                    @if($reembolso->processadoPor)
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">Processado por</span>
                            </label>
                            <div class="mt-2 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                {{ $reembolso->processadoPor->name }}
                            </div>
                        </div>
                    @endif

                    @if($reembolso->observacoes)
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">Observações</span>
                            </label>
                            <div class="mt-2 p-4 bg-base-200 rounded-lg">
                                <p class="text-base-content/80">{{ $reembolso->observacoes }}</p>
                            </div>
                        </div>
                    @endif

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Data de Criação</span>
                        </label>
                        <div class="mt-2 text-sm">{{ $reembolso->created_at->format('d/m/Y H:i:s') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ações -->
        <div class="space-y-6">
            @if(!$reembolso->autorizado && $reembolso->status === 'pendente')
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h2 class="card-title mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            Autorizar Reembolso
                        </h2>
                        
                        <form method="POST" action="{{ route('reembolsos.autorizar', $reembolso->id) }}" class="space-y-4">
                            @csrf
                            
                            <div class="form-control">
                                <label class="label cursor-pointer">
                                    <span class="label-text font-semibold">Autorizar Reembolso</span>
                                    <input type="radio" name="autorizado" value="1" class="radio radio-primary" checked />
                                </label>
                            </div>
                            
                            <div class="form-control">
                                <label class="label cursor-pointer">
                                    <span class="label-text font-semibold">Negar Reembolso</span>
                                    <input type="radio" name="autorizado" value="0" class="radio radio-primary" />
                                </label>
                            </div>

                            <div class="form-control w-full">
                                <label class="label">
                                    <span class="label-text font-semibold">Observações</span>
                                </label>
                                <textarea 
                                    name="observacoes" 
                                    class="textarea textarea-bordered w-full focus:textarea-primary" 
                                    rows="5"
                                    placeholder="Informações sobre a autorização ou negação..."></textarea>
                                <label class="label">
                                    <span class="label-text-alt">Máximo 1000 caracteres</span>
                                </label>
                            </div>

                            <div class="card-actions justify-end mt-6">
                                <button type="submit" class="btn btn-primary w-full gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Confirmar Decisão
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @elseif($reembolso->autorizado && $reembolso->status === 'pendente')
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h2 class="card-title mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Processar Reembolso
                        </h2>
                        
                        <form method="POST" action="{{ route('reembolsos.processar', $reembolso->id) }}" class="space-y-4">
                            @csrf
                            
                            <div class="alert alert-info">
                                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div class="text-sm">
                                    Este reembolso foi autorizado e está pronto para ser processado. Selecione o método de pagamento abaixo.
                                </div>
                            </div>
                            
                            <div class="form-control w-full">
                                <label class="label">
                                    <span class="label-text font-semibold">Método de Reembolso</span>
                                    <span class="label-text-alt text-error">* Obrigatório</span>
                                </label>
                                <select name="metodo" class="select select-bordered w-full focus:select-primary" required>
                                    <option value="">Selecione o método...</option>
                                    <option value="credito_estorno">Crédito/Estorno no Cartão</option>
                                    <option value="transferencia">Transferência Bancária</option>
                                    <option value="boleto">Boleto</option>
                                    <option value="pix">PIX</option>
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
                                    placeholder="Informações adicionais sobre o processamento (ex: número da transação, código de rastreamento, etc.)..."></textarea>
                                <label class="label">
                                    <span class="label-text-alt">Máximo 1000 caracteres</span>
                                </label>
                            </div>

                            <div class="card-actions justify-end mt-6">
                                <button type="submit" class="btn btn-success w-full gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Processar Reembolso
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @else
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h2 class="card-title mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Reembolso {{ $reembolso->status === 'processado' ? 'Processado' : 'Cancelado' }}
                        </h2>
                        <div class="alert {{ $reembolso->status === 'processado' ? 'alert-success' : 'alert-error' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                                @if($reembolso->status === 'processado')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                @else
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                @endif
                            </svg>
                            <div>
                                <div class="font-semibold">
                                    Este reembolso já foi {{ $reembolso->status === 'processado' ? 'processado com sucesso' : 'cancelado' }}
                                </div>
                                @if($reembolso->data_processamento)
                                    <div class="text-sm">Em {{ $reembolso->data_processamento->format('d/m/Y H:i') }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
