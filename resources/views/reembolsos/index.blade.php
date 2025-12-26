@extends('layouts.app')

@section('title', 'Reembolsos')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-4xl font-bold">Reembolsos</h1>
            <p class="text-base-content/60 mt-2">Gerencie todos os reembolsos do sistema</p>
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
            <form method="GET" action="{{ route('reembolsos.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="form-control w-full">
                    <label class="label">
                        <span class="label-text font-semibold">Status</span>
                    </label>
                    <select name="status" class="select select-bordered w-full focus:select-primary">
                        <option value="">Todos os status</option>
                        <option value="pendente" {{ ($filtros['status'] ?? '') === 'pendente' ? 'selected' : '' }}>Pendente</option>
                        <option value="processado" {{ ($filtros['status'] ?? '') === 'processado' ? 'selected' : '' }}>Processado</option>
                        <option value="cancelado" {{ ($filtros['status'] ?? '') === 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                    </select>
                </div>
                <div class="form-control w-full">
                    <label class="label">
                        <span class="label-text font-semibold">Autorização</span>
                    </label>
                    <select name="autorizado" class="select select-bordered w-full focus:select-primary">
                        <option value="">Todos</option>
                        <option value="1" {{ ($filtros['autorizado'] ?? '') === '1' ? 'selected' : '' }}>Autorizados</option>
                        <option value="0" {{ ($filtros['autorizado'] ?? '') === '0' ? 'selected' : '' }}>Não Autorizados</option>
                    </select>
                </div>
                <div class="form-control w-full md:col-span-2">
                    <label class="label">
                        <span class="label-text font-semibold">Buscar</span>
                    </label>
                    <input type="text" name="search" placeholder="Buscar por cliente, devolução..." class="input input-bordered w-full focus:input-primary" value="{{ $filtros['search'] ?? '' }}">
                </div>
                <div class="form-control w-full md:col-span-4 flex flex-row gap-2">
                    <button type="submit" class="btn btn-primary flex-1 gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Filtrar
                    </button>
                    <a href="{{ route('reembolsos.index') }}" class="btn btn-ghost">Limpar</a>
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
                            <th>Cliente</th>
                            <th>Devolução</th>
                            <th>Valor</th>
                            <th class="min-w-[140px]">Autorizado</th>
                            <th>Status</th>
                            <th>Método</th>
                            <th>Data</th>
                            <th class="text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reembolsos as $reembolso)
                            <tr class="hover">
                                <td class="font-bold">#{{ $reembolso->id }}</td>
                                <td>
                                    <div class="font-semibold">{{ $reembolso->cliente->nome }}</div>
                                    <div class="text-sm text-base-content/60">{{ $reembolso->cliente->email }}</div>
                                </td>
                                <td>
                                    <a href="{{ route('devolucoes.show', $reembolso->devolucao_id) }}" class="link link-primary font-semibold">
                                        Devolução #{{ $reembolso->devolucao_id }}
                                    </a>
                                </td>
                                <td>
                                    <div class="font-bold text-lg text-primary">
                                        R$ {{ number_format($reembolso->valor, 2, ',', '.') }}
                                    </div>
                                </td>
                                <td>
                                    @if($reembolso->autorizado)
                                        <div class="badge badge-success gap-2 whitespace-nowrap">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            <span class="truncate">Autorizado</span>
                                        </div>
                                    @else
                                        <div class="badge badge-warning gap-2 whitespace-nowrap">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span class="truncate">Não Autorizado</span>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    @if($reembolso->status === 'pendente')
                                        <div class="badge badge-warning gap-2 whitespace-nowrap">
                                            <span class="loading loading-spinner loading-xs shrink-0"></span>
                                            <span class="truncate">Pendente</span>
                                        </div>
                                    @elseif($reembolso->status === 'processado')
                                        <div class="badge badge-success gap-2 whitespace-nowrap">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span class="truncate">Processado</span>
                                        </div>
                                    @else
                                        <div class="badge badge-error gap-2 whitespace-nowrap">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                            <span class="truncate">Cancelado</span>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    @if($reembolso->metodo)
                                        <div class="badge badge-outline whitespace-nowrap">
                                            <span class="truncate max-w-[120px]">{{ ucfirst(str_replace('_', ' ', $reembolso->metodo)) }}</span>
                                        </div>
                                    @else
                                        <span class="text-base-content/50">-</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="text-sm">{{ $reembolso->created_at->format('d/m/Y') }}</div>
                                    <div class="text-xs text-base-content/60">{{ $reembolso->created_at->format('H:i') }}</div>
                                </td>
                                <td class="text-right">
                                    <a href="{{ route('reembolsos.show', $reembolso->id) }}" class="btn btn-sm btn-primary gap-2">
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
                                <td colspan="9" class="text-center py-12">
                                    <div class="flex flex-col items-center gap-4">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-base-content/30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <p class="text-lg font-semibold text-base-content/60">Nenhum reembolso encontrado</p>
                                        <p class="text-sm text-base-content/50">Tente ajustar os filtros</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($reembolsos->hasPages())
                <div class="p-4 border-t bg-base-50">
                    <div class="flex justify-center">
                        {{ $reembolsos->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
