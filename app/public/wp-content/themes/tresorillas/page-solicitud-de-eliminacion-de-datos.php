<?php
/**
 * Solicitud de eliminación de datos (misma URL del sitio anterior; la usa la app de Meta).
 * Texto editable en el admin; el formulario guarda la solicitud en Contactos y avisa por correo.
 *
 * @package TresOrillas
 */

defined( 'ABSPATH' ) || exit;
get_header();
$tor_kf = array(
	'label' => array( 'class' => true, 'for' => true ),
	'input' => array( 'type' => true, 'id' => true, 'name' => true, 'value' => true, 'required' => true, 'tabindex' => true, 'autocomplete' => true ),
	'span'  => array( 'class' => true, 'aria-hidden' => true ),
);
while ( have_posts() ) :
	the_post();
	?>
	<div class="cab"><div class="wrap">
		<div class="migas"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Inicio</a><span>/</span><span><?php the_title(); ?></span></div>
		<h1 class="h-l"><?php the_title(); ?></h1>
		<p class="body">Respondemos en máximo 10 días hábiles y te confirmamos por el mismo medio.</p>
	</div></div>
	<section style="padding-top: 8px;"><div class="wrap tor-contacto tor-legal">
		<div class="texto"><?php the_content(); ?></div>
		<form class="form tor-form-datos cristal" id="formDatos" novalidate>
			<p class="etq full">Formulario de solicitud</p>
			<label class="campo full" for="dNom">Nombre completo<input class="input" id="dNom" name="nombre" required autocomplete="name" maxlength="120"></label>
			<label class="campo" for="dCor">Correo<input class="input" id="dCor" name="correo" type="email" autocomplete="email" maxlength="120"></label>
			<label class="campo" for="dTel">Teléfono (opcional)<input class="input" id="dTel" name="telefono" type="tel" autocomplete="tel" maxlength="40"></label>
			<label class="campo full" for="dPla">Plataforma desde la que interactuaste<select class="input" id="dPla" name="plataforma"><?php foreach ( array( 'Facebook', 'Instagram', 'Messenger', 'WhatsApp', 'Este sitio web', 'Otra' ) as $tor_o ) { echo '<option>' . esc_html( $tor_o ) . '</option>'; } ?></select></label>
			<label class="campo full" for="dMsg">¿Qué datos quieres eliminar o corregir?<textarea class="input" id="dMsg" name="mensaje" rows="4" maxlength="3000" required></textarea></label>
			<label class="acepto full" for="dOk"><input type="checkbox" id="dOk" name="consent" value="1" required><span>Autorizo usar estos datos solo para tramitar mi solicitud.</span></label>
			<?php echo wp_kses( tor_trampa(), $tor_kf ); ?>
			<div class="full"><button class="btn btn-tinta" type="submit">Enviar solicitud</button></div>
			<p class="ok full" id="okDatos" role="status"></p>
		</form>
	</div></section>
	<?php
endwhile;
get_footer();
