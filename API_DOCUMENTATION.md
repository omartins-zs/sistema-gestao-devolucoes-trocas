# Documentação da API - Sistema de Gestão de Devoluções

## 📋 Índice

- [Base URL](#base-url)
- [Autenticação](#autenticação)
- [Formato de Resposta](#formato-de-resposta)
- [Códigos de Status HTTP](#códigos-de-status-http)
- [Endpoints](#endpoints)
  - [Listar Devoluções](#1-listar-devoluções)
  - [Criar Devolução](#2-criar-devolução)
  - [Criar Troca](#3-criar-troca)
  - [Visualizar Devolução](#4-visualizar-devolução)
  - [Atualizar Status](#5-atualizar-status)
- [Exemplos de Uso](#exemplos-de-uso)
- [Importar no Postman](#importar-no-postman)
- [Ações Automáticas](#ações-automáticas)

## Base URL

```
http://localhost:8000/api
```

## Autenticação

Atualmente, a API não requer autenticação. Em produção, recomenda-se implementar Laravel Sanctum ou Passport.

## Formato de Resposta

Todas as respostas seguem o formato padrão:

### Sucesso
```json
{
  "status": "success",
  "message": "Mensagem de sucesso",
  "data": { ... }
}
```

### Erro
```json
{
  "status": "error",
  "message": "Mensagem de erro amigável",
  "error": "Detalhes técnicos do erro"
}
```

## Códigos de Status HTTP

- `200` - Sucesso
- `201` - Criado com sucesso
- `400` - Erro de validação ou regra de negócio
- `404` - Recurso não encontrado
- `500` - Erro interno do servidor

---

## Endpoints

### 1. Listar Devoluções

**GET** `/api/devolucoes`

Lista todas as devoluções com paginação e filtros opcionais.

#### Query Parameters

| Parâmetro | Tipo | Obrigatório | Descrição |
|-----------|------|--------------|-----------|
| `status` | string | Não | Filtrar por status: `pendente`, `aprovada`, `recusada`, `concluida` |
| `cliente_id` | integer | Não | Filtrar por ID do cliente |
| `produto_id` | integer | Não | Filtrar por ID do produto |
| `per_page` | integer | Não | Itens por página (padrão: 15) |

#### Exemplo de Requisição

```bash
GET /api/devolucoes?status=pendente&per_page=10
```

#### Exemplo de Resposta (200 OK)

```json
{
  "status": "success",
  "message": "Devoluções listadas com sucesso",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "pedido_item_id": 1,
        "cliente_id": 1,
        "produto_id": 1,
        "quantidade": 2,
        "motivo": "Produto com defeito na tela",
        "status": "pendente",
        "data_solicitacao": "2024-12-26T15:00:00.000000Z",
        "data_status": null,
        "observacoes": null,
        "created_at": "2024-12-26T15:00:00.000000Z",
        "updated_at": "2024-12-26T15:00:00.000000Z",
        "cliente": {
          "id": 1,
          "nome": "João Silva",
          "email": "joao.silva@email.com",
          "telefone": "(11) 98765-4321"
        },
        "produto": {
          "id": 1,
          "sku": "PROD-001",
          "nome": "Notebook Dell Inspiron 15",
          "preco": "3299.90"
        },
        "pedido_item": {
          "id": 1,
          "pedido_id": 1,
          "produto_id": 1,
          "quantidade": 2,
          "preco_unitario": "3299.90",
          "pedido": {
            "id": 1,
            "cliente_id": 1,
            "data_pedido": "2024-11-15",
            "total": "6599.80"
          }
        }
      }
    ],
    "first_page_url": "http://localhost:8000/api/devolucoes?page=1",
    "from": 1,
    "last_page": 1,
    "last_page_url": "http://localhost:8000/api/devolucoes?page=1",
    "links": [...],
    "next_page_url": null,
    "path": "http://localhost:8000/api/devolucoes",
    "per_page": 15,
    "prev_page_url": null,
    "to": 1,
    "total": 1
  }
}
```

---

### 2. Criar Devolução

**POST** `/api/devolucoes`

Cria uma nova solicitação de devolução. O status inicial será `pendente`.

#### Body (JSON)

| Campo | Tipo | Obrigatório | Descrição |
|-------|------|-------------|-----------|
| `pedido_item_id` | integer | Sim | ID do item do pedido |
| `quantidade` | integer | Sim | Quantidade a devolver (mínimo: 1) |
| `motivo` | string | Sim | Motivo da devolução (mínimo: 10 caracteres, máximo: 1000) |
| `tipo` | string | Não | Tipo: `devolucao` (padrão) ou `troca` |
| `produto_troca_id` | integer | Sim* | ID do produto de troca (obrigatório se tipo for `troca`) |

\* Obrigatório apenas quando `tipo` for `troca`

#### Validações

- `pedido_item_id` deve existir na tabela `pedido_items`
- `quantidade` deve ser maior que 0
- `quantidade` não pode exceder a quantidade do item do pedido
- `motivo` deve ter entre 10 e 1000 caracteres
- Se `tipo` for `troca`, `produto_troca_id` é obrigatório
- `produto_troca_id` deve ser diferente do produto devolvido
- `produto_troca_id` deve existir na tabela `produtos`

#### Exemplo de Requisição

```bash
POST /api/devolucoes
Content-Type: application/json
Accept: application/json

{
  "pedido_item_id": 1,
  "quantidade": 2,
  "motivo": "Produto com defeito na tela. A tela apresenta riscos e não liga corretamente.",
  "tipo": "devolucao"
}
```

**Exemplo de Requisição - Troca:**
```bash
POST /api/devolucoes
Content-Type: application/json
Accept: application/json

{
  "pedido_item_id": 1,
  "quantidade": 1,
  "motivo": "Produto não corresponde à descrição. Quero trocar por outro modelo.",
  "tipo": "troca",
  "produto_troca_id": 2,
  "motivo_troca": "Foi enviado o pedido errado. Era a cor preta e veio azul"
}
```

**Campos Adicionais para Troca:**
- `motivo_troca` (obrigatório quando tipo=troca): Motivo específico da troca (mínimo: 10 caracteres, máximo: 1000)
  - Exemplos:
    - "Foi enviado o pedido errado. Era a cor preta e veio azul"
    - "Pedi uma bola de basquete e veio uma de futsal"
    - "Tamanho incorreto. Preciso de um tamanho maior"

#### Exemplo de Resposta (201 Created)

```json
{
  "status": "success",
  "message": "Devolução criada com sucesso",
  "data": {
    "id": 1,
    "pedido_item_id": 1,
    "cliente_id": 1,
    "produto_id": 1,
    "produto_troca_id": null,
    "quantidade": 2,
    "motivo": "Produto com defeito na tela. A tela apresenta riscos e não liga corretamente.",
    "status": "pendente",
    "tipo": "devolucao",
    "data_solicitacao": "2024-12-26T15:00:00.000000Z",
    "data_status": null,
    "observacoes": null,
    "created_at": "2024-12-26T15:00:00.000000Z",
    "updated_at": "2024-12-26T15:00:00.000000Z",
    "cliente": {
      "id": 1,
      "nome": "João Silva",
      "email": "joao.silva@email.com"
    },
    "produto": {
      "id": 1,
      "nome": "Notebook Dell Inspiron 15",
      "sku": "PROD-001"
    },
    "pedido_item": {
      "id": 1,
      "pedido_id": 1,
      "quantidade": 2
    }
  }
}
```

#### Exemplo de Resposta de Erro (400 Bad Request)

```json
{
  "status": "error",
  "message": "Erro ao criar devolução",
  "error": "Quantidade solicitada (5) excede a quantidade do pedido (2)"
}
```

---

### 4. Visualizar Devolução

**GET** `/api/devolucoes/{id}`

Obtém os detalhes completos de uma devolução específica, incluindo todos os relacionamentos (cliente, produto, histórico, pedido de troca, reembolso, código de rastreamento).

#### Parâmetros de URL

| Parâmetro | Tipo | Descrição |
|-----------|------|-----------|
| `id` | integer | ID da devolução |

#### Exemplo de Requisição

```bash
GET /api/devolucoes/1
```

#### Exemplo de Resposta (200 OK)

```json
{
  "status": "success",
  "message": "Devolução encontrada",
  "data": {
    "id": 1,
    "pedido_item_id": 1,
    "cliente_id": 1,
    "produto_id": 1,
    "quantidade": 2,
    "motivo": "Produto com defeito na tela",
    "status": "aprovada",
    "data_solicitacao": "2024-12-26T15:00:00.000000Z",
    "data_status": "2024-12-26T16:00:00.000000Z",
    "observacoes": "Devolução aprovada. Cliente deve enviar o produto.",
    "created_at": "2024-12-26T15:00:00.000000Z",
    "updated_at": "2024-12-26T16:00:00.000000Z",
    "cliente": {
      "id": 1,
      "nome": "João Silva",
      "email": "joao.silva@email.com",
      "telefone": "(11) 98765-4321"
    },
    "produto": {
      "id": 1,
      "sku": "PROD-001",
      "nome": "Notebook Dell Inspiron 15",
      "preco": "3299.90"
    },
    "pedido_item": {
      "id": 1,
      "pedido_id": 1,
      "produto_id": 1,
      "quantidade": 2,
      "preco_unitario": "3299.90",
      "pedido": {
        "id": 1,
        "cliente_id": 1,
        "data_pedido": "2024-11-15",
        "total": "6599.80"
      }
    },
    "historico": [
      {
        "id": 1,
        "devolucao_id": 1,
        "status_old": null,
        "status_new": "pendente",
        "alterado_por": null,
        "data_alteracao": "2024-12-26T15:00:00.000000Z",
        "observacoes": "Solicitação de devolução criada"
      },
      {
        "id": 2,
        "devolucao_id": 1,
        "status_old": "pendente",
        "status_new": "aprovada",
        "alterado_por": 1,
        "data_alteracao": "2024-12-26T16:00:00.000000Z",
        "observacoes": "Devolução aprovada. Cliente deve enviar o produto.",
        "alterado_por_user": {
          "id": 1,
          "name": "Administrador",
          "email": "admin@example.com"
        }
      }
    ],
    "lembretes_email": [
      {
        "id": 1,
        "devolucao_id": 1,
        "data_envio": "2024-12-26T16:00:05.000000Z",
        "canal": "email"
      }
    ]
  }
}
```

#### Exemplo de Resposta de Erro (404 Not Found)

```json
{
  "status": "error",
  "message": "Devolução não encontrada",
  "error": "No query results for model [App\\Models\\Devolucao] 999"
}
```

---

### 5. Atualizar Status

**PUT** `/api/devolucoes/{id}`

Atualiza o status de uma devolução. Dispara e-mail de notificação ao cliente automaticamente.

#### Parâmetros de URL

| Parâmetro | Tipo | Descrição |
|-----------|------|-----------|
| `id` | integer | ID da devolução |

#### Body (JSON)

| Campo | Tipo | Obrigatório | Descrição |
|-------|------|-------------|-----------|
| `status` | string | Sim | Novo status: `pendente`, `aprovada`, `recusada`, `concluida` |
| `observacoes` | string | Não | Observações sobre a alteração (máximo: 1000 caracteres) |

#### Regras de Transição

- `pendente` → `aprovada` ou `recusada`
- `aprovada` → `concluida` (ajusta estoque automaticamente)
- `recusada` → (fim, não pode mudar)
- `concluida` → (fim, não pode mudar)

#### Exemplo de Requisição - Aprovar

```bash
PUT /api/devolucoes/1
Content-Type: application/json
Accept: application/json

{
  "status": "aprovada",
  "observacoes": "Devolução aprovada. Cliente deve enviar o produto para o endereço: Rua Exemplo, 123 - São Paulo/SP. CEP: 01234-567."
}
```

#### Exemplo de Requisição - Recusar

```bash
PUT /api/devolucoes/1
Content-Type: application/json
Accept: application/json

{
  "status": "recusada",
  "observacoes": "Devolução recusada. O produto não se enquadra na política de devolução. Prazo de 7 dias ultrapassado."
}
```

#### Exemplo de Requisição - Concluir

```bash
PUT /api/devolucoes/1
Content-Type: application/json
Accept: application/json

{
  "status": "concluida",
  "observacoes": "Devolução concluída. Produto recebido e em bom estado. Estoque ajustado automaticamente. Reembolso processado."
}
```

#### Exemplo de Resposta (200 OK)

```json
{
  "status": "success",
  "message": "Status da devolução atualizado com sucesso",
  "data": {
    "id": 1,
    "status": "aprovada",
    "data_status": "2024-12-26T16:00:00.000000Z",
    "observacoes": "Devolução aprovada. Cliente deve enviar o produto.",
    "historico": [
      {
        "id": 2,
        "status_old": "pendente",
        "status_new": "aprovada",
        "data_alteracao": "2024-12-26T16:00:00.000000Z"
      }
    ]
  }
}
```

#### Exemplo de Resposta de Erro (400 Bad Request)

```json
{
  "status": "error",
  "message": "Erro ao atualizar status da devolução",
  "error": "Transição de status inválida: de 'recusada' para 'aprovada'"
}
```

---

## Exemplos de Uso

### cURL

#### Listar Devoluções Pendentes

```bash
curl -X GET "http://localhost:8000/api/devolucoes?status=pendente" \
  -H "Accept: application/json"
```

#### Criar Devolução

```bash
curl -X POST "http://localhost:8000/api/devolucoes" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "pedido_item_id": 1,
    "quantidade": 2,
    "motivo": "Produto com defeito na tela. A tela apresenta riscos e não liga corretamente."
  }'
```

#### Atualizar Status

```bash
curl -X PUT "http://localhost:8000/api/devolucoes/1" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "status": "aprovada",
    "observacoes": "Devolução aprovada. Cliente deve enviar o produto."
  }'
```

### JavaScript (Fetch API)

```javascript
// Listar devoluções
fetch('http://localhost:8000/api/devolucoes?status=pendente', {
  headers: {
    'Accept': 'application/json'
  }
})
.then(response => response.json())
.then(data => console.log(data));

// Criar devolução
fetch('http://localhost:8000/api/devolucoes', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  },
  body: JSON.stringify({
    pedido_item_id: 1,
    quantidade: 2,
    motivo: 'Produto com defeito na tela'
  })
})
.then(response => response.json())
.then(data => console.log(data));

// Atualizar status
fetch('http://localhost:8000/api/devolucoes/1', {
  method: 'PUT',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  },
  body: JSON.stringify({
    status: 'aprovada',
    observacoes: 'Devolução aprovada'
  })
})
.then(response => response.json())
.then(data => console.log(data));
```

### PHP (Guzzle)

```php
use GuzzleHttp\Client;

$client = new Client(['base_uri' => 'http://localhost:8000']);

// Listar devoluções
$response = $client->get('/api/devolucoes', [
    'query' => ['status' => 'pendente'],
    'headers' => ['Accept' => 'application/json']
]);
$devolucoes = json_decode($response->getBody(), true);

// Criar devolução
$response = $client->post('/api/devolucoes', [
    'json' => [
        'pedido_item_id' => 1,
        'quantidade' => 2,
        'motivo' => 'Produto com defeito na tela'
    ],
    'headers' => ['Accept' => 'application/json']
]);
$devolucao = json_decode($response->getBody(), true);

// Atualizar status
$response = $client->put('/api/devolucoes/1', [
    'json' => [
        'status' => 'aprovada',
        'observacoes' => 'Devolução aprovada'
    ],
    'headers' => ['Accept' => 'application/json']
]);
$resultado = json_decode($response->getBody(), true);
```

---

## Código de Rastreamento

Quando uma devolução é aprovada ou concluída, é possível gerar um código de rastreamento único para o envio do produto.

**Formato do código:** `BR{ID}{RANDOM}BR`
- Exemplo: `BR0001A5B6C7D8BR`

**Geração:**
- Pode ser gerado manualmente através da interface web (`POST /devolucoes/{id}/gerar-codigo-rastreamento`)
- Campo `codigo_rastreamento` na tabela devolucoes (unique, nullable)
- Campo `data_envio` registra quando foi enviado
- Código incluído nos e-mails de notificação

**Uso:**
- Cliente pode rastrear o envio do produto usando este código
- Sistema de logística pode usar para rastreamento

## Importar no Postman

### Método 1: Importar Collection JSON

1. Abra o Postman
2. Clique em **Import** (canto superior esquerdo)
3. Selecione a opção **File** ou **Link**
4. Selecione o arquivo `postman/Sistema_Devolucoes.postman_collection.json`
5. Clique em **Import**

### Método 2: Importar via URL (se hospedado)

1. Abra o Postman
2. Clique em **Import**
3. Cole a URL da collection
4. Clique em **Import**

### Configurar Variável de Ambiente

Após importar, configure a variável `base_url`:

1. Clique no ícone de **olho** (variáveis) no canto superior direito
2. Adicione uma variável:
   - **Variable**: `base_url`
   - **Initial Value**: `http://localhost:8000`
   - **Current Value**: `http://localhost:8000`

Ou crie um Environment:

1. Clique em **Environments** (lateral esquerda)
2. Clique em **+** para criar novo
3. Nome: `Local Development`
4. Adicione variável `base_url` com valor `http://localhost:8000`
5. Selecione o environment no dropdown superior direito

### Testar Requisições

1. Execute os seeders para ter dados de exemplo:
   ```bash
   php artisan migrate:fresh --seed
   ```

2. No Postman, selecione uma requisição (ex: "Listar Devoluções")
3. Clique em **Send**
4. Verifique a resposta

### Exemplos de Dados para Teste

Após executar os seeders, você terá:
- **15 Clientes** (IDs: 1-15)
- **15 Produtos** (IDs: 1-15)
- **30 Pedidos** com múltiplos itens
- **50 Devoluções** (IDs: 1-50) com exemplos variados:
  - 20 devoluções simples
  - 15 trocas com motivo_troca
  - 15 reembolsos
  - Códigos de rastreamento aleatórios
  - Status variados (pendente, aprovada, recusada, concluida)

Para obter IDs válidos, primeiro liste os pedidos ou use a interface web.

---

## Notas Importantes

1. **E-mails**: Os e-mails são enviados de forma assíncrona via fila. Certifique-se de que o worker está rodando:
   ```bash
   php artisan queue:work
   ```

2. **Estoque**: O estoque só é ajustado quando o status muda de `aprovada` para `concluida`.

3. **Histórico**: Todas as alterações de status são registradas automaticamente na tabela `devolucao_historico`.

4. **Validações**: As validações são feitas tanto no Form Request quanto no Service para garantir integridade.

5. **Transações**: Todas as operações críticas são envolvidas em transações de banco de dados.

---

## Suporte

Para dúvidas ou problemas, consulte:
- `README.md` - Documentação geral do projeto
- `DECISOES_TECNICAS.md` - Decisões arquiteturais
- `ESTRUTURA_PROJETO.md` - Estrutura do projeto

