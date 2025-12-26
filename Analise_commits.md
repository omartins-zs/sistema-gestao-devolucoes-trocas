# Padrão de Análise de Commits

## 1ª Parte — Análise de Commits

Este documento define o **padrão oficial para análise de commits** do projeto.

O objetivo é:
- Analisar **todos os arquivos modificados**
- Identificar corretamente o **tipo de alteração**
- Gerar **mensagens de commit padronizadas**
- Organizar tudo em um único arquivo para revisão antes da aplicação dos commits

---

## Fluxo de Trabalho

1. Analisar todos os arquivos alterados
2. Descrever claramente o que mudou em cada arquivo
3. Classificar a mudança (simples ou complexa)
4. Sugerir o commit adequado seguindo o padrão abaixo
5. Consolidar tudo neste arquivo para validação

---

## Padrão de Commits (iuricode)

Referência oficial:
- https://github.com/iuricode/padroes-de-commits

## Padrões de emojis/Tipos de Commit 💈

<table>
  <thead>
    <tr>
      <th>Tipo do commit</th>
      <th>Emoji</th>
      <th>Palavra-chave</th>
    </tr>
  </thead>
 <tbody>
    <tr>
      <td>Acessibilidade</td>
      <td>♿ <code>:wheelchair:</code></td>
      <td></td>
    </tr>
    <tr>
      <td>Adicionando um teste</td>
      <td>✅ <code>:white_check_mark:</code></td>
      <td><code>test</code></td>
    </tr>
    <tr>
      <td>Atualizando a versão de um submódulo</td>
      <td>⬆️ <code>:arrow_up:</code></td>
      <td></td>
    </tr>
    <tr>
      <td>Retrocedendo a versão de um submódulo</td>
      <td>⬇️ <code>:arrow_down:</code></td>
      <td></td>
    </tr>
    <tr>
      <td>Adicionando uma dependência</td>
      <td>➕ <code>:heavy_plus_sign:</code></td>
      <td><code>build</code></td>
    </tr>
    <tr>
      <td>Alterações de revisão de código</td>
      <td>👌 <code>:ok_hand:</code></td>
      <td><code>style</code></td>
    </tr>
    <tr>
      <td>Animações e transições</td>
      <td>💫 <code>:dizzy:</code></td>
      <td></td>
    </tr>
    <tr>
      <td>Bugfix</td>
      <td>🐛 <code>:bug:</code></td>
      <td><code>fix</code></td>
    </tr>
    <tr>
      <td>Comentários</td>
      <td>💡 <code>:bulb:</code></td>
      <td><code>docs</code></td>
    </tr>
    <tr>
      <td>Commit inicial</td>
      <td>🎉 <code>:tada:</code></td>
      <td><code>init</code></td>
    </tr>
    <tr>
      <td>Configuração</td>
      <td>🔧 <code>:wrench:</code></td>
      <td><code>chore</code></td>
    </tr>
    <tr>
      <td>Deploy</td>
      <td>🚀 <code>:rocket:</code></td>
      <td></td>
    </tr>
    <tr>
      <td>Documentação</td>
      <td>📚 <code>:books:</code></td>
      <td><code>docs</code></td>
    </tr>
    <tr>
      <td>Em progresso</td>
      <td>🚧 <code>:construction:</code></td>
      <td></td>
    </tr>
    <tr>
      <td>Estilização de interface</td>
      <td>💄 <code>:lipstick:</code></td>
      <td><code>feat</code></td>
    </tr>
    <tr>
      <td>Infraestrutura</td>
      <td>🧱 <code>:bricks:</code></td>
      <td><code>ci</code></td>
    </tr>
    <tr>
      <td>Lista de ideias (tasks)</td>
      <td>🔜 <code> :soon: </code></td>
      <td></td>
    </tr>
    <tr>
      <td>Mover/Renomear</td>
      <td>🚚 <code>:truck:</code></td>
      <td><code>chore</code></td>
    </tr>
    <tr>
      <td>Novo recurso</td>
      <td>✨ <code>:sparkles:</code></td>
      <td><code>feat</code></td>
    </tr>
    <tr>
      <td>Package.json em JS</td>
      <td>📦 <code>:package:</code></td>
      <td><code>build</code></td>
    </tr>
    <tr>
      <td>Performance</td>
      <td>⚡ <code>:zap:</code></td>
      <td><code>perf</code></td>
    </tr>
    <tr>
        <td>Refatoração</td>
        <td>♻️ <code>:recycle:</code></td>
        <td><code>refactor</code></td>
    </tr>
    <tr>
      <td>Limpeza de Código</td>
      <td>🧹 <code>:broom:</code></td>
      <td><code>cleanup</code></td>
    </tr>
    <tr>
      <td>Removendo um arquivo</td>
      <td>🗑️ <code>:wastebasket:</code></td>
      <td><code>remove</code></td>
    </tr>
    <tr>
      <td>Removendo uma dependência</td>
      <td>➖ <code>:heavy_minus_sign:</code></td>
      <td><code>build</code></td>
    </tr>
    <tr>
      <td>Responsividade</td>
      <td>📱 <code>:iphone:</code></td>
      <td></td>
    </tr>
    <tr>
      <td>Revertendo mudanças</td>
      <td>💥 <code>:boom:</code></td>
      <td><code>fix</code></td>
    </tr>
    <tr>
      <td>Segurança</td>
      <td>🔒️ <code>:lock:</code></td>
      <td></td>
    </tr>
    <tr>
      <td>SEO</td>
      <td>🔍️ <code>:mag:</code></td>
      <td></td>
    </tr>
    <tr>
      <td>Tag de versão</td>
      <td>🔖 <code>:bookmark:</code></td>
      <td></td>
    </tr>
    <tr>
      <td>Teste de aprovação</td>
      <td>✔️ <code>:heavy_check_mark:</code></td>
      <td><code>test</code></td>
    </tr>
    <tr>
      <td>Testes</td>
      <td>🧪 <code>:test_tube:</code></td>
      <td><code>test</code></td>
    </tr>
    <tr>
      <td>Texto</td>
      <td>📝 <code>:pencil:</code></td>
      <td></td>
    </tr>
    <tr>
      <td>Tipagem</td>
      <td>🏷️ <code>:label:</code></td>
      <td></td>
    </tr>
    <tr>
      <td>Tratamento de erros</td>
      <td>🥅 <code>:goal_net:</code></td>
      <td></td>
    </tr>
    <tr>
      <td>Dados</td>
      <td>🗃️ <code>:card_file_box:</code></td>
      <td><code>raw</code></td>
    </tr>
  </tbody>
