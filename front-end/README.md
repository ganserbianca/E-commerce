# Lume Ótica — E-commerce

Projeto acadêmico de uma loja virtual de ótica, com **API em Laravel/PHP**, **front-end em HTML, CSS e JavaScript modular** e **MySQL**, executados com Docker Compose.

O sistema contempla catálogo de produtos, filtros, carrinhos de visitante e de cliente autenticado, cadastro e autenticação, endereços, checkout, pedidos, pagamento simulado e operações administrativas.

> **Estado da validação:** os seeders de demonstração foram preparados e tiveram a sintaxe verificada, mas ainda **não foram executados e validados em conjunto** no banco isolado. A documentação abaixo descreve a configuração prevista; o funcionamento completo deve ser confirmado em teste.

## Requisitos

- Git
- Docker com Docker Compose (`docker compose`)
- Portas **8080**, **8001** e **3306** disponíveis, conforme a configuração atual do Compose

## Instalação após clonar

Execute os comandos a partir da raiz do repositório:

### 1. Clonar e acessar o projeto

```bash
git clone https://github.com/ganserbianca/E-commerce.git
cd E-commerce
```

### 2. Configurar o ambiente Laravel

```bash
cp back-end/.env.example back-end/.env
```

Confira as variáveis de conexão no `back-end/.env` de acordo com o `compose.yaml`. No ambiente Docker, o host do banco é `mysql`. Para a conta administrativa de demonstração, configure `ADMIN_EMAIL` e `ADMIN_PASSWORD`.

**Nunca publique o arquivo `.env` ou senhas reais no GitHub.**

### 3. Instalar as dependências PHP

```bash
docker run --rm -v "$PWD/back-end:/app" -w /app composer:2 install --no-interaction
```

### 4. Iniciar os containers

```bash
docker compose up -d --build
docker compose ps
```

### 5. Gerar a chave e criar as tabelas

```bash
docker compose exec backend php artisan key:generate
docker compose exec backend php artisan migrate
```

### 6. Popular o banco de desenvolvimento

```bash
docker compose exec backend php artisan db:seed
```

**Atenção:** execute o seeding em um banco de desenvolvimento descartável. Os seeders podem atualizar registros existentes, inclusive quantidades em estoque. Não execute `migrate:fresh` em um banco com dados que você precisa preservar.

### 7. Acessar a aplicação

- **Loja (proxy):** http://localhost:8080
- **Backend/API:** http://localhost:8001

Se as portas estiverem ocupadas, encerre o outro ambiente Docker ou ajuste o mapeamento de portas antes de iniciar.

## Credenciais de demonstração

As contas abaixo são destinadas **somente ao desenvolvimento local** e dependem da execução bem-sucedida dos seeders.

| Perfil | E-mail | Senha |
|---|---|---|
| Administrador | `admin@otica.local` | `Admin123456` |
| Cliente — Ana Oliveira | `ana@otica.local` | `Cliente123!` |
| Cliente — Bruno Santos | `bruno@otica.local` | `Cliente123!` |
| Cliente — Carla Ferreira | `carla@otica.local` | `Cliente123!` |

A conta administrativa é criada com os valores definidos em `ADMIN_EMAIL` e `ADMIN_PASSWORD` no `back-end/.env`. Os valores da tabela para o administrador são **exemplos**, correspondentes à configuração de desenvolvimento utilizada no projeto; se as variáveis forem alteradas, utilize as credenciais configuradas.

## Dados de demonstração (seeders)

Os seeders foram preparados para atender aos cenários de teste solicitados no projeto:

- **Usuários:** um administrador e três clientes.
- **Categorias:** Óculos de Grau, Óculos de Sol, Armações e Acessórios.
- **Produtos:** 30 produtos distribuídos entre as categorias, com preços e estoques variados.
- **Indisponibilidade:** `Óculos de Sol Sunset` com estoque zero; `Óculos de Sol Exclusive` inativo (`available = false`).
- **Carrinhos:** um carrinho de visitante identificado por token e um carrinho da cliente Ana, ambos com itens.
- **Endereços:** endereços fictícios para os três clientes.
- **Pedidos:** cinco exemplos nos estados `pending`, `paid`, `sent`, `delivered` e `cancelled`.
- **Pagamentos:** registros simulados aprovados para os pedidos pagos, enviados e entregues.

Os estados de pedido utilizados no backend são:

| Status | Significado |
|---|---|
| `pending` | Aguardando pagamento |
| `paid` | Pago |
| `sent` | Enviado |
| `delivered` | Entregue |
| `cancelled` | Cancelado |

**Observação:** a presença de um carrinho de visitante no banco não comprova, por si só, todos os fluxos da API para visitantes. Esse comportamento deve ser verificado nos testes da aplicação.

## Organização do projeto

- `back-end/`: API Laravel, regras de negócio, modelos, migrations e seeders.
- `front-end/`: interface da loja, estilos e scripts JavaScript.
- `compose.yaml`: configuração dos serviços Docker.

### Front-end modular

O antigo `app.js` foi dividido por responsabilidade, sem mudar a estrutura visual:

- `core.js`: estado compartilhado, HTTP/API, CSRF, utilitários, mensagens e tratamento de formulários.
- `catalog.js`: busca, filtros e exibição de produtos.
- `cart.js`: carrinho de visitante, carrinho autenticado e sincronização.
- `orders.js`: checkout, pedidos, pagamento simulado e cancelamento.
- `account.js`: autenticação, perfil, endereços e estado da sessão.
- `admin.js`: preenchimento dos campos administrativos.
- `app.js`: apenas eventos da interface e inicialização.

O `index.html` continua importando apenas `app.js` com `type="module"`. Todos os demais módulos são importados por ele.

O front-end precisa ser servido via HTTP, e não aberto como `file://`, porque utiliza módulos ES. O Nginx deve servir todos os arquivos `.js` da mesma pasta. Para reconstruir apenas a interface e o proxy:

```bash
docker compose up -d --build frontend proxy
```

## Comandos úteis

```bash
# Conferir os serviços
docker compose ps

# Acompanhar os logs do backend
docker compose logs -f backend

# Verificar o estado das migrations
docker compose exec backend php artisan migrate:status

# Parar sem apagar os volumes do banco
docker compose down

# Iniciar novamente
docker compose up -d
```

O Laravel é responsável pela autenticação e pelas permissões administrativas. O checkout utiliza pagamento simulado. Os contratos de resposta da API e as regras de autenticação via Sanctum ainda devem ser conferidos com a aplicação em execução.
