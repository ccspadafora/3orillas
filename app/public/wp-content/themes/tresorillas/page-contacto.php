<?php
/**
 * Contacto.
 *
 * @package TresOrillas
 */

defined( 'ABSPATH' ) || exit;
get_header();
$tor_kf = array(
	'label' => array( 'class' => true, 'for' => true ),
	'input' => array( 'type' => true, 'id' => true, 'name' => true, 'value' => true, 'required' => true, 'tabindex' => true, 'autocomplete' => true ),
	'span'  => array( 'class' => true, 'aria-hidden' => true ),
	'a'     => array( 'href' => true, 'target' => true, 'rel' => true ),
);
?>
<div class="cab"><div class="wrap">
	<div class="migas"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Inicio</a><span>/</span><span>Contacto</span></div>
	<h1 class="h-xl" id="h-contacto">Hablemos de <em>tu marca.</em></h1>
</div></div>
<section style="padding-top: 8px;"><div class="wrap tor-contacto" style="display:grid;grid-template-columns:minmax(0,5fr) minmax(0,7fr);gap:56px;align-items:start;">
	<div style="display:flex;flex-direction:column;gap:10px;">
		<?php get_template_part( 'parts/opciones-contacto' ); ?>
		<p class="tenue small" style="margin-top:8px;"><?php echo esc_html( tor_horario_txt() ); ?></p>
	</div>
	<form class="form" id="formContacto" data-origen="contacto" novalidate>
		<label class="campo" for="fNom">Nombre<input class="input" id="fNom" name="nombre" required autocomplete="name" maxlength="120"></label>
		<label class="campo" for="fTel">WhatsApp<input class="input" id="fTel" name="telefono" type="tel" required autocomplete="tel" maxlength="40"></label>
		<label class="campo full" for="fLin">¿Qué necesitas?<select class="input" id="fLin" name="linea"><?php foreach ( tor_necesidades() as $tor_n ) { echo '<option>' . esc_html( $tor_n ) . '</option>'; } ?></select></label>
		<label class="campo full" for="fMsg">Cuéntanos tu idea<textarea class="input" id="fMsg" name="mensaje" rows="5" maxlength="3000"></textarea></label>
		<div class="full"><?php echo wp_kses( tor_acepto( 'fOk' ), $tor_kf ); ?></div>
		<?php echo wp_kses( tor_trampa(), $tor_kf ); ?>
		<div class="full"><button class="btn btn-tinta" type="submit">Enviar mensaje</button></div>
		<p class="ok full" id="okContacto" role="status"></p>
	</form>
</div></section>
<?php
get_footer();
