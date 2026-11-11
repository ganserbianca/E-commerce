import {loadProducts,bindProductEvents} from './products.js';
import {loadCart,bindCartEvents} from './cart.js';
import {getProfile,bindProfileEvents} from './profile.js';
import {bindAuthEvents} from './auth.js';
function bindGlobalEvents(){document.querySelectorAll('[data-auth-open]').forEach(el=>el.addEventListener('click',()=>bootstrap.Modal.getOrCreateInstance(document.querySelector('#authModal')).show()));}
async function init(){bindProductEvents();bindCartEvents();bindProfileEvents();bindAuthEvents();bindGlobalEvents();loadProducts();const user=await getProfile(false);if(user)await loadCart();}
document.addEventListener('DOMContentLoaded',init);
