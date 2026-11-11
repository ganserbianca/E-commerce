import {
    $, state, api, money, escapeHtml, errorMessage
} from './core.js';

import { renderCart } from './cart.js';
import { renderAdminSelectors } from './admin.js';

export function image(product) {
    const url = String(product.image || '');

    if (/^https?:\/\//i.test(url)) {
        return `
            <img
                src="${escapeHtml(url)}"
                alt="${escapeHtml(product.name)}"
                loading="lazy"
                onerror="this.replaceWith(document.createTextNode('◉'))"
            >
        `;
    }

    return '<i class="bi bi-eyeglasses placeholder-icon"></i>';
}

export async function loadCatalog() {
    try {
        const [products, brands, audiences, categories] = await Promise.all([
            api('/api/produtos'),
            api('/api/marcas'),
            api('/api/publicos'),
            api('/api/categorias')
        ]);

        state.products = Array.isArray(products) ? products : [];
        state.brands = brands;
        state.audiences = audiences;
        state.categories = categories;

        renderFilters();
        renderProducts();
        renderAdminSelectors();
        renderCart();
    } catch (error) {
        $('#products-grid').innerHTML = `
            <div class="col-12">
                <div class="alert alert-warning">
                    ${escapeHtml(errorMessage(error))}.
                    Confira se o Laravel está iniciado.
                </div>
            </div>
        `;
    }
}

export function renderFilters() {
    const selectedBrand = $('#filter-brand').value;
    const selectedAudience = $('#filter-audience').value;

    $('#filter-brand').innerHTML = `
        <option value="">Todas as marcas</option>
        ${state.brands.map((brand) => `
            <option value="${brand.id}">${escapeHtml(brand.name)}</option>
        `).join('')}
    `;

    $('#filter-audience').innerHTML = `
        <option value="">Todos os públicos</option>
        ${state.audiences.map((audience) => `
            <option value="${audience.id}">${escapeHtml(audience.name)}</option>
        `).join('')}
    `;

    $('#filter-brand').value = selectedBrand;
    $('#filter-audience').value = selectedAudience;
}

export function renderProducts() {
    const search = $('#search').value.toLocaleLowerCase('pt-BR');
    const brand = $('#filter-brand').value;
    const audience = $('#filter-audience').value;

    const found = state.products.filter((product) => {
        const text = `${product.name} ${product.description || ''}`
            .toLocaleLowerCase('pt-BR');

        const matchesSearch = text.includes(search);

        const matchesBrand = !brand ||
            String(product.brand?.id) === brand;

        const matchesAudience = !audience ||
            (product.audiences || []).some(
                (item) => String(item.id) === audience
            );

        return matchesSearch && matchesBrand && matchesAudience;
    });

    if (!found.length) {
        $('#products-grid').innerHTML = `
            <div class="col-12">
                <div class="empty-state">
                    Nenhum produto encontrado.
                </div>
            </div>
        `;
        return;
    }

    $('#products-grid').innerHTML = found.map((product) => {
        const available = product.available && product.stock_quantity > 0;

        return `
            <div class="col-sm-6 col-lg-4 col-xl-3">
                <article class="product-card">
                    <div class="product-image">
                        ${image(product)}
                    </div>
                    <div class="product-info">
                        <div class="eyebrow mb-2">
                            ${escapeHtml(product.brand?.name || 'ÓTICA')}
                        </div>
                        <h5>${escapeHtml(product.name)}</h5>
                        <p class="muted small mb-2">
                            ${escapeHtml(
                                (product.description || 'Armação para o seu estilo').slice(0, 95)
                            )}
                        </p>
                        <div class="product-price mb-3">
                            ${money(product.price)}
                        </div>
                        <button
                            type="button"
                            class="btn btn-dark w-100"
                            data-add="${product.id}"
                            ${available ? '' : 'disabled'}
                        >
                            ${available ? 'Adicionar ao carrinho' : 'Indisponível'}
                        </button>
                    </div>
                </article>
            </div>
        `;
    }).join('');
}