</table>

---

## Regras para Mensagens de Commit

- Máximo de **50 caracteres** na mensagem principal
- Usar verbo no infinitivo (Criar, Adicionar, Atualizar, Remover)
- Evitar mensagens genéricas
- Um commit por responsabilidade

Quando houver **muitas alterações relacionadas**, usar:

- **Mensagem curta**
- **Descrição detalhada no corpo do commit**

---

## Exemplos Práticos

### Exemplo 1 — Criação de arquivo

**Arquivo:** `database/seeders/PartidaSeeder.php`

**Análise:**
Criação de seeder responsável por popular a tabela de partidas para ambiente de desenvolvimento e testes.

**Commit sugerido:**

```
🔧 :wrench: Criando seeder de partidas
```

---

### Exemplo 2 — Alteração simples (coluna)

**Arquivo:** `database/migrations/xxxx_add_quadro_numero_partidas.php`

**Análise:**
Adição da coluna `quadro_numero` para controle interno das partidas.

**Commit sugerido:**

```
🗃️ :card_file_box: Add coluna quadro_numero em partidas
```

---

### Exemplo 3 — Criação de Model

**Arquivo:** `app/Models/Atleta.php`

**Análise:**
Criação do model Atleta para representação dos dados de atletas no sistema.

**Commit sugerido:**

```
🗃️ :card_file_box: Criando model de Atleta
```

---

---

## Análise de Commits — Sistema de Gestão de Devoluções e Trocas

### Arquivos Modificados

#### 1. README.md
**Análise:**
Atualização da documentação principal do projeto com informações sobre funcionalidades, instalação e uso do sistema.

**Commit sugerido:**
```
📚 Atualizando documentação do README
```

---

#### 2. bootstrap/app.php
**Análise:**
Configuração do bootstrap da aplicação Laravel com rotas web, API e console.

**Commit sugerido:**
```
🔧 Configurando bootstrap da aplicação
```

---

#### 3. composer.json
**Análise:**
Atualização das dependências do projeto via Composer.

**Commit sugerido:**
```
➕ Atualizando dependências do Composer
```

---

#### 4. composer.lock
**Análise:**
Lock file do Composer com versões exatas das dependências instaladas.

**Commit sugerido:**
```
➕ Atualizando composer.lock
```

---

