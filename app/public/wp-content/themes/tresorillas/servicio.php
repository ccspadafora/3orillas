<?php
/**
 * Página de un servicio (/servicios/<slug>/) o de Áurea (/aurea/).
 * El contenido viene de tor_servicios() en el plugin; esta plantilla solo lo pinta.
 * Ritmo: escenario oscuro → río de texto → bloques que alternan claro y obsidiana → contacto.
 *
 * @package TresOrillas
 */

defined( 'ABSPATH' ) || exit;

$tor_s = tor_servicio_actual();
if ( ! $tor_s ) {
	require get_template_directory() . '/page.php';
	return;
}

get_header();

$tor_colores = array(
	'e' => array( 'var(--champan)', 'var(--bronce)', '#EFE6D3' ),
	'c' => array( 'var(--salvia)', 'var(--oliva)', '#DFE8DA' ),
	't' => array( 'var(--hielo)', 'var(--cobalto)', '#DCE6F5' ),
);
$tor_col   = $tor_colores[ $tor_s['o'] ] ?? $tor_colores['e'];
$tor_todos = tor_core_activo() ? TOR_Proyectos::datos() : array();
$tor_kt    = tor_kses_tarjeta();
$tor_wa    = tor_wa( $tor_s['wa'] ?? '' );

/**
 * Encabezado de bloque: etiqueta, titular y bajada.
 */
$tor_cabeza = static function ( array $b, string $extra = '' ): void {
	?>
	<div class="tor-cabeza">
		<div style="display:flex;flex-direction:column;gap:14px;">
			<?php if ( ! empty( $b['etq'] ) ) : ?><p class="etq"><?php echo esc_html( $b['etq'] ); ?></p><?php endif; ?>
			<?php if ( ! empty( $b['h2'] ) ) : ?><h2 class="h-l"><?php tor_e_titular( $b['h2'] ); ?></h2><?php endif; ?>
		</div>
		<?php if ( ! empty( $b['intro'] ) || $extra ) : ?>
			<div style="display:flex;flex-direction:column;gap:16px;align-items:flex-start;">
				<?php if ( ! empty( $b['intro'] ) ) : ?><p class="body"><?php echo esc_html( $b['intro'] ); ?></p><?php endif; ?>
				<?php echo $extra; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- botones armados y escapados en esta plantilla. ?>
			</div>
		<?php endif; ?>
	</div>
	<?php
};

$tor_onda      = '<svg class="onda dibujar" width="900" height="600" viewBox="0 0 900 600" style="right:-220px;top:-60px;" aria-hidden="true"><path d="M40 420 C 160 120, 360 100, 420 300 S 640 560, 860 220" fill="none" stroke="#C9B38A" stroke-width="1.2"/><path d="M40 470 C 160 170, 360 150, 420 350 S 640 610, 860 270" fill="none" stroke="#C9B38A" stroke-width="1.2" opacity=".5"/></svg>';
$tor_kses_onda = array(
	'svg'  => array( 'class' => true, 'width' => true, 'height' => true, 'viewbox' => true, 'style' => true, 'aria-hidden' => true ),
	'path' => array( 'd' => true, 'fill' => true, 'stroke' => true, 'stroke-width' => true, 'opacity' => true ),
);

