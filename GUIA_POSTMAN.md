# 📬 Guia de Importação - Postman Collection

## 🚀 Como Importar

### Passo 1: Abrir Postman
1. Abra o aplicativo Postman (ou acesse [postman.com](https://www.postman.com))
2. Certifique-se de estar logado na sua conta

### Passo 2: Importar Collection
1. Clique no botão **"Import"** no canto superior esquerdo
2. Selecione a opção **"File"** ou **"Upload Files"**
3. Navegue até a pasta `postman/` do projeto
4. Selecione o arquivo: `Sistema_Devolucoes.postman_collection.json`
5. Clique em **"Import"**

### Passo 3: Configurar Variável de Ambiente
1. Após importar, você verá a collection **"Sistema de Gestão de Devoluções - API"**
2. Clique na collection para expandir
3. Vá na aba **"Variables"**
4. Configure a variável `base_url`:
   - **Initial Value**: `http://localhost:8000`
   - **Current Value**: `http://localhost:8000`

**Ou crie um Environment:**
1. Clique em **"Environments"** no menu lateral
2. Clique em **"+"** para criar novo ambiente
3. Nome: `Sistema Devoluções - Local`
4. Adicione variável:
   - **Variable**: `base_url`
   - **Initial Value**: `http://localhost:8000`
   - **Current Value**: `http://localhost:8000`
5. Salve e selecione este ambiente

## 📋 Endpoints Disponíveis

### 1. Listar Devoluções
- **Method**: GET
- **URL**: `{{base_url}}/api/devolucoes`
- **Query Params**: status, cliente_id, produto_id, per_page

### 2. Criar Devolução
- **Method**: POST
- **URL**: `{{base_url}}/api/devolucoes`
- **Body**: JSON com pedido_item_id, quantidade, motivo, tipo

### 3. Criar Troca
- **Method**: POST
- **URL**: `{{base_url}}/api/devolucoes`
- **Body**: JSON com pedido_item_id, quantidade, motivo, tipo="troca", produto_troca_id, motivo_troca

### 4. Visualizar Devolução
- **Method**: GET
- **URL**: `{{base_url}}/api/devolucoes/{id}`

### 5. Atualizar Status - Aprovar
- **Method**: PUT
- **URL**: `{{base_url}}/api/devolucoes/{id}`
- **Body**: JSON com status="aprovada", observacoes

### 6. Atualizar Status - Recusar
- **Method**: PUT
- **URL**: `{{base_url}}/api/devolucoes/{id}`
- **Body**: JSON com status="recusada", observacoes

### 7. Atualizar Status - Concluir
- **Method**: PUT
- **URL**: `{{base_url}}/api/devolucoes/{id}`
- **Body**: JSON com status="concluida", observacoes

## 📝 Exemplos de Body

### Criar Devolução
```json
{
    "pedido_item_id": 1,
    "quantidade": 2,
    "motivo": "Produto com defeito na tela. A tela apresenta riscos e não liga corretamente após alguns minutos de uso.",
    "tipo": "devolucao"
}
```

### Criar Troca
```json
{
    "pedido_item_id": 1,
    "quantidade": 1,
    "motivo": "Produto não corresponde à descrição. Quero trocar por outro modelo mais adequado às minhas necessidades.",
    "tipo": "troca",
    "produto_troca_id": 2,
    "motivo_troca": "Foi enviado o pedido errado. Era a cor preta e veio azul"
}
```

### Atualizar Status
```json
{
    "status": "aprovada",
    "observacoes": "Devolução aprovada. Cliente deve enviar o produto para o endereço: Rua Exemplo, 123 - São Paulo/SP. CEP: 01234-567. Prazo de 7 dias úteis."
}
```

## ✅ Verificação

Após importar, teste a collection:

1. Certifique-se de que o servidor está rodando:
   ```bash
   php artisan serve
   ```

2. Execute a requisição **"Listar Devoluções"**
   - Deve retornar status 200
   - Deve retornar uma lista de devoluções

3. Execute a requisição **"Criar Devolução"**
   - Deve retornar status 201
   - Deve retornar os dados da devolução criada

## 🔧 Troubleshooting

### Erro: "Could not get response"
- Verifique se o servidor Laravel está rodando
- Verifique se a variável `base_url` está configurada corretamente

### Erro: "404 Not Found"
- Verifique se as rotas estão registradas: `php artisan route:list`
- Verifique se está usando a URL correta (com `/api`)

### Erro: "422 Unprocessable Entity"
- Verifique os dados do body
- Certifique-se de que todos os campos obrigatórios estão preenchidos
- Verifique os tipos de dados (inteiros, strings, etc.)

## 📚 Documentação Completa

Para mais detalhes, consulte:
- `API_DOCUMENTATION.md` - Documentação completa da API
- `README.md` - Documentação geral do projeto
