import { api } from './api.js';
import { $, escapeHTML, notify } from './utils.js';

let addresses = [];

const unwrap = response => response?.data ?? response;

export async function loadProfile() {
    const profile = unwrap(await api('/api/perfil'));
    $('#accountName').textContent = profile.name ?? profile.nome ?? '';
    $('#accountEmail').textContent = profile.email ?? '';
}

export async function updateProfile(data) {
    await api('/api/perfil', {
        method: 'PUT',
        body: JSON.stringify(data)
    });

    await loadProfile();
    notify('Dados atualizados!');
}

export async function updatePassword(data) {
    await api('/api/perfil/senha', {
        method: 'PATCH',
        body: JSON.stringify(data)
    });

    notify('Senha atualizada!');
}

export async function loadAddresses() {
    const response = unwrap(await api('/api/enderecos'));
    addresses = Array.isArray(response) ? response : response.data ?? [];
    return addresses;
}

export async function createAddress(data) {
    await api('/api/enderecos', {
        method: 'POST',
        body: JSON.stringify(data)
    });

    await loadAddresses();
}

export async function updateAddress(id, data) {
    await api(`/api/enderecos/${id}`, {
        method: 'PUT',
        body: JSON.stringify(data)
    });

    await loadAddresses();
}

export async function deleteAddress(id) {
    await api(`/api/enderecos/${id}`, {
        method: 'DELETE'
    });

    await loadAddresses();
}

export function renderAddresses(container, onSelect) {
    container.innerHTML = addresses.map(address => `
        <article class="address-card">
            <strong>${escapeHTML(address.street)}, ${escapeHTML(address.number)}</strong>
            <p>${escapeHTML(address.neighborhood)} - ${escapeHTML(address.city)}/${escapeHTML(address.uf)}</p>
            <p>CEP: ${escapeHTML(address.cep)}</p>
            ${address.complement ? `<p>${escapeHTML(address.complement)}</p>` : ''}
            <button data-address="${address.id}">Selecionar</button>
        </article>
    `).join('');

    container.querySelectorAll('[data-address]').forEach(button => {
        button.addEventListener('click', () => {
            onSelect?.(button.dataset.address);
        });
    });
}