<?php
/**
 * Demo Lab: escenario con retrato y demos que se alternan, tres pasos y el laboratorio en una sala obsidiana.
 * La lista, las pestañas y la escena del laboratorio las arma el JS (lab()).
 *
 * @package TresOrillas
 */

defined( 'ABSPATH' ) || exit;
get_header();
$tor_app = isset( $_GET['app'] ) ? sanitize_key( wp_unslash( $_GET['app'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- selección pública de solo lectura.

$tor_demos = tor_core_activo() ? array_values( array_filter( TOR_Proyectos::datos(), static fn( $p ) => (bool) $p['demo'] ) ) : array();
$tor_ciclo = array();
foreach ( $tor_demos as $tor_p ) {
	if ( $tor_p['video'] && ! $tor_p['mobile'] ) {
		$tor_ciclo[] = array( $tor_p, $tor_p['kind'], add_query_arg( 'app', $tor_p['demo'], tor_url( 'demo-lab' ) ) . '#laboratorio' );
	}
}
?>
<div class="tor-serv tor-lab-pag" style="--o: var(--hielo); --o-txt: var(--cobalto); --o-suave: #DCE6F5;">
	<?php
	get_template_part(
		'parts/escena',
		null,
		array(
			'migas'     => array( array( 'Inicio', home_url( '/' ) ), array( 'Demo Lab' ) ),
			'o'         => 't',
			'tag'       => count( $tor_demos ) . ' plataformas reales · usuario de prueba',
			'eyebrow'   => 'Demo Lab',
			'h1'        => 'No te lo contamos. <em>Entra y navégalo.</em>',
			'intro'     => 'Plataformas que construimos para clientes reales, en producción. Entras con un usuario de prueba y las recorres a tu ritmo, antes de hablar con nosotros.',
			'acciones'  => array(
				array( 'Entrar al laboratorio', '#laboratorio', 'marfil' ),
				array( 'Recorrido guiado por Zoom', '', 'llamar' ),
			),
			'ciclo'     => $tor_ciclo,
			'retrato'   => 'retrato-1.jpg',
			'flotantes' => array( array( count( $tor_demos ) . ' demos', 'web y app móvil' ), array( 'Sin registro', 'usuario de prueba incluido' ), array( '2 min', 'para recorrer cada una' ) ),
			'cinta'     => array_map( static fn( $p ) => $p['name'] . ' · ' . $p['kind'], $tor_demos ),
		)
	);
	?>

	<section class="tor-lab-pasos"><div class="wrap">
		<ol class="tor-pasos">
			<li data-luz><span class="n">01</span><b>Elige una plataforma</b><span>ERP, CRM, marketplace, apps.</span></li>
			<li data-luz><span class="n">02</span><b>Copia el usuario de prueba</b><span>Y abre la plataforma real en otra pestaña.</span></li>
			<li data-luz><span class="n">03</span><b>Recórrela a tu ritmo</b><span>Si te gusta, la adaptamos a tu negocio.</span></li>
		</ol>
	</div></section>

	<section class="tinta grano tor-sec-oscura tor-lab-sala" id="laboratorio">
		<span class="tor-lab-luz" id="labLuz" aria-hidden="true"></span>
		<svg class="onda dibujar" width="1100" height="600" viewBox="0 0 1100 600" style="left:-200px;bottom:-240px;opacity:.5;" aria-hidden="true"><path d="M0 380 C 200 60, 420 60, 500 260 S 820 520, 1100 160" fill="none" stroke="#C9B38A" stroke-width="1.2"/></svg>
		<div class="wrap lab" id="labWrap" data-app="<?php echo esc_attr( $tor_app ); ?>" style="position:relative;">
			<div class="lab-lado">
				<p class="etq">El laboratorio</p>
				<div class="lab-lista" id="labLista" role="tablist" aria-label="Plataformas"></div>
				<div class="lab-humano">
					<img src="<?php echo esc_url( tor_m( 'estudio/fundador.webp' ) ); ?>" alt="" loading="lazy">
					<div><b>¿Te la mostramos?</b><span>Un asesor la recorre contigo por Zoom.</span><button class="btn btn-marfil btn-sm" type="button" data-llamar>Agendar recorrido</button></div>
				</div>
			</div>
			<div class="lab-escena">
				<div class="lab-top"><div style="display:flex;flex-direction:column;gap:6px;"><span class="tag" id="lTag"></span><h2 class="h-m" id="lNombre"></h2></div><div class="tabs" role="tablist" aria-label="Vista"><button role="tab" data-tab="demo" aria-selected="true">Demo en vivo</button><button role="tab" data-tab="video" aria-selected="false">Video</button><button role="tab" data-tab="ficha" aria-selected="false">Ficha</button></div></div>
				<div class="lab-marco"><div id="labEscena"></div></div>
				<div class="cred cristal-oscuro" id="labCred"></div>
			</div>
		</div>
	</section>

	<section><div class="wrap">
		<?php get_template_part( 'parts/cartel-contacto' ); ?>
	</div></section>
</div>
<?php
get_footer();
