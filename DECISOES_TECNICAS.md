# Decisões Técnicas e Arquiteturais

Este documento explica as principais decisões técnicas tomadas durante o desenvolvimento do sistema.

## 📐 Arquitetura

### Separação de Responsabilidades

O projeto segue uma arquitetura em camadas bem definida:

1. **Controllers**: Apenas orquestram chamadas, não contêm lógica de negócio
2. **Services**: Contêm toda a lógica de negócio e regras complexas
3. **Form Requests**: Validação isolada e reutilizável
4. **Models**: Apenas relacionamentos e configurações do Eloquent
5. **Jobs**: Processamento assíncrono de tarefas pesadas

### Por que Services?

**Problema**: Colocar lógica de negócio diretamente nos Controllers viola o princípio SRP (Single Responsibility Principle).

**Solução**: Criamos Services dedicados (`DevolucaoService`, `EstoqueService`) que:
- Centralizam a lógica de negócio
- São facilmente testáveis
- Podem ser reutilizados em diferentes contextos (API, Web, Commands)
- Facilitam manutenção e evolução do código

**Exemplo**:
```php
// ❌ Ruim: Lógica no Controller
public function store(Request $request) {
    $pedidoItem = PedidoItem::find($request->pedido_item_id);
    if ($request->quantidade > $pedidoItem->quantidade) {
        throw new Exception("Quantidade inválida");
    }
    // ... mais lógica ...
}

// ✅ Bom: Lógica no Service
public function store(StoreDevolucaoRequest $request) {
    $devolucao = $this->devolucaoService->criarDevolucao($request->validated());
    return response()->json($devolucao);
}
```

## 🔄 Sistema de Filas

### Por que usar Filas para E-mails?

**Decisão**: Implementar Job assíncrono para envio de e-mails.

**Justificativa Técnica**:

1. **Performance e Escalabilidade**
   - Envio de e-mail via SMTP pode levar 1-5 segundos
   - Bloquear a resposta HTTP por esse tempo degrada a experiência do usuário
   - Com filas, a resposta é imediata e o e-mail é processado em background

2. **Resiliência**
   - Sistema de retry automático (3 tentativas)
   - Backoff exponencial entre tentativas (30s, 60s, 120s)
   - Tratamento de falhas com método `failed()`
   - Logs estruturados para debugging

3. **Desacoplamento**
   - Se o serviço de e-mail estiver indisponível, a aplicação continua funcionando
   - E-mails são enfileirados e processados quando o serviço voltar

4. **Monitoramento**
   - Fácil rastrear quantos e-mails foram enviados
   - Tabela `lembretes_email` registra todos os envios

**Alternativa Considerada**: Envio síncrono
- ❌ Rejeitada: Bloqueia a resposta HTTP
- ❌ Rejeitada: Sem retry automático
- ❌ Rejeitada: Pior experiência do usuário

## 🗄️ Estrutura do Banco de Dados

### Relacionamentos

Todos os relacionamentos foram definidos com foreign keys para garantir integridade referencial:

- `devolucoes.pedido_item_id` → `pedido_items.id`
- `devolucoes.cliente_id` → `clientes.id`
- `devolucoes.produto_id` → `produtos.id`
- `devolucao_historico.devolucao_id` → `devolucoes.id`
- `devolucao_historico.alterado_por` → `users.id` (nullable)

### Enum para Status

Uso de ENUM no banco de dados para status:
- Garante integridade no nível do banco
- Evita valores inválidos
- Melhor performance que VARCHAR com CHECK constraint

### Tabela de Histórico

**Decisão**: Criar tabela separada `devolucao_historico` ao invés de apenas atualizar `devolucoes`.

**Justificativa**:
- Auditoria completa: todas as mudanças são registradas
- Rastreabilidade: saber quem mudou e quando
- Histórico imutável: não pode ser alterado acidentalmente
- Facilita análises e relatórios

## ✅ Validação

### Form Requests vs Validação no Controller

**Decisão**: Usar Form Requests para todas as validações.

**Vantagens**:
1. **Reutilização**: Mesmas regras podem ser usadas em diferentes endpoints
2. **Testabilidade**: Fácil testar validações isoladamente
3. **Mensagens Personalizadas**: Mensagens de erro claras e amigáveis
4. **Separação de Responsabilidades**: Validação separada da lógica

