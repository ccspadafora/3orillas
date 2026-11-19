<?php
/**
 * Escenario oscuro de las páginas internas: panel obsidiana con textura, órbita, luz que sigue el puntero,
 * onda champán, ventana con el proyecto (o retrato B/N) y tarjetas de cristal que flotan. Abajo, una cinta en movimiento.
 *
 * Argumentos ($args):
 *  - migas     array [ [texto, url], ..., [texto] ]
 *  - o         'e'|'c'|'t' color de la orilla
 *  - tag       texto de la etiqueta de orilla (opcional)
 *  - eyebrow   texto
 *  - h1        titular (admite <em> y <br>)
 *  - intro     texto
 *  - acciones  [ [texto, url|'', tipo: 'marfil'|'borde'|'llamar', externo bool] ]
 *  - vivo      bool: muestra el estado de respuesta según el horario
 *  - proyecto  array de proyecto (TOR_Proyectos::datos) o null
 *  - ciclo     lista de [proyecto, nombre del servicio, url] para alternar en la ventana (índice de Servicios)
 *  - retrato   archivo de assets/media para la variante con retrato B/N
 *  - flotantes [ [valor, etiqueta], ... ] tarjetas de cristal
 *  - cinta     lista de textos de la cinta inferior
 *
 * @package TresOrillas
 */

defined( 'ABSPATH' ) || exit;

$a = wp_parse_args(
	$args ?? array(),
	array(
		'migas'     => array(),
		'o'         => 'e',
		'tag'       => '',
		'eyebrow'   => '',
		'h1'        => '',
		'intro'     => '',
		'acciones'  => array(),
		'vivo'      => false,
		'proyecto'  => null,
		'ciclo'     => array(),
		'retrato'   => '',
		'flotantes' => array(),
		'cinta'     => array(),
	)
);

$tor_luz = array(
	'e' => 'rgba(201,179,138,.55)',
	'c' => 'rgba(169,194,160,.5)',
	't' => 'rgba(150,165,250,.5)',
);
$tor_col = array(
	'e' => 'var(--champan)',
	'c' => 'var(--salvia)',
	't' => 'var(--hielo)',
);

