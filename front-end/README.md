# Front-end modular da ótica

O antigo `app.js` foi dividido por responsabilidade, sem mudar a estrutura visual.

- `core.js`: estado compartilhado, HTTP/API, CSRF, utilitários, mensagens e tratamento de formulários.
- `catalog.js`: busca, filtros e exibição de produtos.
- `cart.js`: carrinho de visitante, carrinho autenticado e sincronização.
- `orders.js`: checkout, pedidos, pagamento simulado e cancelamento.
- `account.js`: autenticação, perfil, endereços e estado da sessão.
- `admin.js`: preenchimento dos campos administrativos.
- `app.js`: apenas eventos da interface e inicialização.

O `index.html` continua importando apenas `app.js` com `type="module"`. Todos os demais módulos são importados por ele.

## Executar

Sirva o front-end via HTTP, não abrindo `index.html` como `file://` (módulos ES precisam de servidor). Use seu Docker Compose/Nginx existente.

```sh
docker compose up -d --build frontend proxy
```

O Nginx do front-end deve servir todos os arquivos `.js` da mesma pasta.

## Observações

O Laravel continua responsável pela autenticação e pelas permissões administrativas. O checkout usa pagamento simulado. Verifique os contratos de resposta e as regras de Sanctum com a API em execução; o build não foi testado contra o servidor.
