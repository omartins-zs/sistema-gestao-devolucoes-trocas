# Estrutura do Projeto

Este documento descreve a estrutura completa do projeto e onde encontrar cada componente.

## 📁 Estrutura de Diretórios

```
sistema-gestao-devolucoes-trocas/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/
│   │   │   │   └── DevolucaoController.php    # API RESTful
│   │   │   └── Web/
│   │   │       └── DevolucaoController.php      # Interface web
│   │   └── Requests/
│   │       ├── StoreDevolucaoRequest.php       # Validação criação
│   │       └── UpdateDevolucaoStatusRequest.php # Validação atualização
│   ├── Jobs/
│   │   └── EnviarEmailNotificacaoDevolucao.php # Job assíncrono
│   ├── Models/
│   │   ├── Cliente.php
│   │   ├── Produto.php
│   │   ├── Pedido.php
│   │   ├── PedidoItem.php
│   │   ├── EstoqueAtual.php
│   │   ├── Devolucao.php
│   │   ├── DevolucaoHistorico.php
│   │   ├── LembreteEmail.php
│   │   └── User.php
│   └── Services/
│       ├── DevolucaoService.php                # Lógica de devoluções
│       └── EstoqueService.php                  # Lógica de estoque
├── database/
│   ├── migrations/
│   │   ├── 2025_12_26_155716_create_clientes_table.php
│   │   ├── 2025_12_26_155717_create_produtos_table.php
│   │   ├── 2025_12_26_155718_create_pedidos_table.php
│   │   ├── 2025_12_26_155719_create_pedido_items_table.php
│   │   ├── 2025_12_26_155720_create_estoque_atual_table.php
│   │   ├── 2025_12_26_155721_create_devolucoes_table.php
│   │   ├── 2025_12_26_155722_create_devolucao_historico_table.php
│   │   └── 2025_12_26_155722_create_lembretes_email_table.php
│   └── seeders/
│       ├── DatabaseSeeder.php
│       ├── ClienteSeeder.php
│       ├── ProdutoSeeder.php
│       ├── PedidoSeeder.php
│       └── DevolucaoSeeder.php
├── resources/
│   └── views/
│       ├── layouts/
│       │   └── app.blade.php                   # Layout base
│       └── devolucoes/
│           ├── index.blade.php                 # Listagem
│           └── show.blade.php                  # Detalhes
├── routes/
│   ├── web.php                                 # Rotas web
│   └── api.php                                 # Rotas API
└── README.md                                   # Documentação principal
```

## 🔗 Fluxo de Dados

### Criação de Devolução

```
Cliente/API
    ↓
DevolucaoController (API ou Web)
    ↓
StoreDevolucaoRequest (validação)
    ↓
DevolucaoService::criarDevolucao()
    ↓
Devolucao (Model) → Banco de Dados
    ↓
DevolucaoHistorico (registro inicial)
```

### Atualização de Status

```
Gestor/API
    ↓
DevolucaoController::update()
    ↓
UpdateDevolucaoStatusRequest (validação)
    ↓
DevolucaoService::atualizarStatus()
    ↓
Validação de transição
    ↓
Atualização no banco (transação)
    ↓
Registro no histórico
    ↓
Se concluída: EstoqueService::incrementarEstoque()
    ↓
EnviarEmailNotificacaoDevolucao::dispatch() (assíncrono)
```

## 📊 Modelos e Relacionamentos

### Cliente
- `hasMany` Pedido
- `hasMany` Devolucao

### Produto
- `hasMany` PedidoItem
- `hasMany` Devolucao
- `hasOne` EstoqueAtual

### Pedido
- `belongsTo` Cliente
- `hasMany` PedidoItem

### PedidoItem
- `belongsTo` Pedido
- `belongsTo` Produto
- `hasMany` Devolucao

### Devolucao
- `belongsTo` PedidoItem
- `belongsTo` Cliente
- `belongsTo` Produto
- `hasMany` DevolucaoHistorico
- `hasMany` LembreteEmail

### DevolucaoHistorico
- `belongsTo` Devolucao
- `belongsTo` User (alterado_por)

## 🎯 Endpoints da API

### GET /api/devolucoes
Lista devoluções com filtros opcionais.

### POST /api/devolucoes
Cria nova devolução.

**Body**:
```json
{
  "pedido_item_id": 1,
  "quantidade": 2,
  "motivo": "Produto com defeito"
}
```

### GET /api/devolucoes/{id}
Obtém detalhes de uma devolução.

### PUT /api/devolucoes/{id}
Atualiza status de uma devolução.

**Body**:
```json
{
  "status": "aprovada",
  "observacoes": "Observações opcionais"
}
```

## 🌐 Rotas Web

### GET /devolucoes
Lista devoluções (interface administrativa).

### GET /devolucoes/{id}
Exibe detalhes de uma devolução.

### PUT /devolucoes/{id}
Atualiza status de uma devolução.

## 🔄 Fluxo de Status

```
pendente
    ├──→ aprovada
    │       └──→ concluida (ajusta estoque)
    └──→ recusada (fim)
```

**Regras**:
- `pendente` → `aprovada` ou `recusada`
- `aprovada` → `concluida` (ajusta estoque)
- `recusada` → (fim, não pode mudar)
- `concluida` → (fim, não pode mudar)

## 📧 Sistema de E-mails

### Job: EnviarEmailNotificacaoDevolucao

**Configuração**:
- Tries: 3
- Timeout: 60s
- Backoff: [30s, 60s, 120s]

**Disparo**: Automático quando status muda.

**Registro**: Tabela `lembretes_email` registra todos os envios.

## 🗄️ Tabelas do Banco

1. **clientes**: Dados dos clientes
2. **produtos**: Catálogo de produtos
3. **pedidos**: Pedidos realizados
4. **pedido_items**: Itens de cada pedido
5. **estoque_atual**: Estoque atual de cada produto
6. **devolucoes**: Solicitações de devolução
7. **devolucao_historico**: Histórico de alterações
8. **lembretes_email**: Registro de e-mails enviados

## 🧪 Seeders

Ordem de execução:
1. ClienteSeeder (5 clientes)
2. ProdutoSeeder (6 produtos + estoque)
3. PedidoSeeder (10 pedidos com itens)
4. DevolucaoSeeder (5 devoluções)

Execute com: `php artisan migrate:fresh --seed`

## 📝 Convenções de Código

- **Controllers**: Apenas orquestração
- **Services**: Toda lógica de negócio
- **Form Requests**: Validação isolada
- **Models**: Apenas relacionamentos e casts
- **Jobs**: Processamento assíncrono
- **Nomes**: Português para domínio, inglês para código

## 🔍 Onde Encontrar

- **Lógica de negócio**: `app/Services/`
- **Validações**: `app/Http/Requests/`
- **Endpoints API**: `app/Http/Controllers/Api/`
- **Interface Web**: `app/Http/Controllers/Web/` e `resources/views/`
- **Jobs**: `app/Jobs/`
- **Migrations**: `database/migrations/`
- **Seeders**: `database/seeders/`

