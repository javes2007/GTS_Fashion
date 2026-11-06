<?php if(!empty($_SESSION['administrador_id'])):?></div><?php else:?>
<footer class="pie"><div class="pie-contenido"><section id="acerca"><h2 class="pie-marca">GTS Fashion</h2><p class="pie-eslogan">Tu estilo, tu esencia.</p></section><section id="contacto"><h2>Contacto</h2><p>Conoce nuestra colección y encuentra tu estilo.</p></section><span class="pie-copyright">© 2026 GTS Fashion. Todos los derechos reservados.</span></div></footer><?php endif;?>
<script>
document.addEventListener('click',function(event){
 const boton=event.target.closest('[data-toggle-password]');if(!boton)return;
 const campo=document.getElementById(boton.getAttribute('data-toggle-password'));if(!campo)return;
 const visible=campo.type==='password';campo.type=visible?'text':'password';
 boton.setAttribute('aria-pressed',visible?'true':'false');boton.setAttribute('aria-label',visible?'Ocultar contraseña':'Mostrar contraseña');
});
(function(){const alerta=document.getElementById('admin-alerta');if(!alerta)return;let ultimo=Number(alerta.dataset.ultimo||0);const contador=document.getElementById('admin-alerta-num');async function revisar(){try{const r=await fetch('admin_notificaciones.php?after='+ultimo,{cache:'no-store',credentials:'same-origin'});if(!r.ok)return;const d=await r.json();if(contador)contador.textContent=d.pendientes;if(Array.isArray(d.nuevos))d.nuevos.forEach(p=>{ultimo=Math.max(ultimo,Number(p.id));const box=document.createElement('div');box.className='notificacion-compra';const title=document.createElement('strong');title.textContent='Nueva compra recibida';const info=document.createElement('span');info.textContent='Pedido #'+Number(p.id)+' · '+p.cliente;const link=document.createElement('a');link.href='pedidos.php';link.textContent=' Revisar pedido';box.append(title,info,link);document.body.appendChild(box);setTimeout(()=>box.remove(),9000);});if(d.ultimo)ultimo=Math.max(ultimo,Number(d.ultimo));}catch(e){}}setInterval(revisar,12000);})();
</script></body></html>