**Exemplo**:
```php
// StoreDevolucaoRequest.php
public function rules(): array {
    return [
        'pedido_item_id' => ['required', 'integer', 'exists:pedido_items,id'],
        'quantidade' => ['required', 'integer', 'min:1'],
        'motivo' => ['required', 'string', 'min:10', 'max:1000'],
    ];
}

public function messages(): array {
    return [
        'motivo.required' => 'O motivo da devolução é obrigatório.',
        'motivo.min' => 'O motivo deve ter no mínimo 10 caracteres.',
    ];
}
```

## 🔒 Tratamento de Erros

### Estratégia de Tratamento

1. **Try/Catch em Pontos Críticos**
   - Services: Todas as operações de banco envolvidas em transações
   - Controllers: Captura exceções e retorna respostas padronizadas

2. **Logs Estruturados**
   - Contexto completo em cada log
   - Níveis apropriados (info, warning, error)
   - Facilita debugging em produção

3. **Respostas Padronizadas**
   ```json
   {
     "status": "success|error",
     "message": "Mensagem amigável",
     "data": {...} // ou "error": "Detalhes técnicos"
   }
   ```

4. **Transações de Banco**
   - Garantem consistência
   - Rollback automático em caso de erro
   - Evitam estados inconsistentes

## 🎨 Interface Web

### Decisão: Tailwind CSS via CDN

**Justificativa**:
- Prototipagem rápida
- Sem necessidade de build complexo
- Interface moderna e responsiva
- Fácil de substituir por build local se necessário

### Estrutura de Views

- `layouts/app.blade.php`: Layout base com navegação
- `devolucoes/index.blade.php`: Listagem com filtros
- `devolucoes/show.blade.php`: Detalhes e ações

**Padrões**:
- Mensagens de sucesso/erro via session flash
- Formulários com validação do lado do servidor
- Paginação para listagens grandes

## 📊 Ajuste de Estoque

### Quando Ajustar?

**Decisão**: Ajustar estoque apenas quando status muda para `concluida` E status anterior era `aprovada`.

**Justificativa**:
- Evita ajustar estoque de devoluções recusadas
- Garante que produto foi realmente recebido
- Fluxo: Pendente → Aprovada → Concluída (produto retornou)

**Implementação**:
```php
if ($novoStatus === 'concluida' && $statusAnterior === 'aprovada') {
    $this->processarConclusaoDevolucao($devolucao);
    // Incrementa estoque
}
```

## 🚫 O que NÃO foi implementado (e por quê)

### Autenticação/Autorização

**Não implementado**: Sistema completo de autenticação.

**Motivo**: Não era requisito do desafio. Em produção, seria necessário:
- Laravel Sanctum ou Passport para API
- Middleware de autenticação
- Políticas de autorização (ex: apenas gestores podem aprovar)

### Testes Automatizados

**Não implementado**: Testes unitários e de integração.

**Motivo**: Foco do desafio era arquitetura e organização. Em produção, seria essencial:
- Testes de Services
- Testes de Controllers
- Testes de Jobs
- Testes de integração

### Sistema de Troca

**Não implementado**: Lógica específica para trocas (criar nova saída de estoque do produto de troca).

**Motivo**: O requisito mencionava "se necessário", mas não havia detalhes suficientes. A estrutura está preparada para essa funcionalidade futura.

## 🔮 Melhorias Futuras

1. **Eventos e Listeners**: Substituir dispatch direto do Job por Events
2. **DTOs**: Usar Data Transfer Objects para comunicação entre camadas
3. **Repository Pattern**: Se a lógica de acesso a dados crescer
4. **Cache**: Cachear consultas frequentes (ex: produtos, clientes)
5. **API Versioning**: Preparar para evolução da API
6. **Rate Limiting**: Proteger endpoints públicos
7. **Documentação Swagger**: Documentação automática da API

## 📝 Conclusão

Todas as decisões foram tomadas pensando em:
- **Manutenibilidade**: Código fácil de entender e modificar
- **Testabilidade**: Fácil de testar isoladamente
- **Escalabilidade**: Preparado para crescer
- **Boas Práticas**: Seguindo padrões da comunidade Laravel

O projeto está pronto para evolução e pode ser facilmente estendido com novas funcionalidades.