$tor_migas = array( array( 'Inicio', home_url( '/' ) ) );
if ( $tor_s['padre'] ) {
	$tor_migas[] = array( 'Servicios', tor_url( $tor_s['padre'] ) );
}
$tor_migas[] = array( $tor_s['name'] );
?>
<div class="tor-serv" style="--o: <?php echo esc_attr( $tor_col[0] ); ?>; --o-txt: <?php echo esc_attr( $tor_col[1] ); ?>; --o-suave: <?php echo esc_attr( $tor_col[2] ); ?>;">

	<?php
	get_template_part(
		'parts/escena',
		null,
		array(
			'migas'     => $tor_migas,
			'o'         => $tor_s['o'],
			'tag'       => tor_orilla_etq( $tor_s['o'] ),
			'eyebrow'   => $tor_s['eyebrow'],
			'h1'        => $tor_s['h1'],
			'intro'     => $tor_s['intro'],
			'acciones'  => array(
				array( 'Cotizar por WhatsApp', $tor_wa, 'marfil', true ),
				array( 'Hablemos por Zoom', '', 'llamar' ),
			),
			'vivo'      => true,
			'proyecto'  => tor_proyecto( $tor_s['ref'] ),
			'retrato'   => $tor_s['retrato'] ?? '',
			'flotantes' => $tor_s['flotantes'] ?? array(),
			'cinta'     => $tor_s['chips'],
		)
	);
	?>

	<div class="tor-rio" aria-hidden="true"><div class="tor-rio-pista" data-rio><?php echo esc_html( implode( ' · ', $tor_s['chips'] ) . ' · ' ); ?><em><?php echo esc_html( $tor_s['eyebrow'] ); ?></em><?php echo esc_html( ' · ' ); ?></div></div>

	<?php if ( ! empty( $tor_s['cifras'] ) && function_exists( 'tor_cifras' ) ) : ?>
		<div class="wrap"><div class="tor-cifras">
			<?php foreach ( tor_cifras() as $tor_c ) : ?>
				<div class="tor-cifra"><b><?php echo esc_html( $tor_c[0] ); ?></b><span><?php echo esc_html( $tor_c[1] ); ?></span></div>
			<?php endforeach; ?>
		</div></div>
	<?php endif; ?>

	<?php
	foreach ( $tor_s['bloques'] as $tor_bi => $tor_b ) :
		$tor_oscuro = 'oscuro' === ( $tor_b['tono'] ?? '' );
		switch ( $tor_b['tipo'] ) {

			case 'cartas':
				$tor_cols = (int) ( $tor_b['cols'] ?? min( 3, count( $tor_b['items'] ) ) );
				?>
				<section class="<?php echo $tor_oscuro ? 'tinta grano tor-sec-oscura' : ''; ?>">
					<?php echo $tor_oscuro ? wp_kses( $tor_onda, $tor_kses_onda ) : ''; ?>
					<div class="wrap" style="position:relative;">
						<?php $tor_cabeza( $tor_b ); ?>
						<div class="tor-cartas" style="--cols: <?php echo esc_attr( (string) $tor_cols ); ?>;">
							<?php foreach ( $tor_b['items'] as $tor_i => $tor_it ) : ?>
								<article class="orilla tor-carta" data-luz>
									<span class="glow" style="background: var(--o);"></span>
									<span class="tor-carta-n"><?php echo esc_html( trim( ( $tor_b['num'] ?? '' ) . ' ' . sprintf( '%02d', $tor_i + 1 ) ) ); ?></span>
									<h3><?php echo esc_html( $tor_it[0] ); ?></h3>
									<p class="body"><?php echo esc_html( $tor_it[1] ); ?></p>
									<?php if ( ! empty( $tor_it[2] ) ) : ?><ul><?php foreach ( $tor_it[2] as $tor_li ) { echo '<li>' . esc_html( $tor_li ) . '</li>'; } ?></ul><?php endif; ?>
								</article>
							<?php endforeach; ?>
						</div>
					</div>
				</section>
				<?php
				break;

			case 'modelo':
				?>
				<section class="tor-modelo"><div class="wrap">
					<?php $tor_cabeza( $tor_b ); ?>
					<div class="tor-pila">
						<?php foreach ( $tor_b['items'] as $tor_i => $tor_it ) : ?>
							<article class="tor-pila-carta" style="--i: <?php echo esc_attr( (string) $tor_i ); ?>;" data-luz>
								<span class="tor-pila-n"><?php echo esc_html( sprintf( '%02d', $tor_i + 1 ) ); ?></span>
								<h3><?php echo esc_html( $tor_it[0] ); ?></h3>
								<div class="tor-pila-txt"><p class="body"><?php echo esc_html( $tor_it[1] ); ?></p><span class="tor-ok"><?php tor_e_icono( 'check' ); ?><?php echo esc_html( $tor_it[2] ); ?></span></div>
							</article>
						<?php endforeach; ?>
					</div>
					<?php if ( ! empty( $tor_b['comparar'] ) ) : ?>
						<div class="tor-comparar">
							<?php
							$tor_ci = 0;
							foreach ( $tor_b['comparar'] as $tor_t => $tor_l ) :
								$tor_si = 1 === $tor_ci++;
								?>
								<div class="tor-comp<?php echo $tor_si ? ' si grano' : ''; ?>">
									<?php if ( $tor_si ) : ?><span class="tor-halo" aria-hidden="true"></span><?php endif; ?>
									<p class="etq"><?php echo esc_html( $tor_t ); ?></p>
									<ul><?php foreach ( $tor_l as $tor_li ) { echo '<li><span aria-hidden="true">' . ( $tor_si ? '✓' : '✕' ) . '</span>' . esc_html( $tor_li ) . '</li>'; } ?></ul>
								</div>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div></section>
				<?php
				break;

			case 'fases':
				if ( empty( $tor_b['items'] ) ) {
					break;
				}
				$tor_claro = 'claro' === ( $tor_b['tono'] ?? '' );
				?>
				<section class="tor-hz <?php echo $tor_claro ? 'tor-suave' : 'tinta grano tor-sec-oscura'; ?>" data-hz>
					<?php echo $tor_claro ? '' : wp_kses( $tor_onda, $tor_kses_onda ); ?>
					<div class="wrap" style="position:relative;">
						<?php $tor_cabeza( $tor_b, '<a class="btn ' . ( $tor_claro ? 'btn-borde' : 'btn-marfil' ) . ' btn-sm" href="' . esc_url( tor_url( 'metodo' ) ) . '">Conocer el método</a>' ); ?>
						<div class="tor-hz-ventana">
							<ol class="tor-hz-pista">
								<?php foreach ( $tor_b['items'] as $tor_i => $tor_f ) : ?>
									<li class="tor-etapa" data-luz>
										<span class="tor-etapa-n"><?php echo esc_html( sprintf( '%02d', $tor_i + 1 ) ); ?></span>
										<span class="etq">Etapa <?php echo esc_html( sprintf( '%02d', $tor_i + 1 ) ); ?></span>
										<h3><?php echo esc_html( $tor_f[0] ); ?></h3>
										<p><?php echo esc_html( $tor_f[1] ); ?></p>
										<?php if ( '' !== $tor_f[2] ) : ?><span class="tor-etapa-t"><?php echo esc_html( $tor_f[2] ); ?></span><?php endif; ?>
									</li>
								<?php endforeach; ?>
							</ol>
						</div>
						<div class="tor-hz-barra" aria-hidden="true"><i></i></div>
					</div>
				</section>
				<?php
				break;

			case 'lista':
				?>
				<section><div class="wrap">
					<?php $tor_cabeza( $tor_b ); ?>
					<ol class="tor-lista">
						<?php foreach ( $tor_b['items'] as $tor_i => $tor_it ) : ?>
							<li><span class="n"><?php echo esc_html( sprintf( '%02d', $tor_i + 1 ) ); ?></span><h3><?php echo esc_html( $tor_it[0] ); ?></h3><p><?php echo esc_html( $tor_it[1] ); ?></p></li>
						<?php endforeach; ?>
					</ol>
				</div></section>
				<?php
				break;

			case 'tabs':
				$tor_tid = 'tab' . $tor_bi;
				?>
				<section class="tor-suave"><div class="wrap">
					<?php $tor_cabeza( $tor_b ); ?>
					<div class="tor-tabs" data-tabs>
						<div class="tor-tablist" role="tablist" aria-label="<?php echo esc_attr( $tor_b['etq'] ?? 'Áreas' ); ?>">
							<?php
							$tor_ti = 0;
							foreach ( array_keys( $tor_b['items'] ) as $tor_t ) :
								$tor_on = 0 === $tor_ti;
								?>
								<button class="chip" type="button" role="tab" id="<?php echo esc_attr( $tor_tid . '-b' . $tor_ti ); ?>" aria-controls="<?php echo esc_attr( $tor_tid . '-p' . $tor_ti ); ?>" aria-selected="<?php echo $tor_on ? 'true' : 'false'; ?>" tabindex="<?php echo $tor_on ? '0' : '-1'; ?>"><?php echo esc_html( $tor_t ); ?></button>
								<?php
								++$tor_ti;
							endforeach;
							?>
						</div>
						<?php
						$tor_ti = 0;
						foreach ( $tor_b['items'] as $tor_t => $tor_l ) :
							?>
							<div class="tor-panel" role="tabpanel" id="<?php echo esc_attr( $tor_tid . '-p' . $tor_ti ); ?>" aria-labelledby="<?php echo esc_attr( $tor_tid . '-b' . $tor_ti ); ?>"<?php echo 0 === $tor_ti ? '' : ' hidden'; ?>>
								<?php foreach ( $tor_l as $tor_ii => $tor_it ) : ?>
									<div class="tor-item" data-luz><span class="tor-carta-n"><?php echo esc_html( sprintf( '%02d', $tor_ii + 1 ) ); ?></span><h3><?php echo esc_html( $tor_it[0] ); ?></h3><p><?php echo esc_html( $tor_it[1] ); ?></p></div>
								<?php endforeach; ?>
							</div>
							<?php
							++$tor_ti;
						endforeach;
						?>
					</div>
				</div></section>
				<?php
				break;

			case 'chat':
				?>
				<section class="tinta grano tor-sec-oscura">
					<?php echo wp_kses( $tor_onda, $tor_kses_onda ); ?>
					<div class="wrap tor-dos" style="position:relative;align-items:center;">
						<div style="display:flex;flex-direction:column;gap:18px;">
							<p class="etq"><?php echo esc_html( $tor_b['etq'] ); ?></p>
							<h2 class="h-l"><?php tor_e_titular( $tor_b['h2'] ); ?></h2>
							<p class="body"><?php echo esc_html( $tor_b['intro'] ); ?></p>
							<div class="tor-chat-ops" id="chatDemoOps">
								<button class="chip" type="button" data-guion="0">Quiero saber precios</button>
								<button class="chip" type="button" data-guion="1">Tengo un problema con mi pedido</button>
								<button class="chip" type="button" data-guion="2">Quiero agendar una cita</button>
							</div>
						</div>
						<div class="tor-cel" aria-live="polite">
							<div class="tor-cel-top"><span class="tor-avatar" aria-hidden="true">IA</span><span><b>Asistente de tu marca</b><small>Conversación de ejemplo</small></span></div>
							<div class="tor-cel-msgs" id="chatDemo"><div class="tor-burbuja">Hola, soy el asistente de tu marca. ¿En qué te ayudo hoy?</div></div>
						</div>
					</div>
				</section>
				<?php
				break;

			case 'frase':
				?>
				<section class="tor-frase-clara"><div class="wrap tor-dos" style="align-items:end;">
					<div style="display:flex;flex-direction:column;gap:22px;">
						<p class="etq"><?php echo esc_html( $tor_b['etq'] ); ?></p>
						<p class="scrub tor-scrub"><?php tor_e_titular( $tor_b['frase'] ); ?></p>
					</div>
					<div style="display:flex;flex-direction:column;gap:20px;">
						<p class="body"><?php echo esc_html( $tor_b['texto'] ); ?></p>
						<a class="btn btn-tinta tor-iman" href="<?php echo esc_url( $tor_wa ); ?>" target="_blank" rel="noopener" style="align-self:flex-start;">Agenda un diagnóstico <?php tor_e_icono( 'flecha' ); ?></a>
					</div>
				</div></section>
				<?php
				break;

			case 'manifiesto':
				?>
				<section class="tinta grano tor-sec-oscura">
					<?php echo wp_kses( $tor_onda, $tor_kses_onda ); ?>
					<div class="wrap tor-dos" style="position:relative;align-items:end;">
						<div style="display:flex;flex-direction:column;gap:18px;">
							<p class="etq"><?php echo esc_html( $tor_b['etq'] ); ?></p>
							<h2 class="h-l tor-frase-h"><?php tor_e_titular( $tor_b['frase'] ); ?></h2>
						</div>
						<ul class="tor-manifiesto"><?php foreach ( $tor_b['lineas'] as $tor_li ) { echo '<li>' . esc_html( $tor_li ) . '</li>'; } ?></ul>
					</div>
				</section>
				<?php
				break;

			case 'faq':
				?>
				<section><div class="wrap tor-dos">
					<div style="display:flex;flex-direction:column;gap:14px;">
						<p class="etq"><?php echo esc_html( $tor_b['etq'] ); ?></p>
						<h2 class="h-l"><?php tor_e_titular( $tor_b['h2'] ); ?></h2>
						<a class="btn btn-borde btn-sm" href="<?php echo esc_url( $tor_wa ); ?>" target="_blank" rel="noopener" style="align-self:flex-start;margin-top:8px;">¿Otra pregunta? Escríbenos</a>
					</div>
					<div class="tor-faq">
						<?php foreach ( $tor_b['items'] as $tor_i => $tor_q ) : ?>
							<details<?php echo 0 === $tor_i ? ' open' : ''; ?>><summary><?php echo esc_html( $tor_q[0] ); ?><span aria-hidden="true"></span></summary><p><?php echo esc_html( $tor_q[1] ); ?></p></details>
						<?php endforeach; ?>
					</div>
				</div></section>
				<?php
				break;

			case 'trabajos':
				if ( ! empty( $tor_b['ids'] ) ) {
					$tor_lista = array_values( array_filter( $tor_todos, static fn( $x ) => in_array( $x['id'], $tor_b['ids'], true ) ) );
				} else {
					$tor_lista = array_values( array_filter( $tor_todos, static fn( $x ) => (bool) array_intersect( $x['cats'], $tor_b['cats'] ) ) );
				}
				$tor_lista = array_slice( $tor_lista, 0, 3 );
				if ( ! $tor_lista ) {
					break;
				}
				$tor_btn = '<a class="btn btn-borde btn-sm" href="' . esc_url( ! empty( $tor_b['cats'] ) ? tor_cat_url( $tor_b['cats'][0] ) : tor_url( 'trabajo' ) ) . '">Ver todo el trabajo</a>';
				if ( ! empty( $tor_b['lab'] ) ) {
					$tor_btn .= '<a class="btn btn-tinta btn-sm" href="' . esc_url( tor_url( 'demo-lab' ) ) . '">Probar en el Demo Lab</a>';
				}
				$tor_tams = array( 1 => array( 'ancha' ), 2 => array( 'media', 'media' ), 3 => array( 'ancha', '', '' ) );
				$tor_tams = $tor_tams[ count( $tor_lista ) ];
				?>
				<section><div class="wrap">
					<?php $tor_cabeza( $tor_b, '<div style="display:flex;gap:10px;flex-wrap:wrap;">' . $tor_btn . '</div>' ); ?>
					<div class="obras tor-obras"><?php foreach ( $tor_lista as $tor_i => $tor_x ) { echo wp_kses( tor_obra( $tor_x, $tor_tams[ $tor_i ] ), $tor_kt ); } ?></div>
				</div></section>
				<?php
				break;
		}
	endforeach;
	?>

	<section style="padding-bottom: 0;"><div class="wrap">
		<?php get_template_part( 'parts/logos' ); ?>
	</div></section>

	<section class="tor-otros-sec"><div class="wrap">
		<p class="etq" style="margin-bottom: 20px;">Otros servicios</p>
		<div class="tor-otros">
			<?php
			foreach ( tor_lista_servicios() as $tor_slug => $tor_o ) :
				if ( $tor_slug === $tor_s['slug'] ) {
					continue;
				}
				$tor_op = tor_proyecto( $tor_o['ref'] );
				?>
				<a href="<?php echo esc_url( $tor_o['url'] ); ?>" data-luz>
					<?php if ( $tor_op && empty( $tor_op['tipo'] ) ) : ?><img src="<?php echo esc_url( $tor_op['img'] ); ?>" alt="" loading="lazy"><?php endif; ?>
					<span class="dot o-<?php echo esc_attr( $tor_o['o'] ); ?>"></span><b><?php echo esc_html( $tor_o['name'] ); ?></b><?php tor_e_icono( 'flecha' ); ?>
				</a>
			<?php endforeach; ?>
		</div>
	</div></section>

	<section style="padding-top: 0;"><div class="wrap">
		<?php get_template_part( 'parts/cartel-contacto' ); ?>
	</div></section>
</div>
<?php
get_footer();
