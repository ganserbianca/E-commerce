import { state } from './state.js';
import { api, apiWithCsrf } from './api.js';
import { $, pick, money, escapeHTML, notify } from './utils.js';
import {
    productId,
    productName,
    productPrice,
    productImage,
    productCategoryLabel
} from './products.js';

let checkoutNewAddress = false;
let pendingCheckoutOrder = null;

const unwrap = response => response?.data ?? response;

export function cartPayload(payload) {
    return payload?.data ?? payload;
}

export function cartItems(payload) {
    const data = cartPayload(payload);
    return data?.items ?? data?.itens ?? [];
}

export function itemProduct(item) {
    return item.product ?? item.produto ?? item;
}

export function itemProductId(item) {
    const product = itemProduct(item);
    return productId(product) ?? item.product_id ?? item.id;
}

export function itemQuantity(item) {
    return Number(item.quantity ?? item.quantidade ?? 1);
}

export function itemPrice(item) {
    return Number(item.price ?? item.preco ?? productPrice(itemProduct(item)) ?? 0);
}

export function renderCart() {
    const container = $('#cartItems');
    if (!container) return;

    const items = cartItems(state.cart);

    container.innerHTML = items.length
        ? items.map(item => {
            const product = itemProduct(item);
            const id = itemProductId(item);
            const quantity = itemQuantity(item);

            return `
                <article class="cart-item">
                    <img src="${escapeHTML(productImage(product) ?? '')}" alt="">
                    <div class="cart-item-info">
                        <h3>${escapeHTML(productName(product) ?? 'Produto')}</h3>
                        <p>${escapeHTML(productCategoryLabel(product) ?? '')}</p>
                        <strong>${money(itemPrice(item))}</strong>
                        <div class="cart-quantity">
                            <button data-cart-minus="${id}">−</button>
                            <span>${quantity}</span>
                            <button data-cart-plus="${id}">+</button>
                        </div>
                    </div>
                    <button data-cart-remove="${id}">Remover</button>
                </article>
            `;
        }).join('')
        : '<p>Seu carrinho está vazio.</p>';

    const total = items.reduce(
        (sum, item) => sum + itemPrice(item) * itemQuantity(item),
        0
    );

    $('#cartTotal') && ($('#cartTotal').textContent = money(total));
}

export async function loadCart() {
    try {
        state.cart = unwrap(await api('/api/carrinho'));
        renderCart();
    } catch (error) {
        console.error(error);
        notify('Não foi possível carregar o carrinho.');
    }
}

export async function addToCart(id, quantity = 1) {
    await apiWithCsrf('/api/carrinho/itens', {
        method: 'POST',
        body: JSON.stringify({
            product_id: id,
            quantity
        })
    });

    await loadCart();
    notify('Produto adicionado ao carrinho!');
}

export async function changeCartItem(id, quantity) {
    if (quantity <= 0) {
        await apiWithCsrf(`/api/carrinho/itens/${id}`, {
            method: 'DELETE'
        });
    } else {
        await apiWithCsrf(`/api/carrinho/itens/${id}`, {
            method: 'PATCH',
            body: JSON.stringify({ quantity })
        });
    }

    await loadCart();
}

export function bindCartEvents() {
    document.addEventListener('click', async event => {
        const minus = event.target.closest('[data-cart-minus]');
        const plus = event.target.closest('[data-cart-plus]');
        const remove = event.target.closest('[data-cart-remove]');

        try {
            if (minus || plus) {
                const id = (minus ?? plus).dataset.cartMinus
                    ?? (minus ?? plus).dataset.cartPlus;

                const item = cartItems(state.cart)
                    .find(entry => String(itemProductId(entry)) === String(id));

                if (!item) return;

                const quantity = itemQuantity(item) + (plus ? 1 : -1);
                await changeCartItem(id, quantity);
            }

            if (remove) {
                await changeCartItem(remove.dataset.cartRemove, 0);
            }
        } catch (error) {
            console.error(error);
            notify('Não foi possível atualizar o carrinho.');
        }
    });

    $('#checkoutButton')?.addEventListener('click', openCheckout);
    $('#toggleNewAddress')?.addEventListener('click', toggleNewAddress);
    $('#checkoutForm')?.addEventListener('submit', submitCheckout);
}

