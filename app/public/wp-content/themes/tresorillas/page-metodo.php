<?php
/**
 * Método.
 *
 * @package TresOrillas
 */

defined( 'ABSPATH' ) || exit;
get_header();
?>
<div class="cab"><div class="wrap">
	<div class="migas"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Inicio</a><span>/</span><span>Método</span></div>
	<h1 class="h-xl" id="h-metodo">Primero entender.<br><em>Después proponer.</em></h1>
	<p class="body">Ninguna táctica sale sin diagnóstico. Pagas por fases y en cada una ves resultados concretos.</p>
</div></div>
<section style="padding-top: 0;"><div class="wrap">
	<div class="fases" id="fasesMetodo"><?php foreach ( tor_fases() as $tor_i => $tor_f ) { echo wp_kses_post( tor_fase( $tor_f, $tor_i ) ); } ?></div>
	<div class="cartel" style="margin-top: 112px;"><div class="cartel-foto"><img src="<?php echo esc_url( tor_m( 'retrato-1.jpg' ) ); ?>" alt="Retrato en blanco y negro" loading="lazy"></div><div class="cartel-txt"><p class="etq">Para quién trabajamos</p><h2 class="h-m">Empresas con un producto que funciona y ganas de <em>crecer con orden.</em></h2><p class="body">Nos buscan cuando la marca no comunica lo que vale, cuando las ventas dependen de la recomendación o cuando la operación se hace a mano.</p><a href="<?php echo esc_url( tor_url( 'contacto' ) ); ?>" class="btn btn-tinta" style="align-self:flex-start;">Agendar diagnóstico</a></div></div>
</div></section>
<?php
get_footer();
