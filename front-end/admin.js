import { $, state, escapeHtml } from './core.js';

// SELETORES DO PAINEL ADMINISTRATIVO
export function renderAdminSelectors() {
    // MARCAS
    $('#product-brand').innerHTML =
        '<option value="">Selecione a marca</option>' +
        state.brands.map((brand) => `
            <option value="${brand.id}">
                ${escapeHtml(brand.name)}
            </option>
        `).join('');

    // CATEGORIAS
    $('#product-category').innerHTML =
        '<option value="">Selecione a categoria</option>' +
        state.categories.map((category) => `
            <option value="${category.id}">
                ${escapeHtml(category.name)}
            </option>
        `).join('');

    // PÚBLICOS-ALVO
    $('#product-audiences').innerHTML =
        state.audiences.map((audience) => `
            <label class="form-check-label">
                <input
                    type="checkbox"
                    class="form-check-input me-1"
                    name="audience_ids"
                    value="${audience.id}"
                >
                ${escapeHtml(audience.name)}
            </label>
        `).join('') ||
        '<span class="muted small">Cadastre um público-alvo primeiro.</span>';
}