async function openCheckout() {
    const modal = $('#checkoutModal');
    if (!modal) return;

    modal.hidden = false;
    checkoutNewAddress = false;

    $('#checkoutFeedback').textContent = '';
    $('#checkoutSuccess').hidden = true;
    $('#checkoutForm').hidden = false;

    renderCheckoutItems();
    await loadCheckoutAddresses();
}

function renderCheckoutItems() {
    const container = $('#checkoutItems');
    if (!container) return;

    const items = cartItems(state.cart);

    container.innerHTML = items.map(item => `
        <div class="checkout-item">
            <span>${escapeHTML(productName(itemProduct(item)) ?? 'Produto')} × ${itemQuantity(item)}</span>
            <strong>${money(itemPrice(item) * itemQuantity(item))}</strong>
        </div>
    `).join('');

    const total = items.reduce(
        (sum, item) => sum + itemPrice(item) * itemQuantity(item),
        0
    );

    $('#checkoutTotal').textContent = money(total);
}

async function loadCheckoutAddresses() {
    const container = $('#checkoutAddressList');
    const fields = $('#newAddressFields');

    try {
        const response = unwrap(await api('/api/enderecos'));
        const addresses = Array.isArray(response)
            ? response
            : response.data ?? [];

        if (!addresses.length) {
            checkoutNewAddress = true;
            fields.hidden = false;
            container.innerHTML = '<p>Cadastre seu primeiro endereço.</p>';
            return;
        }

        container.innerHTML = addresses.map((address, index) => `
            <label class="checkout-address">
                <input
                    type="radio"
                    name="address_id"
                    value="${address.id}"
                    ${index === 0 ? 'checked' : ''}
                >
                <span>
                    <strong>${escapeHTML(address.street)}, ${escapeHTML(address.number)}</strong>
                    <small>
                        ${escapeHTML(address.neighborhood)} -
                        ${escapeHTML(address.city)}/${escapeHTML(address.uf)}
                    </small>
                    <small>CEP: ${escapeHTML(address.cep)}</small>
                </span>
            </label>
        `).join('');

        checkoutNewAddress = false;
        fields.hidden = true;
    } catch (error) {
        console.error(error);
        checkoutNewAddress = true;
        fields.hidden = false;
        container.innerHTML = `
            <p>Não foi possível carregar os endereços.
            Você pode cadastrar um novo abaixo.</p>
        `;
    }
}

function toggleNewAddress() {
    checkoutNewAddress = !checkoutNewAddress;
    $('#newAddressFields').hidden = !checkoutNewAddress;

    if (checkoutNewAddress) {
        document.querySelectorAll('[name="address_id"]')
            .forEach(input => input.checked = false);
    }
}

async function submitCheckout(event) {
    event.preventDefault();

    const feedback = $('#checkoutFeedback');
    const button = $('#placeOrderButton');

    feedback.textContent = '';
    button.disabled = true;

    try {
        let addressId;

        if (checkoutNewAddress) {
            const form = $('#checkoutForm');
            const data = Object.fromEntries(new FormData(form));

            const address = await apiWithCsrf('/api/enderecos', {
                method: 'POST',
                body: JSON.stringify({
                    cep: data.cep,
                    street: data.street,
                    number: data.number,
                    complement: data.complement || null,
                    neighborhood: data.neighborhood,
                    city: data.city,
                    uf: data.uf
                })
            });

            addressId = unwrap(address).id;
        } else {
            addressId = document.querySelector(
                '[name="address_id"]:checked'
            )?.value;

            if (!addressId) {
                throw new Error('Selecione um endereço.');
            }
        }

        const order = unwrap(await apiWithCsrf('/api/pedidos', {
            method: 'POST',
            body: JSON.stringify({ address_id: addressId })
        }));

        pendingCheckoutOrder = order;

        const method = document.querySelector(
            '[name="paymentMethod"]:checked'
        )?.value;

        await apiWithCsrf(`/api/pedidos/${order.id}/pay`, {
            method: 'POST',
            body: JSON.stringify({ method })
        });

        $('#checkoutForm').hidden = true;
        $('#checkoutSuccess').hidden = false;
        $('#successOrderId').textContent = order.id;
        $('#successOrderTotal').textContent = money(order.total_amount);
        $('#successPaymentMethod').textContent =
            method === 'pix' ? 'PIX' : 'Cartão de crédito';

        await loadCart();
    } catch (error) {
        console.error(error);
        feedback.textContent =
            error.message || 'Não foi possível finalizar o pedido.';
    } finally {
        button.disabled = false;
    }
}