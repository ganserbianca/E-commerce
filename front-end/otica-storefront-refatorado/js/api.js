import {API_BASE} from './state.js';
export async function api(path, options={}) {
 const headers={Accept:'application/json',...(options.headers||{})};
 const response=await fetch(`${API_BASE}${path}`,{credentials:'include',...options,headers});
 const raw=await response.text(); let data;
 try{data=raw?JSON.parse(raw):null;}catch{data=raw;}
 if(!response.ok){const message=data?.message||(data?.errors&&Object.values(data.errors).flat().join('\n'))||`A requisição falhou (HTTP ${response.status}).`;const error=new Error(message);error.status=response.status;error.payload=data;throw error;}
 return data;
}
function xsrfToken(){const cookie=document.cookie.split('; ').find(item=>item.startsWith('XSRF-TOKEN='));return cookie?decodeURIComponent(cookie.slice('XSRF-TOKEN='.length)):'';}
async function csrf(){const response=await fetch(`${API_BASE}/sanctum/csrf-cookie`,{credentials:'include'});if(!response.ok)throw new Error(`Não foi possível iniciar a sessão segura (HTTP ${response.status}).`);}
export async function apiWithCsrf(path,options={}){await csrf();return api(path,{...options,headers:{'X-XSRF-TOKEN':xsrfToken(),...(options.body?{'Content-Type':'application/json'}:{}),...(options.headers||{})}});}
