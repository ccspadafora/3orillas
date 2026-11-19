<?php
/**
 * Pie, cursor, chat y sala de videollamada.
 *
 * @package TresOrillas
 */

defined( 'ABSPATH' ) || exit;
?>
</div>
</main>

<footer>
	<svg class="onda dibujar" width="1100" height="500" viewBox="0 0 1100 500" style="left:-160px;bottom:-120px;opacity:.35;" aria-hidden="true"><path d="M0 380 C 200 60, 420 60, 500 260 S 820 520, 1100 160" fill="none" stroke="#C9B38A" stroke-width="1.2"/></svg>
	<div class="wrap">
		<div class="pie">
			<div style="display:flex;flex-direction:column;gap:22px;"><img src="<?php echo esc_url( tor_m( 'logo-marfil.png' ) ); ?>" alt="3 Orillas" style="width:200px;height:auto;" loading="lazy"><p style="font:400 30px/1.15 var(--serif);max-width:16ch;">Ideas que se convierten <em>en resultados.</em></p></div>
			<nav aria-label="Servicios"><span class="etq">Servicios</span>
				<?php foreach ( tor_lista_servicios() as $tor_s ) : ?>
					<a href="<?php echo esc_url( $tor_s['url'] ); ?>"><?php echo esc_html( $tor_s['name'] ); ?></a>
				<?php endforeach; ?>
			</nav>
			<nav aria-label="Estudio"><span class="etq">Estudio</span><a href="<?php echo esc_url( tor_url( 'trabajo' ) ); ?>">Trabajo</a><a href="<?php echo esc_url( tor_url( 'demo-lab' ) ); ?>">Demo Lab</a><a href="<?php echo esc_url( tor_url( 'metodo' ) ); ?>">Método</a><a href="<?php echo esc_url( tor_url( 'estudio' ) ); ?>">Nosotros</a></nav>
			<nav aria-label="Contacto"><span class="etq">Contacto</span><a href="<?php echo esc_url( tor_url( 'contacto' ) ); ?>">Escríbenos</a><a href="<?php echo esc_url( tor_wa() ); ?>">WhatsApp <?php echo esc_html( tor_wa_txt() ); ?></a><span>Bogotá · Colombia</span></nav>
		</div>
		<div class="pie-fin"><span>© <?php echo esc_html( wp_date( 'Y' ) ); ?> 3 Orillas · <a href="<?php echo esc_url( class_exists( 'TOR_Ajustes' ) ? TOR_Ajustes::url_politica() : '#' ); ?>" style="color:inherit;">Política de datos</a> · <a href="<?php echo esc_url( home_url( '/terminos-y-condiciones-de-uso/' ) ); ?>" style="color:inherit;">Términos</a> · <a href="<?php echo esc_url( home_url( '/solicitud-de-eliminacion-de-datos/' ) ); ?>" style="color:inherit;">Eliminar mis datos</a></span><span>Estrategia · Marketing · Tecnología · Desarrollo digital</span></div>
	</div>
</footer>

<div class="cursor" id="cursor" aria-hidden="true">Ver</div>
<button class="lanzador cristal" id="lanzador" type="button" data-chat aria-controls="panel"><span class="iso"><img src="<?php echo esc_url( tor_m( 'iso-marfil.png' ) ); ?>" alt="" style="width:24px;height:auto;filter:none;"></span><span><b>Cotiza en vivo</b><small><span class="live" data-live></span><span data-estado="corto">Respuesta por WhatsApp</span></small></span></button>
<div class="panel cristal" id="panel" hidden role="dialog" aria-label="Cotización">
	<div class="panel-cab"><span class="iso"><img src="<?php echo esc_url( tor_m( 'iso-marfil.png' ) ); ?>" alt="" style="width:24px;height:auto;filter:none;"></span><div><b>Cotiza en vivo</b><small><span class="live" data-live></span><span data-estado="largo">Respuesta por WhatsApp</span></small></div><button class="cerrar" id="cerrarChat" aria-label="Cerrar">✕</button></div>
	<div class="msgs" id="msgs" aria-live="polite"></div>
	<form class="panel-pie" id="chatForm"><label for="chatIn" class="sr">Mensaje</label><input class="input" id="chatIn" placeholder="Escribe tu mensaje" autocomplete="off" maxlength="500"><button class="btn btn-tinta btn-sm" type="submit" style="min-height:48px;">Enviar</button></form>
</div>

<div class="sala" id="sala" hidden role="dialog" aria-label="Videollamada con un asesor">
	<div class="sala-caja">
		<div class="sala-top"><span class="live" data-live></span><span id="salaEstado">Agenda tu videollamada</span><span class="zoom">Videollamada por Zoom</span></div>
		<div class="sala-main agenda" id="salaMain"></div>
		<div class="sala-ctrl"><a class="ctrl" id="salaFuera" href="#" target="_blank" rel="noopener" style="display:inline-flex;align-items:center;text-decoration:none;">Abrir en otra pestaña</a><button class="ctrl fin" id="finLlamada" type="button">Cerrar</button></div>
	</div>
</div>

<?php wp_footer(); ?>
</body>
</html>
