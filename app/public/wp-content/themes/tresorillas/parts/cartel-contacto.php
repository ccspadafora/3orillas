<?php
/**
 * Cartel "¿Lo hablamos ahora?" del inicio.
 *
 * @package TresOrillas
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="cartel">
	<div class="cartel-foto"><img src="<?php echo esc_url( tor_m( 'retrato-2.jpg' ) ); ?>" alt="Retrato en blanco y negro" loading="lazy"><p class="lema">Tu marca,<br><em>en las mejores manos.</em></p></div>
	<div class="cartel-txt">
		<p class="etq" style="display:flex;align-items:center;gap:10px;"><span class="live" data-live></span><span data-estado="cartel">Te respondemos por WhatsApp</span></p>
		<h2 class="h-m">¿Lo hablamos ahora?</h2>
		<p class="body">Un asesor se conecta contigo por Zoom, comparte su pantalla y te muestra proyectos parecidos al tuyo.</p>
		<div style="display:flex;flex-direction:column;gap:10px;">
			<?php get_template_part( 'parts/opciones-contacto', null, array( 'flechas' => true ) ); ?>
		</div>
	</div>
</div>
