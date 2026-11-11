import {
    $, state, api, money, escapeHtml,
    notify, errorMessage
} from './core.js';

import { image } from './catalog.js';

export function saveGuest() {
    localStorage.setItem('lume_guest_cart', JSON.stringify(state.cart));
    renderCart();
}

export async function refreshCart() {
    if (state.user) {
        const cart = await api('/api/carrinho');
        state.serverCart = cart;
    } else {
        state.serverCart = null;
    }

    renderCart();
}

export function cartItems() {
    if (state.user) {
        return (state.serverCart?.items || []).map((item) => ({
            product: item.product,
            quantity: item.quantity
        }));
    }

    return Object.entries(state.cart)
        .map(([id, quantity]) => ({
            product: state.products.find(
                (product) => String(product.id) === id
            ),
            quantity
        }))
        .filter((item) => item.product);
}

export function renderCart() {
    const items = cartItems();

    const total = items.reduce(
        (sum, item) => sum + Number(item.product.price) * item.quantity,
        0
    );

    const quantity = items.reduce(
        (sum, item) => sum + item.quantity,
        0
    );

    $('#cart-count').textContent = quantity;

    if (!items.length) {
        $('#cart-content').innerHTML = `
            <div class="empty-state">
                Seu carrinho está vazio.
                <a href="#produtos">Ver produtos</a>
            </div>
        `;
        return;
    }

    $('#cart-content').innerHTML = `
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card panel p-3">
                    ${items.map((item) => `
                        <div class="cart-row">
                            <div style="width: 75px">
                                ${image(item.product)}
                            </div>
                            <div class="flex-grow-1">
                                <strong>${escapeHtml(item.product.name)}</strong>
                                <div class="muted">${money(item.product.price)}</div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <button type="button" class="btn btn-sm btn-outline-dark" data-qty="${item.product.id}" data-delta="-1">−</button>
                                <span>${item.quantity}</span>
                                <button type="button" class="btn btn-sm btn-outline-dark" data-qty="${item.product.id}" data-delta="1">+</button>
                                <button type="button" class="btn btn-sm btn-link text-danger" data-remove="${item.product.id}" aria-label="Remover">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    `).join('')}
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card panel p-4">
                    <h4>Resumo</h4>
                    <div class="d-flex justify-content-between my-3">
                        <span>Total</span>
                        <strong>${money(total)}</strong>
                    </div>
                    <div id="checkout-area"></div>
                </div>
            </div>
        </div>
    `;

    renderCheckout();
}

export async function addProduct(id) {
    const product = state.products.find((item) => item.id === id);

    if (!product || !product.available) return;

    try {
        if (state.user) {
            await api('/api/carrinho/itens', {
                method: 'POST',
                data: {
                    product_id: id,
                    quantity: 1
                }
            });

            await refreshCart();
        } else {
            const current = Number(state.cart[id] || 0);

            if (current >= product.stock_quantity) {
                throw new Error('Quantidade indisponível no estoque.');
            }

            state.cart[id] = current + 1;
            saveGuest();
        }

        notify('Produto adicionado ao carrinho.');
    } catch (error) {
        notify(errorMessage(error), true);
    }
}

export async function setQuantity(id, quantity) {
    try {
        if (state.user) {
            if (quantity <= 0) {
                await api(`/api/carrinho/itens/${id}`, {
                    method: 'DELETE'
                });
            } else {
                await api(`/api/carrinho/itens/${id}`, {
                    method: 'PATCH',
                    data: { quantity }
                });
            }

            await refreshCart();
        } else {
            const product = state.products.find(
                (item) => item.id === id
            );

            if (product && quantity > product.stock_quantity) {
                throw new Error('Quantidade acima do estoque disponível.');
            }

            if (quantity <= 0) {
                delete state.cart[id];
            } else {
                state.cart[id] = quantity;
            }

            saveGuest();
        }
    } catch (error) {
        notify(errorMessage(error), true);
    }
}

export function renderCheckout() {
    const area = $('#checkout-area');

    if (!area) return;

    if (!state.user) {
        area.innerHTML = `
            <p class="small text-secondary">
                Entre na sua conta apenas para finalizar a compra.
            </p>
            <button type="button" class="btn btn-dark w-100" data-open-auth="login">
                Entrar e finalizar
            </button>
        `;
        return;
    }

    area.innerHTML = `
        <label for="checkout-address" class="form-label mt-2">
            Endereço de entrega
        </label>
        <select class="form-select" id="checkout-address">
            <option value="">Selecione o endereço</option>
            ${state.addresses.map((address) => `
                <option value="${address.id}">
                    ${escapeHtml(address.street)}, ${escapeHtml(address.number)} — ${escapeHtml(address.city)}
                </option>
            `).join('')}
        </select>
        <button type="button" class="btn btn-outline-dark w-100 mt-2" id="checkout-new-address">
            + Cadastrar endereço
        </button>
        <button type="button" class="btn btn-dark w-100 mt-3" id="checkout-create">
            Criar pedido
        </button>
        <small class="d-block text-secondary mt-2">
            Depois de criar o pedido, escolha Pix ou cartão para registrar o pagamento simulado.
        </small>
    `;
}

export async function syncGuestCart() {
    const items = Object.entries(state.cart);

    if (!items.length) return;

    let failed = 0;

    for (const [id, quantity] of items) {
        try {
            await api('/api/carrinho/itens', {
                method: 'POST',
                data: {
                    product_id: Number(id),
                    quantity: Number(quantity)
                }
            });

            delete state.cart[id];
        } catch {
            failed++;
        }
    }
    saveGuest();
    if (failed) {
        notify(
            `${failed} item(ns) não puderam ser enviados ao carrinho. Confira o estoque.`,
            true
        );
    }
}
