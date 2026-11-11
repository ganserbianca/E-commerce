# Ótica Aura — front de apresentação modular

HTML5 + CSS3 + Bootstrap 5 + JavaScript puro (ES Modules). Sem Vue, React, Vite ou npm.

## Estrutura
- `js/app.js`: inicialização e integração dos módulos
- `js/api.js`: fetch, CSRF e comunicação HTTP com Laravel
- `js/auth.js`: login, cadastro e logout
- `js/products.js`: vitrine, classificação por nome, filtros, busca, ordenação e modal
- `js/cart.js`: carrinho, quantidades, remoção e subtotal
- `js/profile.js`: consulta e exibição do perfil
- `js/utils.js`: helpers de DOM, formatação, escape e notificações
- `js/state.js`: estado compartilhado e URL da API

## Executar
Com o backend Laravel ativo na porta 8001:
```bash
python3 -m http.server 5500
```
Abra http://localhost:5500. Mantenha CORS e SANCTUM_STATEFUL_DOMAINS configurados para localhost:5500.

## Filtros
Quando o backend não traz categoria, `products.js` identifica "sol" ou "grau" no nome do produto. Para classificação robusta e permanente, o ideal é incluir categoria no retorno da API.
