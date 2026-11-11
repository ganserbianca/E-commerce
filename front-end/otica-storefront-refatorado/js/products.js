import {state} from './state.js';
import {api} from './api.js';
import {$,$$,pick,unwrap,money,escapeHTML,setCatalogMessage} from './utils.js';
import {addToCart} from './cart.js';

export const productId=p=>pick(p,['id','id_produto','produto_id','product_id']);
export const productName=p=>pick(p,['nome','nome_produto','name','titulo','title'],'Óculos Aura');
export const productPrice=p=>Number(pick(p,['preco','preco_produto','valor','price','preco_venda'],0));
export const productDescription=p=>pick(p,['descricao','descricao_produto','description','detalhes'],'Uma peça selecionada para acompanhar seu estilo e sua rotina.');
export function productImage(p){
 const value=pick(p,['imagem','imagem_produto','url_imagem','image','foto','caminho_imagem','image_url']);
 if(!value)return 'https://images.unsplash.com/photo-1511499767150-a48a237f0083?auto=format&fit=crop&w=850&q=80';
 if(/^https?:\/\//i.test(value))return value;
 return `http://localhost:8001/${String(value).replace(/^\//,'')}`;
}
function normalizeText(value){
 return String(value??'').toLocaleLowerCase('pt-BR').normalize('NFD').replace(/[\u0300-\u036f]/g,'');
}
function productSearchText(p){
 const category=p?.categoria?.nome??p?.category?.name??p?.categoria_nome??p?.categoria??p?.category??p?.tipo??p?.tipo_produto??'';
 return normalizeText(`${category} ${productName(p)} ${productDescription(p)}`);
}
export function productType(p){
 const text=productSearchText(p);
 if(/\bsol\b|oculos de sol/.test(text))return 'sol';
 if(/\bgrau\b|oculos de grau/.test(text))return 'grau';
 return 'coleção';
}
export const productCategoryLabel=p=>productType(p)==='sol'?'ÓCULOS DE SOL':productType(p)==='grau'?'ÓCULOS DE GRAU':String(productType(p)).toUpperCase();

export function renderProducts(){
 const search=$('#productSearch').value.trim().toLocaleLowerCase('pt-BR');
 let products=[...state.products];
 if(state.activeFilter!=='all')products=products.filter(p=>productType(p)===state.activeFilter || productSearchText(p).includes(state.activeFilter));
 if(search)products=products.filter(p=>`${productName(p)} ${productDescription(p)} ${productCategoryLabel(p)}`.toLocaleLowerCase('pt-BR').includes(search));
 const sort=$('#sortProducts').value;
 if(sort==='price-asc')products.sort((a,b)=>productPrice(a)-productPrice(b));
 if(sort==='price-desc')products.sort((a,b)=>productPrice(b)-productPrice(a));
 if(sort==='name')products.sort((a,b)=>productName(a).localeCompare(productName(b),'pt-BR'));
 setCatalogMessage(`${products.length} ${products.length===1?'produto encontrado':'produtos encontrados'}`);
 const grid=$('#productGrid');
 if(!products.length){const msg=state.products.length?'Não encontramos produtos com esses filtros.':'Nossa coleção está sendo preparada.';
 grid.innerHTML=`<div class="col-12"><div class="catalog-empty"><i class="bi bi-eyeglasses"></i><strong>${escapeHTML(msg)}</strong><span>${state.products.length?'Experimente buscar por outro termo ou escolha outra categoria.':'Assim que houver produtos cadastrados, eles aparecerão aqui.'}</span></div></div>`;return;}
 grid.innerHTML=products.map((p,i)=>{const id=productId(p),image=productImage(p),name=productName(p),desc=productDescription(p),label=productCategoryLabel(p),price=productPrice(p);
 return `<div class="col-6 col-lg-3"><article class="product-card"><div class="product-image" data-open-product="${escapeHTML(id)}" role="button" tabindex="0" aria-label="Ver ${escapeHTML(name)}"><img src="${escapeHTML(image)}" alt="${escapeHTML(name)}" loading="lazy" onerror="this.src='https://images.unsplash.com/photo-1511499767150-a48a237f0083?auto=format&fit=crop&w=850&q=80'"><span class="product-badge">${i<2?'SELEÇÃO AURA':'DESIGN AUTÊNTICO'}</span><button class="product-quick" type="button" data-open-product="${escapeHTML(id)}" aria-label="Ver detalhes de ${escapeHTML(name)}"><i class="bi bi-arrow-up-right"></i></button></div><div class="product-details"><div class="product-category">${escapeHTML(label)}</div><h3 class="product-name" title="${escapeHTML(name)}">${escapeHTML(name)}</h3><p class="product-description">${escapeHTML(desc).slice(0,78)}${desc.length>78?'…':''}</p><div class="product-price-row"><strong class="product-price">${money(price)}</strong><button class="product-view" type="button" data-open-product="${escapeHTML(id)}">Detalhes <i class="bi bi-arrow-up-right"></i></button></div></div></article></div>`}).join('');
}
export async function loadProducts(){
 if(state.loadingProducts)return;state.loadingProducts=true;
 $('#productGrid').innerHTML='<div class="col-12 text-center py-5"><div class="spinner-border spinner-aura" role="status"><span class="visually-hidden">Carregando...</span></div></div>';setCatalogMessage('Carregando coleção...');
 try{state.products=unwrap(await api('/api/produtos'));renderProducts();}
 catch(e){state.products=[];$('#productGrid').innerHTML=`<div class="col-12"><div class="catalog-empty"><i class="bi bi-cloud-slash"></i><strong>Não foi possível carregar a coleção.</strong><span>${escapeHTML(e.message)}<br>Confira se o servidor Laravel está ativo e tente novamente.</span><button class="btn btn-aura mt-3" id="retryProducts">Tentar novamente</button></div></div>`;setCatalogMessage('Falha ao conectar com a loja',true);$('#retryProducts')?.addEventListener('click',loadProducts);}
 finally{state.loadingProducts=false;}
}
export function openProduct(id){
 const p=state.products.find(x=>String(productId(x))===String(id));if(!p)return;
 state.selectedProduct=p;state.modalQuantity=1;
 $('#modalProductImage').innerHTML=`<img src="${escapeHTML(productImage(p))}" alt="${escapeHTML(productName(p))}" onerror="this.src='https://images.unsplash.com/photo-1511499767150-a48a237f0083?auto=format&fit=crop&w=850&q=80'">`;
 $('#modalProductCategory').textContent=productCategoryLabel(p);$('#modalProductName').textContent=productName(p);$('#modalProductDescription').textContent=productDescription(p);$('#modalProductPrice').textContent=money(productPrice(p));$('#modalQty').textContent='1';
 bootstrap.Modal.getOrCreateInstance($('#productModal')).show();
}
export function bindProductEvents(){
 $('#openSearch').addEventListener('click',()=>{$('#colecao').scrollIntoView({behavior:'smooth'});setTimeout(()=>$('#productSearch').focus(),450)});
 $('#productSearch').addEventListener('input',renderProducts);$('#sortProducts').addEventListener('change',renderProducts);$('#reloadProducts').addEventListener('click',loadProducts);
 $('#categoryFilters').addEventListener('click',e=>{const b=e.target.closest('[data-filter]');if(!b)return;state.activeFilter=b.dataset.filter;$$('.catalog-tab').forEach(t=>t.classList.toggle('active',t===b));renderProducts();});
 $$('[data-category-link]').forEach(l=>l.addEventListener('click',()=>{const t=l.dataset.categoryLink;state.activeFilter=t;$$('.catalog-tab').forEach(b=>b.classList.toggle('active',b.dataset.filter===t));renderProducts();}));
 $('#productGrid').addEventListener('click',e=>{const t=e.target.closest('[data-open-product]');if(t)openProduct(t.dataset.openProduct)});
 $('#productGrid').addEventListener('keydown',e=>{if((e.key==='Enter'||e.key===' ')&&e.target.matches('[data-open-product]')){e.preventDefault();openProduct(e.target.dataset.openProduct)}});
 $('#modalQtyMinus').addEventListener('click',()=>{state.modalQuantity=Math.max(1,state.modalQuantity-1);$('#modalQty').textContent=state.modalQuantity});
 $('#modalQtyPlus').addEventListener('click',()=>{state.modalQuantity=Math.min(20,state.modalQuantity+1);$('#modalQty').textContent=state.modalQuantity});
 $('#modalAddCart').addEventListener('click',()=>{if(state.selectedProduct)addToCart(state.selectedProduct,state.modalQuantity)});
}
