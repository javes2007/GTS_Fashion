<?php if(!empty($_SESSION['administrador_id'])):?></div><?php else:?>
<footer class="pie"><div class="pie-contenido"><section id="acerca"><h2 class="pie-marca">GTS Fashion</h2><p class="pie-eslogan">Tu estilo, tu esencia.</p></section><section id="contacto"><h2>Contacto</h2><p>Conoce nuestra colección y encuentra tu estilo.</p></section><span class="pie-copyright">© 2026 GTS Fashion. Todos los derechos reservados.</span></div></footer><?php endif;?>
<script>
document.addEventListener('click',function(event){
 const boton=event.target.closest('[data-toggle-password]');if(!boton)return;
 const campo=document.getElementById(boton.getAttribute('data-toggle-password'));if(!campo)return;
 const visible=campo.type==='password';campo.type=visible?'text':'password';
 boton.setAttribute('aria-pressed',visible?'true':'false');boton.setAttribute('aria-label',visible?'Ocultar contraseña':'Mostrar contraseña');
});
</script></body></html>
