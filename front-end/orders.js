import {
    $, state, api, money, escapeHtml,
    notify, errorMessage, addressModal
} from './core.js';

import { refreshCart } from './cart.js';

export async function createOrder() {
    const addressId = Number($('#checkout-address')?.value);

    if (!addressId) {
        state.checkoutPending = true;
        addressModal.show();
        notify('Cadastre ou selecione um endereço para continuar.', true);
        return;
    }

    try {
        const order = await api('/api/pedidos', {
            method: 'POST',
            data: {
                address_id: addressId
            }
        });

        notify(`Pedido #${order.id} criado!`);

        await refreshCart();
        await loadOrders();

        location.hash = '#pedidos';
        showPayDialog(order.id);
    } catch (error) {
        notify(errorMessage(error), true);
    }
}

export function showPayDialog(id) {
    const method = window.prompt(
        `Pedido #${id} criado. Digite 1 para Pix ou 2 para cartão de crédito. ` +
        'Cancelar deixa o pedido pendente.'
    );

    if (method === '1' || method === '2') {
        payOrder(id, method === '1' ? 'pix' : 'credit_card');
    }
}

export async function payOrder(id, method) {
    try {
        await api(`/api/pedidos/${id}/pay`, {
            method: 'POST',
            data: {
                method
            }
        });

        notify('Pagamento simulado aprovado!');
        await loadOrders();
    } catch (error) {
        notify(errorMessage(error), true);
    }
}

// CARREGAR PEDIDOS
export async function loadOrders() {
    if (!state.user) {
        $('#orders-content').innerHTML = `
            <div class="empty-state">
                Entre para consultar seus pedidos.
            </div>
        `;
        return;
    }

    try {
        const list = await api('/api/pedidos');

        state.orders = Array.isArray(list) ? list : [];

        if (!state.orders.length) {
            $('#orders-content').innerHTML = `
                <div class="empty-state">
                    Você ainda não tem pedidos.
                </div>
            `;
            return;
        }

        $('#orders-content').innerHTML = state.orders.map((order) => `
            <div class="card panel p-3 mb-3">
                <div class="d-flex justify-content-between flex-wrap gap-2">
                    <div>
                        <strong>Pedido #${order.id}</strong>
                        <div class="muted small">
                            Status: ${escapeHtml(order.status)}
                        </div>
                    </div>
                    <strong>${money(order.total_amount)}</strong>
                </div>
                <div class="mt-2 small">
                    ${(order.items || []).map((item) => `
                        ${escapeHtml(item.product_name || item.name || 'Item')}
                        × ${item.quantity}
                    `).join(' · ')}
                </div>
                <div class="mt-3 d-flex gap-2">
                    ${order.status === 'pending' ? `
                        <button type="button" class="btn btn-sm btn-dark" data-pay="${order.id}">
                            Pagar
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger" data-cancel="${order.id}">
                            Cancelar
                        </button>
                    ` : ''}
                </div>
            </div>
        `).join('');
    } catch (error) {
        notify(errorMessage(error), true);
    }
}
