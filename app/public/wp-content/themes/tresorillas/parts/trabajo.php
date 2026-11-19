<?php
/**
 * Trabajo: categorías, búsqueda, cuadrícula y lista. El JS reconstruye la cuadrícula con animación.
 *
 * @package TresOrillas
 */

defined( 'ABSPATH' ) || exit;

$tor_cats = tor_core_activo() ? TOR_Proyectos::categorias() : array();
$tor_ok   = array_column( $tor_cats, 0 );
$tor_wf   = 'todo';
if ( is_tax( 'tor_categoria' ) ) {
	$tor_wf = (string) get_queried_object()->slug;
} elseif ( isset( $_GET['cat'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- filtro público de solo lectura.
	$tor_wf = sanitize_key( wp_unslash( $_GET['cat'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
}
if ( ! in_array( $tor_wf, $tor_ok, true ) ) {
	$tor_wf = 'todo';
}
$tor_desc  = '';
foreach ( $tor_cats as $tor_c ) {
	if ( $tor_c[0] === $tor_wf ) {
		$tor_desc = $tor_c[2];
	}
}
$tor_lista = array_values( array_filter( tor_core_activo() ? TOR_Proyectos::datos() : array(), static fn( $p ) => 'todo' === $tor_wf || in_array( $tor_wf, $p['cats'], true ) ) );
$tor_pat   = array( 'ancha', '', '', '', 'media', 'media' );
?>
<div class="cab" style="padding-bottom: 28px;"><div class="wrap">
	<div class="migas"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Inicio</a><span>/</span><span>Trabajo</span></div>
	<div style="display:flex;justify-content:space-between;align-items:end;gap:32px;flex-wrap:wrap;">
		<h1 class="h-xl" id="h-trabajo">Trabajo que <em>habla</em><br>por nosotros.</h1>
		<p class="body" id="catDesc" style="max-width:40ch;"><?php echo esc_html( $tor_desc ); ?></p>
	</div>
</div></div>
<div class="cat-bar" id="catBar" data-inicial="<?php echo esc_attr( $tor_wf ); ?>">
	<div class="wrap cat-inner">
		<div class="cats" id="cats" role="tablist" aria-label="Categorías"></div>
		<div class="cat-tools">
			<label class="buscar" for="buscar"><?php tor_e_icono( 'buscar' ); ?><input id="buscar" placeholder="Buscar proyecto o cliente" autocomplete="off"></label>
			<div class="vistas" role="group" aria-label="Vista"><button id="vGrid" aria-pressed="true" aria-label="Ver en cuadrícula"><?php tor_e_icono( 'grid' ); ?></button><button id="vLista" aria-pressed="false" aria-label="Ver en lista"><?php tor_e_icono( 'lista' ); ?></button></div>
		</div>
	</div>
</div>
<section style="padding-top: 36px;"><div class="wrap">
	<p class="small tenue" id="conteo" aria-live="polite" style="margin-bottom:20px;"><?php echo esc_html( 1 === count( $tor_lista ) ? '1 proyecto' : count( $tor_lista ) . ' proyectos' ); ?></p>
	<div class="obras" id="obras">
		<?php
		foreach ( $tor_lista as $tor_i => $tor_p ) {
			echo wp_kses( tor_obra( $tor_p, count( $tor_lista ) > 2 ? $tor_pat[ $tor_i % 6 ] : 'media' ), tor_kses_tarjeta() );
		}
		?>
	</div>
	<div class="lista-obras" id="listaObras" hidden></div>
	<div class="vacio" id="vacio"<?php echo $tor_lista ? ' hidden' : ''; ?>><h3 class="h-m">Nada con ese nombre todavía.</h3><p class="body">Prueba otra palabra o cuéntanos tu idea y te mostramos algo parecido.</p><a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn-tinta" style="align-self:flex-start;">Describir mi idea</a></div>
</div></section>
<div class="flota" id="flota" aria-hidden="true"><img alt=""></div>
