export const $ = (selector, root = document) => root.querySelector(selector);
export const $$ = (selector, root = document) => [...root.querySelectorAll(selector)];
export const money = value => new Intl.NumberFormat('pt-BR', {style:'currency',currency:'BRL'}).format(Number(value || 0));
export const escapeHTML = value => String(value ?? '').replace(/[&<>"']/g, char => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[char]));
export function pick(object, keys, fallback='') {
  for (const key of keys) { const value=object?.[key]; if(value!==null && value!==undefined && value!=='') return value; }
  return fallback;
}
export function unwrap(payload) {
  if(Array.isArray(payload)) return payload;
  if(Array.isArray(payload?.data)) return payload.data;
  if(Array.isArray(payload?.data?.data)) return payload.data.data;
  if(Array.isArray(payload?.items)) return payload.items;
  return [];
}
export function notify(message) {
  $('#toastMessage').textContent=message;
  bootstrap.Toast.getOrCreateInstance($('#appToast'),{delay:2800}).show();
}
export function setCatalogMessage(message,isError=false) {
  $('#catalogStatus').innerHTML=`<span class="status-dot" style="${isError?'background:#b77965':''}"></span>${escapeHTML(message)}`;
}
