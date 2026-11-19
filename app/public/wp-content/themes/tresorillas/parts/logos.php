<?php
/**
 * Cinta de logos de clientes. Argumentos: titulo (texto), oscuro (bool).
 *
 * @package TresOrillas
 */

defined( 'ABSPATH' ) || exit;
$tor_a = wp_parse_args( $args ?? array(), array( 'titulo' => 'Marcas que han confiado en nosotros', 'oscuro' => false ) );
$tor_l = tor_logos();
?>
<div class="tor-logos<?php echo $tor_a['oscuro'] ? ' oscuro' : ''; ?>">
	<?php if ( $tor_a['titulo'] ) : ?><p class="etq"><?php echo esc_html( $tor_a['titulo'] ); ?></p><?php endif; ?>
	<div class="tor-logos-cinta" role="list" aria-label="Clientes">
		<div class="tor-logos-pista">
			<?php for ( $tor_r = 0; $tor_r < 2; $tor_r++ ) : ?>
				<div class="tor-logos-grupo"<?php echo $tor_r ? ' aria-hidden="true"' : ''; ?>>
					<?php foreach ( $tor_l as $tor_x ) : ?>
						<span role="listitem" class="tor-logo tor-logo-<?php echo esc_attr( $tor_x[0] ); ?>"><img src="<?php echo esc_url( tor_m( 'clientes/' . $tor_x[0] . '.png' ) ); ?>" alt="<?php echo $tor_r ? '' : esc_attr( $tor_x[1] ); ?>" loading="lazy"></span>
					<?php endforeach; ?>
				</div>
			<?php endfor; ?>
		</div>
	</div>
</div>