#### 5. database/seeders/DatabaseSeeder.php
**Análise:**
Atualização do seeder principal para incluir novos seeders do sistema.

**Commit sugerido:**
```
🔧 Atualizando DatabaseSeeder
```

---

#### 6. package-lock.json
**Análise:**
Lock file do NPM com versões exatas das dependências JavaScript.

**Commit sugerido:**
```
📦 Atualizando package-lock.json
```

---

#### 7. package.json
**Análise:**
Atualização das dependências JavaScript do projeto.

**Commit sugerido:**
```
📦 Atualizando dependências NPM
```

---

#### 8. resources/css/app.css
**Análise:**
Atualização dos estilos CSS da aplicação.

**Commit sugerido:**
```
💄 Atualizando estilos CSS
```

---

#### 9. routes/web.php
**Análise:**
Definição das rotas web para devoluções e reembolsos.

**Commit sugerido:**
```
🔧 Configurando rotas web
```

---

### Arquivos Novos — Documentação

#### 10. API_DOCUMENTATION.md
**Análise:**
Documentação completa da API REST do sistema de devoluções e trocas.

**Commit sugerido:**
```
📚 Adicionando documentação da API
```

---

#### 11. Analise_commits.md
**Análise:**
Documento padrão para análise e organização de commits do projeto.

**Commit sugerido:**
```
📚 Criando padrão de análise de commits
```

---

#### 12. CHECKLIST_IMPLEMENTACAO.md
**Análise:**
Checklist de implementação das funcionalidades do sistema.

**Commit sugerido:**
```
📚 Adicionando checklist de implementação
```

---

#### 13. DECISOES_TECNICAS.md
**Análise:**
Documentação das decisões técnicas tomadas durante o desenvolvimento.

**Commit sugerido:**
```
📚 Documentando decisões técnicas
```

---

#### 14. ESTRUTURA_PROJETO.md
**Análise:**
Documentação da estrutura e organização do projeto.

**Commit sugerido:**
```
📚 Documentando estrutura do projeto
```

---

#### 15. GUIA_POSTMAN.md
**Análise:**
Guia de uso da collection Postman para testes da API.

**Commit sugerido:**
```
📚 Adicionando guia do Postman
```

---

#### 16. IMPLEMENTACAO_PEDIDO_REEMBOLSO.md
**Análise:**
Documentação da implementação do sistema de reembolsos.

**Commit sugerido:**
```
📚 Documentando implementação de reembolsos
```

---

#### 17. IMPLEMENTACAO_TROCA.md
**Análise:**
Documentação da implementação do sistema de trocas.

**Commit sugerido:**
```
📚 Documentando implementação de trocas
```

---

#### 18. INSTRUCOES_FINAIS.md
**Análise:**
Instruções finais e orientações para uso do sistema.

**Commit sugerido:**
```
📚 Adicionando instruções finais
```

---

#### 19. RESUMO_FINAL_IMPLEMENTACAO.md
**Análise:**
Resumo final da implementação completa do sistema.

**Commit sugerido:**
```
📚 Adicionando resumo final da implementação
```

---

#### 20. RESUMO_MELHORIAS_FINAIS.md
**Análise:**
Resumo das melhorias finais implementadas no sistema.

**Commit sugerido:**
```
📚 Documentando melhorias finais
```

---

### Arquivos Novos — Controllers

#### 21. app/Http/Controllers/Api/DevolucaoController.php
**Análise:**
Controller da API REST para gerenciamento de devoluções com endpoints de listagem, criação, visualização e atualização de status.

**Commit sugerido:**
```
✨ Criando controller API de devoluções
```

---

#### 22. app/Http/Controllers/Web/DevolucaoController.php
**Análise:**
Controller web para interface de gerenciamento de devoluções com visualização e geração de código de rastreamento.

**Commit sugerido:**
```
✨ Criando controller web de devoluções
```

---

#### 23. app/Http/Controllers/Web/ReembolsoController.php
**Análise:**
Controller web para gerenciamento de reembolsos com autorização e processamento.

**Commit sugerido:**
```
✨ Criando controller web de reembolsos
```

---

### Arquivos Novos — Requests (Validação)

#### 24. app/Http/Requests/AutorizarReembolsoRequest.php
**Análise:**
Form Request para validação de autorização de reembolsos.

**Commit sugerido:**
```
✨ Criando request de autorização de reembolso
```

---

#### 25. app/Http/Requests/ProcessarReembolsoRequest.php
**Análise:**
Form Request para validação de processamento de reembolsos.

