<?php
/**
 * Servicios (/servicios/): índice con un color por orilla y enlace a cada servicio.
 *
 * @package TresOrillas
 */

defined( 'ABSPATH' ) || exit;
get_header();

$tor_servs = tor_lista_servicios();
$tor_aurea = $tor_servs['aurea'] ?? null;
unset( $tor_servs['aurea'] );
$tor_kt  = tor_kses_tarjeta();
$tor_url = static fn( string $slug ): string => isset( $tor_servs[ $slug ] ) ? $tor_servs[ $slug ]['url'] : tor_url( 'servicios' );
?>
<div class="tor-serv tor-indice" style="--o: var(--champan); --o-txt: var(--bronce); --o-suave: #EFE6D3;">

	<?php
	get_template_part(
		'parts/escena',
		null,
		array(
			'migas'     => array( array( 'Inicio', home_url( '/' ) ), array( 'Servicios' ) ),
			'o'         => 'e',
			'tag'       => 'Estrategia · Creatividad · Tecnología',
			'h1'        => 'Cinco servicios,<br><em>un mismo método.</em>',
			'intro'     => 'Funcionan solos o juntos. Todos empiezan con un diagnóstico de una semana y terminan en algo que puedes medir.',
			'acciones'  => array(
				array( 'Encuentra tu servicio', '#encuentra', 'marfil' ),
				array( 'Agenda un diagnóstico', tor_url( 'contacto' ), 'borde' ),
			),
			'retrato'   => 'retrato-2.jpg',
			'flotantes' => array( array( '¿Por dónde empiezo?', 'Responde 3 preguntas y te lo decimos.', '#encuentra' ) ),
			'cinta'     => array_merge( array_column( $tor_servs, 'name' ), $tor_aurea ? array( $tor_aurea['name'] ) : array() ),
		)
	);
	?>

	<div class="tor-rio" aria-hidden="true"><div class="tor-rio-pista" data-rio>Estrategia · <em>Creatividad</em> · Tecnología · Ideas que se convierten <em>en resultados</em> · </div></div>

	<?php if ( function_exists( 'tor_cifras' ) ) : ?>
		<div class="wrap"><div class="tor-cifras">
			<?php foreach ( tor_cifras() as $tor_c ) : ?>
				<div class="tor-cifra"><b><?php echo esc_html( $tor_c[0] ); ?></b><span><?php echo esc_html( $tor_c[1] ); ?></span></div>
			<?php endforeach; ?>
		</div></div>
	<?php endif; ?>

	<section class="tor-guia-sec" id="encuentra"><div class="wrap">
		<div class="tor-guia" data-guia>
			<div class="tor-guia-cara grano">
				<span class="tor-halo" aria-hidden="true"></span>
				<img src="<?php echo esc_url( tor_m( 'estudio/fundador.webp' ) ); ?>" alt="Fundador de 3 Orillas" loading="lazy">
				<p class="tor-guia-burbuja cristal" id="guiaVoz">Respóndeme tres preguntas y te digo <em>por dónde empezar.</em></p>
				<span class="tor-guia-meta"><span class="live"></span>3 preguntas · 20 segundos</span>
			</div>
			<div class="tor-guia-panel">
				<div class="tor-guia-top">
					<p class="etq">Encuentra tu servicio</p>
					<div class="tor-guia-pasos" aria-hidden="true"><i class="on"></i><i></i><i></i></div>
				</div>
				<div class="tor-guia-cuerpo" id="guiaCuerpo" aria-live="polite">
					<?php
					$tor_q = function_exists( 'tor_asistente' ) ? tor_asistente() : array();
					if ( $tor_q ) :
						?>
						<p class="tor-guia-n">Pregunta 1 de <?php echo esc_html( (string) count( $tor_q ) ); ?></p>
						<h2 class="tor-guia-q"><?php tor_e_titular( $tor_q[0]['q'] ); ?></h2>
						<div class="tor-guia-opts">
							<?php foreach ( $tor_q[0]['opts'] as $tor_i => $tor_o ) : ?>
								<button type="button" class="tor-guia-op" data-op="<?php echo esc_attr( (string) $tor_i ); ?>" data-luz><span class="l"><?php echo esc_html( chr( 65 + $tor_i ) ); ?></span><?php echo esc_html( $tor_o[0] ); ?></button>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div></section>

	<section class="tor-catalogo" id="catalogo"><div class="wrap">
		<div class="tor-cabeza">
			<div style="display:flex;flex-direction:column;gap:14px;"><p class="etq">Catálogo</p><h2 class="h-l">O explóralos <em>a tu manera.</em></h2></div>
			<div class="tor-filtros">
				<label class="tor-buscar" for="svcBuscar"><?php tor_e_icono( 'buscar' ); ?><span class="sr">Buscar un servicio</span><input id="svcBuscar" type="search" placeholder="Escribe lo que necesitas: WhatsApp, tienda, ERP…" autocomplete="off" maxlength="60"></label>
				<div class="tor-chips-f" role="group" aria-label="Filtrar por orilla">
					<button type="button" class="chip on" data-f="todo" aria-pressed="true">Todos</button>
					<button type="button" class="chip" data-f="e" aria-pressed="false"><span class="dot o-e"></span>Estrategia</button>
					<button type="button" class="chip" data-f="c" aria-pressed="false"><span class="dot o-c"></span>Creatividad</button>
					<button type="button" class="chip" data-f="t" aria-pressed="false"><span class="dot o-t"></span>Tecnología</button>
				</div>
			</div>
		</div>
		<div class="obras tor-svcs" id="svcGrid">
			<?php
			foreach ( array_merge( $tor_servs, $tor_aurea ? array( 'aurea' => $tor_aurea ) : array() ) as $tor_slug => $tor_s ) :
				$tor_p   = tor_proyecto( $tor_s['ref'] );
				$tor_hay = remove_accents( strtolower( $tor_s['name'] . ' ' . $tor_s['corto'] . ' ' . implode( ' ', $tor_s['chips'] ) . ' ' . ( $tor_s['claves'] ?? '' ) ) );
				?>
				<a class="obra tor-svc" href="<?php echo esc_url( $tor_s['url'] ); ?>" data-cursor="Ver servicio" data-luz data-o="<?php echo esc_attr( $tor_s['o'] ); ?>" data-slug="<?php echo esc_attr( $tor_slug ); ?>" data-buscar="<?php echo esc_attr( $tor_hay ); ?>">
					<?php echo $tor_p ? wp_kses( tor_obra_media( $tor_p ), $tor_kt ) : '<div class="obra-media"></div>'; ?>
					<div class="tor-svc-info">
						<span class="tor-tag"><span class="dot o-<?php echo esc_attr( $tor_s['o'] ); ?>"></span><?php echo esc_html( tor_orilla_etq( $tor_s['o'] ) ); ?></span>
						<h3 class="h-m"><?php echo esc_html( $tor_s['name'] ); ?></h3>
						<p class="body"><?php echo esc_html( $tor_s['corto'] ); ?></p>
						<span class="tor-ir">Ver servicio <?php tor_e_icono( 'ir' ); ?></span>
					</div>
				</a>
			<?php endforeach; ?>
		</div>
		<p class="tor-vacio" id="svcVacio" hidden>No encontramos un servicio con esa palabra. <a href="<?php echo esc_url( tor_wa( 'Hola, necesito ayuda con algo que no encontré en sus servicios.' ) ); ?>" target="_blank" rel="noopener">Cuéntanos qué necesitas por WhatsApp <?php tor_e_icono( 'flecha' ); ?></a></p>
	</div></section>

	<section class="tinta grano tor-sec-oscura">
		<span class="tor-halo" style="inset:auto auto -40% 40%;width:700px;height:500px;opacity:.35;" aria-hidden="true"></span>
		<svg class="onda dibujar" width="900" height="600" viewBox="0 0 900 600" style="left:-260px;bottom:-200px;" aria-hidden="true"><path d="M40 420 C 160 120, 360 100, 420 300 S 640 560, 860 220" fill="none" stroke="#C9B38A" stroke-width="1.2"/><path d="M40 470 C 160 170, 360 150, 420 350 S 640 610, 860 270" fill="none" stroke="#C9B38A" stroke-width="1.2" opacity=".5"/></svg>
		<div class="wrap" style="position:relative;">
			<div class="tor-cabeza">
				<div style="display:flex;flex-direction:column;gap:14px;"><p class="etq">Software y desarrollo</p><h2 class="h-l">Un precio. Un pago.<br><em>Cambios ilimitados.</em></h2></div>
				<div style="display:flex;flex-direction:column;gap:16px;align-items:flex-start;"><p class="body">Cerramos alcance y precio antes de escribir la primera línea de código. El código y los datos son tuyos.</p><a class="btn btn-marfil" href="<?php echo esc_url( $tor_url( 'software' ) ); ?>">Cómo funciona</a></div>
			</div>
			<div class="tor-cartas tor-oscuras" style="--cols: 3;">
				<div class="tor-cifra cristal" data-luz><b>Precio fijo</b><span>Si nos toma más tiempo, lo asumimos nosotros.</span></div>
				<div class="tor-cifra cristal" data-luz><b>Un solo pago</b><span>Sin mensualidades obligatorias ni licencias.</span></div>
				<div class="tor-cifra cristal" data-luz><b>2 meses</b><span>de cambios ilimitados después de la entrega.</span></div>
			</div>
		</div>
	</section>

	<section><div class="wrap">
		<div class="tor-cabeza"><h2 class="h-l">Tres orillas,<br><em>un solo destino.</em></h2><p class="body">Cada orilla es una disciplina. Entra por la que necesitas hoy; el método las conecta.</p></div>
		<div class="orillas">
			<div class="orilla"><span class="glow" style="background: var(--champan);"></span><span class="num">Orilla 01</span><h3>Estrategia</h3><p class="body">Analizamos, estructuramos y trazamos el camino para que tu marca crezca.</p>
				<ul class="tor-enlaces"><li><a href="<?php echo esc_url( $tor_url( 'marketing-digital' ) ); ?>">Marketing digital</a></li><?php if ( $tor_aurea ) : ?><li><a href="<?php echo esc_url( $tor_aurea['url'] ); ?>">Áurea · Wellness</a></li><?php endif; ?></ul></div>
			<div class="orilla oscura"><span class="glow" style="background: #6d7a63;"></span><span class="num" style="color:#9E9A92;">Orilla 02</span><h3><em>Creatividad</em></h3><p class="body">Ideas que conectan con propósito, rendimiento y visión.</p>
				<ul class="tor-enlaces"><li><a href="<?php echo esc_url( $tor_url( 'webs-y-ecommerce' ) ); ?>">Webs y e-commerce</a></li><li><a href="<?php echo esc_url( tor_cat_url( 'marca' ) ); ?>">Diseño y marca</a></li></ul></div>
			<div class="orilla"><span class="glow" style="background: var(--hielo);"></span><span class="num">Orilla 03</span><h3>Tecnología</h3><p class="body">Soluciones a la medida, de la web a la inteligencia artificial.</p>
				<ul class="tor-enlaces"><li><a href="<?php echo esc_url( $tor_url( 'software' ) ); ?>">Software y apps</a></li><li><a href="<?php echo esc_url( $tor_url( 'chatbot-whatsapp' ) ); ?>">Chatbot con IA</a></li><li><a href="<?php echo esc_url( $tor_url( 'automatizacion-ia' ) ); ?>">Automatización</a></li></ul></div>
		</div>
	</div></section>

	<section class="tor-hz tor-suave" data-hz><div class="wrap">
		<div class="tor-cabeza"><h2 class="h-l">Primero entender.<br><em>Después proponer.</em></h2><a href="<?php echo esc_url( tor_url( 'metodo' ) ); ?>" class="btn btn-borde btn-sm">Conocer el método</a></div>
		<div class="tor-hz-ventana"><ol class="tor-hz-pista">
			<?php foreach ( tor_fases() as $tor_i => $tor_f ) : ?>
				<li class="tor-etapa" data-luz><span class="tor-etapa-n"><?php echo esc_html( sprintf( '%02d', $tor_i + 1 ) ); ?></span><span class="etq">Fase <?php echo esc_html( (string) ( $tor_i + 1 ) ); ?></span><h3><?php echo esc_html( $tor_f['t'] ); ?></h3><p><?php echo esc_html( $tor_f['d'] ); ?></p><span class="tor-etapa-t"><?php echo esc_html( $tor_f['time'] ); ?></span></li>
			<?php endforeach; ?>
		</ol></div>
		<div class="tor-hz-barra" aria-hidden="true"><i></i></div>
	</div></section>

	<?php if ( $tor_aurea ) : ?>
		<section style="padding-top: 0;"><div class="wrap">
			<a class="tor-aurea grano" data-luz href="<?php echo esc_url( $tor_aurea['url'] ); ?>" data-cursor="Conocer">
				<span class="tor-halo" aria-hidden="true"></span>
				<div style="display:flex;flex-direction:column;gap:16px;position:relative;">
					<span class="tor-tag"><span class="dot o-c"></span>Línea especializada</span>
					<h2 class="h-l">Áurea, para marcas <em>wellness.</em></h2>
					<p class="body"><?php echo esc_html( $tor_aurea['intro'] ); ?></p>
				</div>
				<div class="tor-aurea-der">
					<ul class="tor-chips"><?php foreach ( array_slice( $tor_aurea['chips'], 0, 6 ) as $tor_c ) { echo '<li>' . esc_html( $tor_c ) . '</li>'; } ?></ul>
					<span class="btn btn-tinta">Conocer Áurea <?php tor_e_icono( 'flecha' ); ?></span>
				</div>
			</a>
		</div></section>
	<?php endif; ?>

	<section class="tor-oficina-sec"><div class="tor-oficina" data-oficina>
		<img src="<?php echo esc_url( tor_m( 'estudio/oficina.webp' ) ); ?>" alt="Oficina de 3 Orillas en Bogotá con el equipo trabajando" loading="lazy">
		<div class="wrap tor-oficina-txt"><p class="etq">Nuestra casa · Bogotá</p><h2 class="h-l">Un solo equipo,<br><em>bajo un mismo techo.</em></h2><p class="body">Estrategia, diseño y desarrollo sentados en la misma mesa, para que nada se pierda entre el qué y el cómo.</p><a class="btn btn-marfil" href="<?php echo esc_url( tor_url( 'estudio' ) ); ?>">Conocer el estudio <?php tor_e_icono( 'flecha' ); ?></a></div>
	</div></section>

	<section style="padding-top: 0;"><div class="wrap">
		<?php get_template_part( 'parts/logos' ); ?>
	</div></section>

	<section style="padding-top: 0;"><div class="wrap">
		<?php get_template_part( 'parts/cartel-contacto' ); ?>
	</div></section>
</div>
<?php
get_footer();
