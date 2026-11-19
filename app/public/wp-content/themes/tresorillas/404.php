<?php
/**
 * Página no encontrada.
 *
 * @package TresOrillas
 */

defined( 'ABSPATH' ) || exit;
get_header();
?>
<div class="cab"><div class="wrap">
	<p class="etq">Error 404</p>
	<h1 class="h-xl">Esta orilla <em>no existe.</em></h1>
	<p class="body">La página que buscas se movió o nunca estuvo aquí.</p>
	<div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:8px;"><a class="btn btn-tinta" href="<?php echo esc_url( home_url( '/' ) ); ?>">Ir al inicio</a><a class="btn btn-borde" href="<?php echo esc_url( tor_url( 'trabajo' ) ); ?>">Ver el trabajo</a></div>
</div></div>
<?php
get_footer();