**Commit sugerido:**
```
✨ Criando request de processamento de reembolso
```

---

#### 26. app/Http/Requests/StoreDevolucaoRequest.php
**Análise:**
Form Request para validação de criação de devoluções com regras para devolução e troca.

**Commit sugerido:**
```
✨ Criando request de criação de devolução
```

---

#### 27. app/Http/Requests/UpdateDevolucaoStatusRequest.php
**Análise:**
Form Request para validação de atualização de status de devoluções.

**Commit sugerido:**
```
✨ Criando request de atualização de status
```

---

### Arquivos Novos — Jobs (Filas)

#### 28. app/Jobs/EnviarEmailNotificacaoDevolucao.php
**Análise:**
Job assíncrono para envio de e-mails de notificação sobre mudanças de status de devoluções.

**Commit sugerido:**
```
✨ Criando job de notificação de devolução
```

---

#### 29. app/Jobs/EnviarEmailNotificacaoReembolso.php
**Análise:**
Job assíncrono para envio de e-mails de notificação sobre mudanças de status de reembolsos.

**Commit sugerido:**
```
✨ Criando job de notificação de reembolso
```

---

### Arquivos Novos — Models

#### 30. app/Models/Cliente.php
**Análise:**
Model Eloquent para representação de clientes com relacionamentos.

**Commit sugerido:**
```
🗃️ Criando model Cliente
```

---

#### 31. app/Models/Devolucao.php
**Análise:**
Model Eloquent para devoluções com relacionamentos com pedidos, produtos, histórico e reembolsos.

**Commit sugerido:**
```
🗃️ Criando model Devolucao
```

---

#### 32. app/Models/DevolucaoHistorico.php
**Análise:**
Model Eloquent para histórico de alterações de status das devoluções.

**Commit sugerido:**
```
🗃️ Criando model DevolucaoHistorico
```

---

#### 33. app/Models/EstoqueAtual.php
**Análise:**
Model Eloquent para controle de estoque atual dos produtos.

**Commit sugerido:**
```
🗃️ Criando model EstoqueAtual
```

---

#### 34. app/Models/LembreteEmail.php
**Análise:**
Model Eloquent para registro de e-mails de notificação enviados.

**Commit sugerido:**
```
🗃️ Criando model LembreteEmail
```

---

#### 35. app/Models/Pedido.php
**Análise:**
Model Eloquent para pedidos com relacionamentos com clientes, itens e devoluções.

**Commit sugerido:**
```
🗃️ Criando model Pedido
```

---

#### 36. app/Models/PedidoItem.php
**Análise:**
Model Eloquent para itens de pedidos com relacionamentos com pedidos e produtos.

**Commit sugerido:**
```
🗃️ Criando model PedidoItem
```

---

#### 37. app/Models/Produto.php
**Análise:**
Model Eloquent para produtos com relacionamentos com pedidos e devoluções.

**Commit sugerido:**
```
🗃️ Criando model Produto
```

---

#### 38. app/Models/Reembolso.php
**Análise:**
Model Eloquent para reembolsos com relacionamento com devoluções.

**Commit sugerido:**
```
🗃️ Criando model Reembolso
```

---

### Arquivos Novos — Services

#### 39. app/Services/DevolucaoService.php
**Análise:**
Service com lógica de negócio para criação, atualização de status, geração de código de rastreamento e processamento de devoluções.

**Commit sugerido:**
```
✨ Criando service de devoluções
```

---

#### 40. app/Services/EstoqueService.php
**Análise:**
Service para gerenciamento de estoque com incremento, decremento e consulta de quantidade disponível.

**Commit sugerido:**
```
✨ Criando service de estoque
```

---

#### 41. app/Services/ReembolsoService.php
**Análise:**
Service com lógica de negócio para criação, autorização e processamento de reembolsos.

**Commit sugerido:**
```
✨ Criando service de reembolsos
```

---

### Arquivos Novos — Migrations

#### 42. database/migrations/2025_12_26_155716_create_clientes_table.php
**Análise:**
Migration para criação da tabela de clientes.

**Commit sugerido:**
```
🗃️ Criando migration de clientes
```

---

#### 43. database/migrations/2025_12_26_155717_create_produtos_table.php
**Análise:**
Migration para criação da tabela de produtos.

**Commit sugerido:**
```
🗃️ Criando migration de produtos
```

---

