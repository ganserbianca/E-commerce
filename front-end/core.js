
function readGuestCart() {
    try {
        const value = JSON.parse(localStorage.getItem('lume_guest_cart') || '{}');
        return value && typeof value === 'object' && !Array.isArray(value) ? value : {};
    } catch {
        return {};
    }
}

export const $ = (selector) => document.querySelector(selector);
export const $$ = (selector) => [...document.querySelectorAll(selector)];

export const money = (value) => new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL'
}).format(Number(value) || 0);

export const escapeHtml = (value) => String(value ?? '').replace(
    /[&<>"']/g,
    (char) => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#39;'
    })[char]
);

export const state = {
    user: null,
    products: [],
    brands: [],
    audiences: [],
    categories: [],
    addresses: [],
    cart: readGuestCart(),
    serverCart: null,
    orders: [],
    authMode: 'login',
    checkoutPending: false
};

export const authModal = bootstrap.Modal.getOrCreateInstance($('#auth-modal'));
export const addressModal = bootstrap.Modal.getOrCreateInstance($('#address-modal'));

export function notify(message, error = false) {
    $('#toast-text').textContent = message;
    $('#toast').className = `toast ${error ? 'text-bg-danger' : 'text-bg-success'}`;

    bootstrap.Toast.getOrCreateInstance($('#toast'), {
        delay: 4500
    }).show();
}

export function errorMessage(error) {
    return error.message || 'Não foi possível concluir a operação.';
}

// COOKIES
export function cookie(name) {
    const item = document.cookie
        .split('; ')
        .find((value) => value.startsWith(name + '='));

    if (!item) return '';

    try {
        return decodeURIComponent(item.substring(name.length + 1));
    } catch {
        return '';
    }
}

// PROTEÇÃO CSRF
export async function csrf() {
    const response = await fetch('/sanctum/csrf-cookie', {
        method: 'GET',
        credentials: 'same-origin',
        headers: {
            Accept: 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    });

    if (!response.ok) {
        throw new Error('Não foi possível inicializar a proteção CSRF.');
    }

    if (!cookie('XSRF-TOKEN')) {
        throw new Error('O Laravel não forneceu o cookie de proteção CSRF.');
    }
}

// REQUISIÇÕES À API
export async function api(path, { method = 'GET', data } = {}) {
    const requestMethod = method.toUpperCase();
    const needsCsrf = !['GET', 'HEAD', 'OPTIONS'].includes(requestMethod);

    async function sendRequest() {
        const headers = {
            Accept: 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        };

        if (data !== undefined) {
            headers['Content-Type'] = 'application/json';
        }

        if (needsCsrf) {
            const csrfToken = cookie('XSRF-TOKEN');

            if (csrfToken) {
                headers['X-XSRF-TOKEN'] = csrfToken;
            }
        }

        return fetch(path, {
            method: requestMethod,
            credentials: 'same-origin',
            headers,
            body: data === undefined ? undefined : JSON.stringify(data)
        });
    }

    if (needsCsrf && !cookie('XSRF-TOKEN')) {
        await csrf();
    }

    let response = await sendRequest();

    // RENOVAR O TOKEN CASO A SESSÃO CSRF TENHA EXPIRADO
    if (response.status === 419 && needsCsrf) {
        await csrf();
        response = await sendRequest();
    }

    let json = {};

    try {
        json = await response.json();
    } catch {
        // A resposta pode não conter JSON.
    }

    if (!response.ok) {
        const fields = json.errors
            ? Object.values(json.errors).flat().join(' ')
            : '';

        if (response.status === 419) {
            throw new Error(
                'Sua sessão de segurança expirou. Atualize a página e faça login novamente.'
            );
        }

        if (response.status === 401) {
            throw new Error(
                'Sua sessão não está autenticada. Faça login novamente.'
            );
        }

        throw new Error(
            fields || json.message || `Erro HTTP ${response.status}`
        );
    }

    return json.data ?? json;
}

// DADOS DOS FORMULÁRIOS
export const formData = (form) => Object.fromEntries(new FormData(form));

// EXECUÇÃO DOS FORMULÁRIOS
export async function handle(form, callback) {
    const button = form.querySelector(
        'button[type="submit"], button:not([type])'
    );

    try {
        if (button) button.disabled = true;
        await callback();
    } catch (error) {
        notify(errorMessage(error), true);
    } finally {
        if (button) button.disabled = false;
    }
}