/* Cifra: la parte numérica rueda; el resto queda fijo. */
$tor_cifra = static function ( string $v ): string {
	if ( preg_match( '/^([+$]*[\d][\d.,]*[M×%]?)(.*)$/u', $v, $m ) ) {
		return '<span class="roll" data-to="' . esc_attr( $m[1] ) . '">' . esc_html( $m[1] ) . '</span>' . esc_html( $m[2] );
	}
	return esc_html( $v );
};
$tor_pos = array(
	array( 'left: -4%; top: 10%; --d: 16px;' ),
	array( 'right: -2%; top: 60%; --d: 24px;' ),
	array( 'right: 24%; bottom: -3%; --d: 12px;' ),
);
$tor_p = $a['proyecto'];
if ( ! $tor_p && $a['ciclo'] ) {
	$tor_p = $a['ciclo'][0][0];
}
?>
<section class="tor-escena grano" data-escena style="--o: <?php echo esc_attr( $tor_col[ $a['o'] ] ?? $tor_col['e'] ); ?>; --luz: <?php echo esc_attr( $tor_luz[ $a['o'] ] ?? $tor_luz['e'] ); ?>;">
	<div class="tor-escena-panel">
		<span class="tor-luz" aria-hidden="true"></span>
		<svg class="tor-escena-onda" width="1200" height="700" viewBox="0 0 1200 700" aria-hidden="true"><path d="M-20 560 C 180 180, 460 160, 560 380 S 880 700, 1220 260" fill="none" stroke="#C9B38A" stroke-width="1.2"/><path d="M-20 620 C 180 240, 460 220, 560 440 S 880 760, 1220 320" fill="none" stroke="#C9B38A" stroke-width="1.2" opacity=".45"/></svg>

		<div class="wrap tor-escena-grid">
			<div class="tor-escena-txt">
				<?php if ( $a['migas'] ) : ?>
					<div class="migas">
						<?php
						$tor_n = count( $a['migas'] );
						foreach ( $a['migas'] as $tor_i => $tor_m ) {
							if ( ! empty( $tor_m[1] ) ) {
								echo '<a href="' . esc_url( $tor_m[1] ) . '">' . esc_html( $tor_m[0] ) . '</a>';
							} else {
								echo '<span>' . esc_html( $tor_m[0] ) . '</span>';
							}
							if ( $tor_i < $tor_n - 1 ) {
								echo '<span>/</span>';
							}
						}
						?>
					</div>
				<?php endif; ?>
				<?php if ( $a['tag'] ) : ?><span class="tor-tag cristal-oscuro"><span class="dot" style="background: var(--o);"></span><?php echo esc_html( $a['tag'] ); ?></span><?php endif; ?>
				<?php if ( $a['eyebrow'] ) : ?><p class="etq tor-eyebrow"><?php echo esc_html( $a['eyebrow'] ); ?></p><?php endif; ?>
				<h1 class="h-xl tor-escena-h1"><?php tor_e_titular( $a['h1'] ); ?></h1>
				<?php if ( $a['intro'] ) : ?><p class="body"><?php echo esc_html( $a['intro'] ); ?></p><?php endif; ?>
				<?php if ( $a['acciones'] ) : ?>
					<div class="tor-acciones">
						<?php
						foreach ( $a['acciones'] as $tor_ac ) {
							$tor_cls = 'btn tor-iman ' . ( 'marfil' === ( $tor_ac[2] ?? '' ) ? 'btn-marfil' : 'btn-borde' );
							if ( 'llamar' === ( $tor_ac[2] ?? '' ) ) {
								echo '<button class="' . esc_attr( $tor_cls ) . '" type="button" data-llamar>' . esc_html( $tor_ac[0] ) . '</button>';
							} else {
								$tor_ext = ! empty( $tor_ac[3] ) ? ' target="_blank" rel="noopener"' : '';
								echo '<a class="' . esc_attr( $tor_cls ) . '" href="' . esc_url( $tor_ac[1] ) . '"' . $tor_ext . '>' . esc_html( $tor_ac[0] ) . ( 'marfil' === ( $tor_ac[2] ?? '' ) ? tor_icono( 'flecha' ) : '' ) . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- atributos escapados; el ícono es SVG fijo.
							}
						}
						?>
					</div>
				<?php endif; ?>
				<?php if ( $a['vivo'] ) : ?><p class="small tor-quince"><span class="live" data-live></span><span data-estado="quince">Te respondemos por WhatsApp</span></p><?php endif; ?>
			</div>

			<div class="tor-escena-scene<?php echo ( $a['retrato'] && $tor_p ) ? ' tor-escena-mix' : ''; ?>">
				<?php if ( ! ( $a['retrato'] && $tor_p ) ) : ?>
				<svg class="tor-orbita" viewBox="0 0 200 200" aria-hidden="true"><circle cx="100" cy="100" r="99"/><circle cx="100" cy="100" r="72" style="stroke-dasharray:2 4;"/><g class="tor-gira"><circle class="v" r="2.4" cx="100" cy="1"/></g><g class="tor-gira lento"><circle class="v" r="1.8" cx="100" cy="28" style="fill: var(--o);"/></g></svg>
				<?php endif; ?>

				<?php if ( $a['retrato'] ) : ?>
					<div class="tor-escena-retrato"><img src="<?php echo esc_url( tor_m( $a['retrato'] ) ); ?>" alt="Retrato en blanco y negro" fetchpriority="high"></div>
				<?php endif; ?>
				<?php if ( $tor_p ) : ?>
					<a class="tor-ventana" href="<?php echo esc_url( $a['ciclo'] ? $a['ciclo'][0][2] : $tor_p['url'] ); ?>" data-cursor="<?php echo esc_attr( $a['ciclo'] ? 'Ver servicio' : 'Ver caso' ); ?>"<?php echo $a['ciclo'] ? ' data-ciclo="' . esc_attr( (string) wp_json_encode( array_map( static fn( $c ) => array( 'v' => $c[0]['video'], 'i' => $c[0]['img'], 'p' => $c[0]['name'], 's' => $c[1], 'u' => $c[2] ), $a['ciclo'] ) ) ) . '"' : ''; ?>>
						<div class="navegador">
							<div class="barra"><i></i><i></i><i></i><span class="tor-ventana-url"><?php echo esc_html( '3orillas.com/' . ( $tor_p['demo'] ?: $tor_p['id'] ) ); ?></span></div>
							<div class="tor-ventana-media">
								<?php if ( ! empty( $tor_p['tipo'] ) ) : ?>
									<div class="tipo" style="background:<?php echo esc_attr( $tor_p['tipo'][0] ); ?>;color:<?php echo esc_attr( $tor_p['tipo'][1] ); ?>;"><span><?php echo esc_html( $tor_p['client'] ); ?></span><b><?php echo esc_html( $tor_p['name'] ); ?></b><span><?php echo esc_html( $tor_p['kind'] ); ?></span></div>
								<?php elseif ( $tor_p['video'] && ! $tor_p['mobile'] ) : ?>
									<video src="<?php echo esc_url( $tor_p['video'] ); ?>" poster="<?php echo esc_url( $tor_p['img'] ); ?>" autoplay muted loop playsinline aria-label="<?php echo esc_attr( 'Video de ' . $tor_p['name'] ); ?>"></video>
								<?php else : ?>
									<img src="<?php echo esc_url( $tor_p['img'] ); ?>" alt="<?php echo esc_attr( $tor_p['name'] . ': ' . $tor_p['kind'] ); ?>" fetchpriority="high">
								<?php endif; ?>
							</div>
						</div>
						<span class="tor-ventana-pie cristal-oscuro"><span class="small" data-ciclo-s><?php echo esc_html( $a['ciclo'] ? $a['ciclo'][0][1] : 'Proyecto real' ); ?></span><b data-ciclo-p><?php echo esc_html( $tor_p['name'] ); ?></b><?php if ( $a['ciclo'] ) : ?><span class="tor-ciclo-bar"><i></i></span><?php endif; ?></span>
					</a>
				<?php endif; ?>

				<?php
				$tor_mix = $a['retrato'] && $tor_p;
				foreach ( array_slice( $a['flotantes'], 0, $tor_mix ? 1 : 3 ) as $tor_i => $tor_f ) :
					$tor_tag = ! empty( $tor_f[2] ) ? 'a' : 'div';
					?>
					<<?php echo esc_attr( $tor_tag ); ?> class="card cristal tor-flota<?php echo ! empty( $tor_f[2] ) ? ' tor-flota-ir' : ''; ?>" style="<?php echo esc_attr( $tor_pos[ $tor_i ][0] ); ?>"<?php echo ! empty( $tor_f[2] ) ? ' href="' . esc_url( $tor_f[2] ) . '"' : ''; ?>>
						<b><?php echo $tor_cifra( (string) $tor_f[0] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escapado en $tor_cifra. ?></b>
						<span><?php echo esc_html( $tor_f[1] ); ?></span>
						<?php if ( ! empty( $tor_f[2] ) ) : ?><span class="tor-flota-cta">Empezar <?php tor_e_icono( 'ir' ); ?></span><?php endif; ?>
					</<?php echo esc_attr( $tor_tag ); ?>>
				<?php endforeach; ?>

				<?php if ( ! ( $a['retrato'] && $tor_p ) ) : ?>
				<div class="sello tor-sello" aria-hidden="true">
					<svg viewBox="0 0 120 120" width="104" height="104"><defs><path id="torCirc" d="M60,60 m-44,0 a44,44 0 1,1 88,0 a44,44 0 1,1 -88,0"/></defs><circle cx="60" cy="60" r="58" fill="rgba(255,255,255,.06)" stroke="rgba(255,255,255,.22)"/><text font-family="Inter Tight, sans-serif" font-size="9.5" letter-spacing="2.2" fill="#F5F2EC"><textPath href="#torCirc">3 ORILLAS STUDIO · BOGOTÁ · COLOMBIA ·</textPath></text></svg>
					<svg viewBox="0 0 24 24" width="20" height="20" style="position:absolute;left:42px;top:42px;animation:none;" aria-hidden="true"><path d="M12 2l1.8 8.2L22 12l-8.2 1.8L12 22l-1.8-8.2L2 12l8.2-1.8z" fill="#C9B38A"/></svg>
				</div>
				<?php endif; ?>
			</div>
		</div>

		<?php if ( $a['cinta'] ) : ?>
			<div class="tor-cinta" aria-hidden="true"><div class="tor-cinta-pista">
				<?php for ( $tor_r = 0; $tor_r < 2; $tor_r++ ) : ?>
					<span class="tor-cinta-grupo"><?php foreach ( $a['cinta'] as $tor_c ) { echo '<span>' . esc_html( $tor_c ) . '</span><i></i>'; } ?></span>
				<?php endfor; ?>
			</div></div>
		<?php endif; ?>
	</div>
</section>
