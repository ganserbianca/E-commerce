
import {
    $, $$, state, api, escapeHtml,
    notify, errorMessage, authModal
} from './core.js';

import { renderCart, refreshCart } from './cart.js';
import { loadOrders } from './orders.js';

// CARREGAR ENDEREÇOS
export async function loadAddresses() {
    if (!state.user) return;

    try {
        state.addresses = await api('/api/enderecos');
        renderAddresses();
        renderCart();
    } catch (error) {
        notify(errorMessage(error), true);
    }
}

// EXIBIR ENDEREÇOS
export function renderAddresses() {
    $('#addresses-list').innerHTML = state.addresses.length
        ? state.addresses.map((address) => `
            <div class="border rounded p-3 mb-2">
                <strong>
                    ${escapeHtml(address.street)}, ${escapeHtml(address.number)}
                </strong>
                <div class="small muted">
                    ${escapeHtml(address.neighborhood)} ·
                    ${escapeHtml(address.city)}/${escapeHtml(address.uf)} ·
                    ${escapeHtml(address.cep)}
                </div>
                <button
                    type="button"
                    class="btn btn-sm btn-link text-danger p-0 mt-2"
                    data-address-delete="${address.id}"
                >
                    Excluir
                </button>
            </div>
        `).join('')
        : '<p class="text-secondary">Nenhum endereço cadastrado.</p>';
}

// ABRIR LOGIN OU CADASTRO
export function openAuth(mode = 'login') {
    state.authMode = mode;

    const isLogin = mode === 'login';

    $('#auth-title').textContent = isLogin ? 'Entrar' : 'Criar conta';
    $('#auth-submit').textContent = isLogin ? 'Entrar' : 'Cadastrar';

    $('#auth-name-field').classList.toggle('d-none', isLogin);
    $('#auth-form [name="name"]').required = !isLogin;

    $('#auth-toggle').textContent = isLogin
        ? 'Ainda não tem conta? Cadastre-se'
        : 'Já tem conta? Entrar';

    authModal.show();
}

// ATUALIZAR SESSÃO
export async function updateSession() {
    try {
        state.user = await api('/api/perfil');
    } catch {
        state.user = null;
    }

    renderSession();

    if (state.user) {
        await Promise.all([
            loadAddresses(),
            loadOrders()
        ]);

        await refreshCart();
    } else {
        state.addresses = [];
        state.serverCart = null;

        renderCart();

        $('#orders-content').innerHTML = `
            <div class="empty-state">
                Entre para consultar seus pedidos.
            </div>
        `;
    }
}

// EXIBIR INTERFACE CONFORME A SESSÃO
export function renderSession() {
    const logged = Boolean(state.user);

    const isAdmin = logged && (
        state.user.type_user === 'admin' ||
        state.user.is_admin === true
    );

    $('#guest-account').classList.toggle('d-none', logged);
    $('#account-content').classList.toggle('d-none', !logged);
    $('#logout-btn').classList.toggle('d-none', !logged);

    $$('[data-auth-only]').forEach((element) => {
        element.classList.toggle('d-none', !logged);
    });

    $$('[data-admin-only]').forEach((element) => {
        element.classList.toggle('d-none', !isAdmin);
    });

    $('#admin').classList.toggle('d-none', !isAdmin);

    if (logged) {
        $('#profile-form [name="name"]').value = state.user.name || '';
        $('#profile-form [name="email"]').value = state.user.email || '';
    }
}
