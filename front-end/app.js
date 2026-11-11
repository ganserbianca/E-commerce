
import {
    $, $$, state, api, csrf, formData, notify,
    errorMessage, handle, authModal, addressModal
} from './core.js';

import { loadCatalog, renderProducts } from './catalog.js';
import { addProduct, cartItems, setQuantity, refreshCart, syncGuestCart } from './cart.js';
import { createOrder, showPayDialog, loadOrders } from './orders.js';
import { openAuth, updateSession, renderSession, loadAddresses } from './account.js';

$('#search').addEventListener('input', renderProducts);
$('#filter-brand').addEventListener('change', renderProducts);
$('#filter-audience').addEventListener('change', renderProducts);

// EVENTOS DE CLIQUE
document.addEventListener('click', async (e) => {
    const t = e.target.closest(
        '[data-add],[data-qty],[data-remove],[data-open-auth],' +
        '[data-pay],[data-cancel],[data-address-delete],' +
        '#new-address,#checkout-new-address,#checkout-create'
    );

    if (!t) return;

    if (t.dataset.add) {
        await addProduct(Number(t.dataset.add));
    }

    if (t.dataset.qty) {
        const item = cartItems().find(
            (x) => x.product.id === Number(t.dataset.qty)
        );

        if (item) {
            await setQuantity(
                item.product.id,
                item.quantity + Number(t.dataset.delta)
            );
        }
    }

    if (t.dataset.remove) {
        await setQuantity(Number(t.dataset.remove), 0);
    }

    if (t.dataset.openAuth) {
        openAuth(t.dataset.openAuth);
    }

    if (t.id === 'new-address' || t.id === 'checkout-new-address') {
        addressModal.show();
    }

    if (t.id === 'checkout-create') {
        await createOrder();
    }

    if (t.dataset.pay) {
        showPayDialog(Number(t.dataset.pay));
    }

    if (t.dataset.cancel && confirm('Cancelar este pedido?')) {
        try {
            await api(`/api/pedidos/${t.dataset.cancel}`, {
                method: 'DELETE'
            });

            notify('Pedido cancelado.');
            await loadOrders();
        } catch (err) {
            notify(errorMessage(err), true);
        }
    }

    if (t.dataset.addressDelete && confirm('Excluir este endereço?')) {
        try {
            await api(`/api/enderecos/${t.dataset.addressDelete}`, {
                method: 'DELETE'
            });

            await loadAddresses();
            notify('Endereço excluído.');
        } catch (err) {
            notify(errorMessage(err), true);
        }
    }
});

$('#auth-toggle').onclick = () => {
    openAuth(state.authMode === 'login' ? 'register' : 'login');
};

$('#auth-form').addEventListener('submit', (e) => {
    e.preventDefault();

    const form = e.currentTarget;

    handle(form, async () => {
        const data = formData(form);

        await csrf();

        if (state.authMode === 'register') {
            await api('/api/register', {
                method: 'POST',
                data
            });

            notify('Conta criada! Entre com seu e-mail e senha.');
            openAuth('login');
            return;
        }

        await api('/api/login', {
            method: 'POST',
            data: {
                email: data.email,
                password: data.password
            }
        });

        authModal.hide();
        await updateSession();

        if (!state.user) {
            throw new Error(
                'Login realizado, mas a sessão não foi reconhecida. ' +
                'Verifique a configuração de cookies/Sanctum.'
            );
        }

        await syncGuestCart();
        await refreshCart();

        notify('Bem-vindo(a)!');

        if (state.checkoutPending) {
            state.checkoutPending = false;
            location.hash = '#carrinho';
        }
    });
});

$('#logout-btn').onclick = async () => {
    try {
        await api('/api/logout', {
            method: 'POST'
        });

        state.user = null;
        renderSession();

        await updateSession();
        notify('Você saiu da conta.');
    } catch (err) {
        notify(errorMessage(err), true);
    }
};

$('#profile-form').addEventListener('submit', (e) => {
    e.preventDefault();

    const form = e.currentTarget;

    handle(form, async () => {
        await api('/api/perfil', {
            method: 'PUT',
            data: formData(form)
        });

        await updateSession();
        notify('Perfil atualizado.');
    });
});

$('#password-form').addEventListener('submit', (e) => {
    e.preventDefault();

    const form = e.currentTarget;

    handle(form, async () => {
        await api('/api/perfil/senha', {
            method: 'PATCH',
            data: formData(form)
        });

        form.reset();
        notify('Senha alterada.');
    });
});

$('#address-form').addEventListener('submit', (e) => {
    e.preventDefault();

    const form = e.currentTarget;

    handle(form, async () => {
        const data = formData(form);
        data.uf = data.uf.toUpperCase();

        await api('/api/enderecos', {
            method: 'POST',
            data
        });

        addressModal.hide();
        form.reset();

        await loadAddresses();
        notify('Endereço cadastrado.');

        if (state.checkoutPending) {
            state.checkoutPending = false;
            location.hash = '#carrinho';
        }
    });
});

const adminForms = [
    {
        id: 'brand-form',
        path: '/api/marcas',
        transform: (data) => data
    },
    {
        id: 'audience-form',
        path: '/api/publicos',
        transform: (data) => data
    },
    {
        id: 'category-form',
        path: '/api/categorias',
        transform: (data) => data
    },
    {
        id: 'product-form',
        path: '/api/produtos',
        transform: (data) => ({
            ...data,
            category_id: Number(data.category_id),
            brand_id: Number(data.brand_id),
            price: Number(data.price),
            stock_quantity: Number(data.stock_quantity),
            available: $('#product-form [name="available"]').checked,
            audience_ids: $$('#product-audiences input:checked').map(
                (input) => Number(input.value)
            )
        })
    }
];

for (const { id, path, transform } of adminForms) {
    $(`#${id}`).addEventListener('submit', (e) => {
        e.preventDefault();

        const form = e.currentTarget;

        handle(form, async () => {
            const data = transform(formData(form));

            await api(path, {
                method: 'POST',
                data
            });

            form.reset();
            await loadCatalog();

            notify('Cadastro realizado com sucesso.');
        });
    });
}

$('#status-form').addEventListener('submit', (e) => {
    e.preventDefault();

    const form = e.currentTarget;

    handle(form, async () => {
        const data = formData(form);

        await api(`/api/pedidos/${Number(data.id)}/status`, {
            method: 'PATCH',
            data: {
                status: data.status
            }
        });

        notify('Status atualizado.');
        await loadOrders();
    });
});

window.addEventListener('hashchange', () => {
    if (location.hash === '#admin' && !state.user) {
        openAuth();
    }

    if (location.hash === '#pedidos' && state.user) {
        loadOrders();
    }
});

await loadCatalog();
await updateSession();