#### 44. database/migrations/2025_12_26_155718_create_pedidos_table.php
**Análise:**
Migration para criação da tabela de pedidos.

**Commit sugerido:**
```
🗃️ Criando migration de pedidos
```

---

#### 45. database/migrations/2025_12_26_155719_create_pedido_items_table.php
**Análise:**
Migration para criação da tabela de itens de pedidos.

**Commit sugerido:**
```
🗃️ Criando migration de pedido_items
```

---

#### 46. database/migrations/2025_12_26_155720_create_estoque_atual_table.php
**Análise:**
Migration para criação da tabela de estoque atual.

**Commit sugerido:**
```
🗃️ Criando migration de estoque_atual
```

---

#### 47. database/migrations/2025_12_26_155721_create_devolucoes_table.php
**Análise:**
Migration para criação da tabela de devoluções com campos para devolução e troca.

**Commit sugerido:**
```
🗃️ Criando migration de devoluções
```

---

#### 48. database/migrations/2025_12_26_155722_create_devolucao_historico_table.php
**Análise:**
Migration para criação da tabela de histórico de alterações de devoluções.

**Commit sugerido:**
```
🗃️ Criando migration de devolucao_historico
```

---

#### 49. database/migrations/2025_12_26_155722_create_lembretes_email_table.php
**Análise:**
Migration para criação da tabela de lembretes de e-mail enviados.

**Commit sugerido:**
```
🗃️ Criando migration de lembretes_email
```

---

#### 50. database/migrations/2025_12_26_170748_create_reembolsos_table.php
**Análise:**
Migration para criação da tabela de reembolsos.

**Commit sugerido:**
```
🗃️ Criando migration de reembolsos
```

---

### Arquivos Novos — Seeders

#### 51. database/seeders/ClienteSeeder.php
**Análise:**
Seeder para popular a tabela de clientes com dados de teste.

**Commit sugerido:**
```
🔧 Criando seeder de clientes
```

---

#### 52. database/seeders/DevolucaoSeeder.php
**Análise:**
Seeder para popular a tabela de devoluções com dados de teste.

**Commit sugerido:**
```
🔧 Criando seeder de devoluções
```

---

#### 53. database/seeders/PedidoSeeder.php
**Análise:**
Seeder para popular a tabela de pedidos com dados de teste.

**Commit sugerido:**
```
🔧 Criando seeder de pedidos
```

---

#### 54. database/seeders/ProdutoSeeder.php
**Análise:**
Seeder para popular a tabela de produtos com dados de teste.

**Commit sugerido:**
```
🔧 Criando seeder de produtos
```

---

### Arquivos Novos — Views

#### 55. resources/views/devolucoes/index.blade.php
**Análise:**
View Blade para listagem de devoluções na interface web.

**Commit sugerido:**
```
💄 Criando view de listagem de devoluções
```

---

#### 56. resources/views/devolucoes/show.blade.php
**Análise:**
View Blade para visualização detalhada de uma devolução.

**Commit sugerido:**
```
💄 Criando view de detalhes de devolução
```

---

#### 57. resources/views/layouts/app.blade.php
**Análise:**
Layout principal da aplicação web com estrutura HTML base.

**Commit sugerido:**
```
💄 Criando layout principal da aplicação
```

---

#### 58. resources/views/reembolsos/index.blade.php
**Análise:**
View Blade para listagem de reembolsos na interface web.

**Commit sugerido:**
```
💄 Criando view de listagem de reembolsos
```

---

#### 59. resources/views/reembolsos/show.blade.php
**Análise:**
View Blade para visualização detalhada de um reembolso.

**Commit sugerido:**
```
💄 Criando view de detalhes de reembolso
```

---

### Arquivos Novos — Routes

#### 60. routes/api.php
**Análise:**
Definição das rotas da API REST para devoluções.

**Commit sugerido:**
```
🔧 Configurando rotas da API
```

---

### Arquivos Novos — Postman

#### 61. postman/Sistema_Devolucoes.postman_collection.json
**Análise:**
Collection do Postman com todas as requisições da API para testes.

**Commit sugerido:**
```
📚 Adicionando collection do Postman
```

---

## Observações Finais

- Total de arquivos analisados: **61**
- Arquivos modificados: **9**
- Arquivos novos: **52**
- Um commit será criado para cada arquivo
- Todos os commits seguem o padrão com apenas 1 emoji
- Mensagens limitadas a 50 caracteres quando possível

---

📌 **Este arquivo serve como base oficial para análise e organização dos commits do projeto.**

