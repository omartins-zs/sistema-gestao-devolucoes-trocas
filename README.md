# Sistema de Gestão de Devoluções e Trocas em E-commerce

Sistema completo desenvolvido em Laravel para gerenciar devoluções e trocas de produtos em uma loja online, com rastreamento de status, ajuste automático de estoque e notificações por e-mail.

## 📋 Índice

- [Sobre o Projeto](#sobre-o-projeto)
- [Funcionalidades](#funcionalidades)
- [Tecnologias Utilizadas](#tecnologias-utilizadas)
- [Arquitetura](#arquitetura)
- [Instalação](#instalação)
- [Configuração](#configuração)
- [Uso](#uso)
- [API](#api)
- [Decisões Técnicas](#decisões-técnicas)

## 🎯 Sobre o Projeto

Este projeto foi desenvolvido como parte de um teste de programação para avaliar organização de código, arquitetura, clareza, padrões e boas práticas. O sistema permite que uma loja online processe devoluções e trocas de produtos de forma organizada, registrando o motivo, status e conectando devoluções ao pedido original.

### Problema Resolvido

- ✅ Rastreamento automático de devoluções abertas ou finalizadas
- ✅ Atualização imediata do estoque ao registrar uma devolução
- ✅ Visibilidade sobre motivos das devoluções para análises
- ✅ Feedback em tempo real para clientes sobre o status do processo

## 🚀 Funcionalidades

### Principais

- **Cadastro de Entidades**: Clientes, Produtos, Pedidos, Itens de Pedido, Estoque
- **Gestão de Devoluções**: Criação, listagem e atualização de status
- **Fluxo de Status**: Pendente → Aprovada/Recusada → Concluída
- **Ajuste Automático de Estoque**: Incremento automático quando devolução é concluída
- **Histórico Completo**: Registro de todas as alterações de status com timestamp e responsável
- **Notificações por E-mail**: Envio assíncrono de e-mails quando status muda
- **Interface Web**: Painel administrativo para gestão de devoluções
- **API RESTful**: Endpoints JSON para integração

## 🛠 Tecnologias Utilizadas

- **Laravel 12** - Framework PHP
- **PHP 8.2+** - Linguagem de programação
- **MySQL/MariaDB** - Banco de dados
- **Tailwind CSS** - Framework CSS (via CDN)
- **Queue System** - Sistema de filas para processamento assíncrono

## 🏗 Arquitetura

O projeto segue os princípios de **Clean Code** e **SOLID**, com separação clara de responsabilidades:

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Api/          # Controllers para API JSON
│   │   └── Web/           # Controllers para interface web
│   └── Requests/          # Form Requests para validação
├── Models/                # Models Eloquent com relacionamentos
├── Services/              # Lógica de negócio
│   ├── DevolucaoService.php
│   └── EstoqueService.php
└── Jobs/                  # Jobs para processamento assíncrono
    └── EnviarEmailNotificacaoDevolucao.php
```

### Princípios Aplicados

- **Single Responsibility Principle (SRP)**: Cada classe tem uma única responsabilidade
- **Dependency Injection**: Services injetados nos Controllers
- **Separation of Concerns**: Controllers apenas orquestram, Services contêm lógica de negócio
- **Form Requests**: Validação isolada em classes dedicadas

## 📦 Instalação

### Pré-requisitos

- PHP 8.2 ou superior
- Composer
- Node.js e NPM
- MySQL/MariaDB ou SQLite

### Passos

1. **Clone o repositório** (ou baixe o projeto)

2. **Instale as dependências**:
```bash
composer install
npm install
```

3. **Configure o ambiente**:
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configure o banco de dados** no arquivo `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nome_do_banco
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
```

5. **Execute as migrations e seeders**:
```bash
php artisan migrate:fresh --seed
```

6. **Configure o sistema de filas** (opcional, para e-mails):
```env
QUEUE_CONNECTION=database
```

## ⚙️ Configuração

### Configuração de E-mail

Configure as credenciais de e-mail no arquivo `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=seu_usuario
MAIL_PASSWORD=sua_senha
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@exemplo.com
MAIL_FROM_NAME="${APP_NAME}"
```

### Executar o Worker de Filas

Para processar os e-mails em background:

```bash
php artisan queue:work --queue=default
```

Ou use o comando de desenvolvimento que já inclui o worker:

```bash
composer dev
```

## 🎮 Uso

### Acessar a Interface Web

1. Inicie o servidor:
```bash
php artisan serve
```

2. Acesse: `http://localhost:8000`

3. Você será redirecionado para a listagem de devoluções

### Credenciais Padrão

Após executar os seeders, um usuário administrador é criado:

- **E-mail**: `admin@example.com`
- **Senha**: (gerada pelo factory, verifique o seeder)

### Fluxo de Trabalho

1. **Cliente solicita devolução** via API ou interface
2. **Status inicial**: `pendente`
3. **Gestor analisa** e aprova/recusa na interface web
4. **Status atualizado**: `aprovada` ou `recusada`
5. **E-mail enviado** automaticamente ao cliente
6. **Quando concluída**: Estoque é ajustado automaticamente

## 📡 API

### Endpoints Disponíveis

#### Listar Devoluções
```http
GET /api/devolucoes
```

**Query Parameters** (opcionais):
- `status`: Filtrar por status (pendente, aprovada, recusada, concluida)
- `cliente_id`: Filtrar por cliente
- `produto_id`: Filtrar por produto
- `per_page`: Itens por página (padrão: 15)

**Exemplo**:
```bash
curl -X GET "http://localhost:8000/api/devolucoes?status=pendente"
```

**Resposta**:
```json
{
  "status": "success",
  "message": "Devoluções listadas com sucesso",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "cliente_id": 1,
        "produto_id": 1,
        "quantidade": 2,
        "motivo": "Produto com defeito",
        "status": "pendente",
        "data_solicitacao": "2024-12-26T15:00:00.000000Z",
        "cliente": {
          "id": 1,
          "nome": "João Silva",
          "email": "joao.silva@email.com"
        },
        "produto": {
          "id": 1,
          "nome": "Notebook Dell Inspiron 15",
          "sku": "PROD-001"
        }
      }
    ]
  }
}
```

#### Criar Devolução
```http
POST /api/devolucoes
```

**Body**:
```json
{
  "pedido_item_id": 1,
  "quantidade": 2,
  "motivo": "Produto com defeito na tela"
}
```

**Exemplo**:
```bash
curl -X POST "http://localhost:8000/api/devolucoes" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "pedido_item_id": 1,
    "quantidade": 2,
    "motivo": "Produto com defeito na tela"
  }'
```

**Resposta** (201 Created):
```json
{
  "status": "success",
  "message": "Devolução criada com sucesso",
  "data": {
    "id": 1,
    "pedido_item_id": 1,
    "cliente_id": 1,
    "produto_id": 1,
    "quantidade": 2,
    "motivo": "Produto com defeito na tela",
    "status": "pendente",
    "data_solicitacao": "2024-12-26T15:00:00.000000Z"
  }
}
```

#### Visualizar Devolução
```http
GET /api/devolucoes/{id}
```

**Exemplo**:
```bash
curl -X GET "http://localhost:8000/api/devolucoes/1"
```

#### Atualizar Status
```http
PUT /api/devolucoes/{id}
```

**Body**:
```json
{
  "status": "aprovada",
  "observacoes": "Devolução aprovada. Cliente deve enviar o produto."
}
```

**Exemplo**:
```bash
curl -X PUT "http://localhost:8000/api/devolucoes/1" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "status": "aprovada",
    "observacoes": "Devolução aprovada. Cliente deve enviar o produto."
  }'
```

**Status válidos**:
- `pendente`
- `aprovada`
- `recusada`
- `concluida`

## 🧠 Decisões Técnicas

### Por que usar Filas para E-mails?

**Decisão**: Implementei um Job assíncrono (`EnviarEmailNotificacaoDevolucao`) para envio de e-mails.

**Justificativa**:
1. **Performance**: Envio de e-mail pode demorar (chamadas SMTP), não deve bloquear a resposta da API
2. **Resiliência**: Sistema de retry automático em caso de falha (3 tentativas com backoff)
3. **Escalabilidade**: Permite processar e-mails em background, sem impactar requisições HTTP
4. **Melhor UX**: Resposta imediata ao usuário, e-mail enviado em background

**Configuração do Job**:
- `tries: 3` - 3 tentativas em caso de falha
- `timeout: 60` - Timeout de 60 segundos
- `backoff: [30, 60, 120]` - Intervalos crescentes entre tentativas

### Por que Services e não tudo no Controller?

**Decisão**: Toda lógica de negócio está em Services (`DevolucaoService`, `EstoqueService`).

**Justificativa**:
1. **Testabilidade**: Services podem ser testados isoladamente
2. **Reutilização**: Lógica pode ser reutilizada em diferentes contextos (API, Web, Commands)
3. **Manutenibilidade**: Código mais organizado e fácil de manter
4. **SOLID**: Segue o princípio de Single Responsibility

### Por que Form Requests?

**Decisão**: Validação isolada em `StoreDevolucaoRequest` e `UpdateDevolucaoStatusRequest`.

**Justificativa**:
1. **Separação de Responsabilidades**: Validação separada da lógica de negócio
2. **Mensagens Personalizadas**: Mensagens de erro claras e amigáveis
3. **Reutilização**: Mesmas regras podem ser usadas em diferentes contextos
4. **Testabilidade**: Fácil de testar validações isoladamente

### Tratamento de Erros

- **Try/Catch** em todos os pontos críticos
- **Logs estruturados** com contexto completo
- **Respostas padronizadas** (JSON com status, message, data/error)
- **Transações de banco** para garantir consistência

### Estrutura de Banco de Dados

- **Relacionamentos bem definidos** com foreign keys
- **Índices apropriados** para performance
- **Timestamps** em todas as tabelas
- **Enums** para status (garantia de integridade)

## 📝 Comandos Úteis

```bash
# Executar migrations e seeders
php artisan migrate:fresh --seed

# Iniciar servidor de desenvolvimento
php artisan serve

# Processar filas (e-mails)
php artisan queue:work

# Executar tudo junto (servidor + filas + vite)
composer dev

# Limpar cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

## 🧪 Dados de Exemplo

Os seeders criam:
- **5 Clientes** de exemplo
- **6 Produtos** com estoque inicial
- **10 Pedidos** com itens aleatórios
- **5 Devoluções** com diferentes status

## 📄 Licença

Este projeto foi desenvolvido como parte de um teste de programação.

## 👨‍💻 Autor

Desenvolvido seguindo as melhores práticas de Laravel e arquitetura de software.

---

**Nota**: Este é um projeto de demonstração. Para uso em produção, considere adicionar:
- Autenticação e autorização (Laravel Sanctum/Passport)
- Rate limiting
- Validações adicionais de segurança
- Testes automatizados
- Monitoramento e alertas
