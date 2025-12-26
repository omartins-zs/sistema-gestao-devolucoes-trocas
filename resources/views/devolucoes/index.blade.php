@extends('layouts.app')

@section('title', 'Devoluções')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-4xl font-bold">Devoluções e Trocas</h1>
            <p class="text-base-content/60 mt-2">Gerencie todas as solicitações de devolução e troca</p>
        </div>
    </div>
    
    <!-- Filtros -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <h2 class="card-title mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                Filtros
            </h2>
            <form method="GET" action="{{ route('devolucoes.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="form-control w-full">
                    <label class="label">
                        <span class="label-text font-semibold">Status</span>
                    </label>
                    <select name="status" class="select select-bordered w-full focus:select-primary">
                        <option value="">Todos os status</option>
                        <option value="pendente" {{ ($filtros['status'] ?? '') === 'pendente' ? 'selected' : '' }}>Pendente</option>
                        <option value="aprovada" {{ ($filtros['status'] ?? '') === 'aprovada' ? 'selected' : '' }}>Aprovada</option>
                        <option value="recusada" {{ ($filtros['status'] ?? '') === 'recusada' ? 'selected' : '' }}>Recusada</option>
                        <option value="concluida" {{ ($filtros['status'] ?? '') === 'concluida' ? 'selected' : '' }}>Concluída</option>
                    </select>
                </div>
                <div class="form-control w-full md:col-span-2">
                    <label class="label">
                        <span class="label-text font-semibold">Buscar</span>
                    </label>
                    <input type="text" name="search" placeholder="Buscar por cliente, produto..." class="input input-bordered w-full focus:input-primary" value="{{ $filtros['search'] ?? '' }}">
                </div>
                <div class="form-control w-full flex flex-col justify-end gap-2">
                    <button type="submit" class="btn btn-primary w-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Filtrar
                    </button>
                    <a href="{{ route('devolucoes.index') }}" class="btn btn-ghost w-full">Limpar Filtros</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabela -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table table-zebra">
                    <thead>
                        <tr class="bg-base-200">
                            <th class="font-bold">ID</th>
                            <th>Tipo</th>
                            <th>Cliente</th>
                            <th>Produto</th>
                            <th>Quantidade</th>
                            <th>Status</th>
                            <th>Data Solicitação</th>
                            <th class="text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($devolucoes as $devolucao)
                            <tr class="hover">
                                <td class="font-bold">#{{ $devolucao->id }}</td>
                                <td>
                                    <div class="badge {{ $devolucao->tipo === 'troca' ? 'badge-primary gap-2' : 'badge-ghost gap-2' }}">
                                        @if($devolucao->tipo === 'troca')
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                            </svg>
                                        @endif
                                        {{ $devolucao->tipo === 'troca' ? 'Troca' : 'Devolução' }}
                                    </div>
                                </td>
                                <td>
                                    <div class="font-semibold">{{ $devolucao->cliente->nome }}</div>
                                    <div class="text-sm text-base-content/60">{{ $devolucao->cliente->email }}</div>
                                </td>
                                <td>
                                    <div class="font-semibold">{{ $devolucao->produto->nome }}</div>
                                    <div class="text-sm text-base-content/60">SKU: {{ $devolucao->produto->sku }}</div>
                                </td>
                                <td>
                                    <div class="badge badge-outline">{{ $devolucao->quantidade }}</div>
                                </td>
                                <td>
                                    @if($devolucao->status === 'pendente')
                                        <div class="badge badge-warning gap-2">
                                            <span class="loading loading-spinner loading-xs"></span>
                                            Pendente
                                        </div>
                                    @elseif($devolucao->status === 'aprovada')
                                        <div class="badge badge-success gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            Aprovada
                                        </div>
                                    @elseif($devolucao->status === 'recusada')
                                        <div class="badge badge-error gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                            Recusada
                                        </div>
                                    @else
                                        <div class="badge badge-info gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Concluída
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="text-sm">{{ $devolucao->data_solicitacao->format('d/m/Y') }}</div>
                                    <div class="text-xs text-base-content/60">{{ $devolucao->data_solicitacao->format('H:i') }}</div>
                                </td>
                                <td class="text-right">
                                    <a href="{{ route('devolucoes.show', $devolucao->id) }}" class="btn btn-sm btn-primary gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        Ver Detalhes
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-12">
                                    <div class="flex flex-col items-center gap-4">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-base-content/30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <p class="text-lg font-semibold text-base-content/60">Nenhuma devolução encontrada</p>
                                        <p class="text-sm text-base-content/50">Tente ajustar os filtros ou criar uma nova devolução</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($devolucoes->hasPages())
                <div class="p-4 border-t bg-base-50">
                    <div class="flex justify-center">
                        {{ $devolucoes->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
